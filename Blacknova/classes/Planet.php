<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: classes/Planet.php
//
// TODO: These are horribly bad. They should be broken out of classes, and turned mostly into template
// behaviors. But in the interest of saying goodbye to the includes directory, and raw functions, this
// will at least allow us to auto-load and use classes instead. Plenty to do in the future, though!

namespace Bad;

class Planet
{
    public function getOwner($db = null, $planet_id = null, &$owner_info = null)
    {
        $owner_info = null;
        if (!is_null($planet_id) && is_numeric($planet_id) && $planet_id > 0)
        {
            $sql  = "SELECT ship_id, character_name, team FROM {$db->prefix}planets ";
            $sql .= "LEFT JOIN {$db->prefix}ships ON {$db->prefix}ships.ship_id = {$db->prefix}planets.owner ";
            $sql .= "WHERE {$db->prefix}planets.planet_id=?;";
            $res = $db->Execute($sql, array ($planet_id));
            \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
            if ($res->RecordCount() > 0)
            {
                $owner_info = (array) $res->fields;
                return true;
            }
        }
        return false;
    }

    public static function planetBombing($db, $langvars)
    {
        global $playerinfo, $ownerinfo, $planetinfo, $planetbeams, $planetfighters, $attackerfighters;
        global $planettorps, $torp_dmg_rate;

        if ($playerinfo['turns'] < 1)
        {
            echo $langvars['l_cmb_atleastoneturn'] . "<br><br>";
            \Bnt\Text::gotoMain($db, $lang, $langvars);
            Footer::display($pdo_db, $lang, $bntreg, $template);
            die();
        }

        echo $langvars['l_bombsaway'] . "<br><br>\n";
        $attackerfighterslost = 0;
        $planetfighterslost = 0;
        $attackerfightercapacity = \Bnt\CalcLevels::fighters($playerinfo['computer'], $level_factor);
        $ownerfightercapacity = \Bnt\CalcLevels::fighters($ownerinfo['computer'], $level_factor);
        $beamsused = 0;

        $res = $db->Execute("LOCK TABLES {$db->prefix}ships WRITE, {$db->prefix}planets WRITE");
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);

        $planettorps = \Bnt\CalcLevels::planetTorps($db, $ownerinfo, $planetinfo, $base_defense, $level_factor);
        $planetbeams = \Bnt\CalcLevels::planetBeams($db, $ownerinfo, $base_defense, $planetinfo);

        $planetfighters = $planetinfo['fighters'];
        $attackerfighters = $playerinfo['ship_fighters'];

        if ($ownerfightercapacity / $attackerfightercapacity < 1)
        {
            echo $langvars['l_bigfigs'] . "<br><br>\n";
        }

        if ($planetbeams <= $attackerfighters)
        {
            $attackerfighterslost = $planetbeams;
            $beamsused = $planetbeams;
        }
        else
        {
            $attackerfighterslost = $attackerfighters;
            $beamsused = $attackerfighters;
        }

        if ($attackerfighters <= $attackerfighterslost)
        {
            echo $langvars['l_bigbeams'] . "<br>\n";
        }
        else
        {
            $attackerfighterslost += $planettorps * $torp_dmg_rate;

            if ($attackerfighters <= $attackerfighterslost)
            {
                echo $langvars['l_bigtorps'] . "<br>\n";
            }
            else
            {
                echo $langvars['l_strafesuccess'] . "<br>\n";
                if ($ownerfightercapacity / $attackerfightercapacity > 1)
                {
                    $planetfighterslost = $attackerfighters - $attackerfighterslost;

                }
                else
                {
                    $planetfighterslost = round(($attackerfighters - $attackerfighterslost) * $ownerfightercapacity / $attackerfightercapacity);
                }
                if ($planetfighterslost > $planetfighters)
                {
                    $planetfighterslost = $planetfighters;
                }
            }
        }

        echo "<br><br>\n";
        \Bnt\PlayerLog::writeLog($db, $ownerinfo['ship_id'], LOG_PLANET_BOMBED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]|$beamsused|$planettorps|$planetfighterslost");
        $res = $db->Execute("UPDATE {$db->prefix}ships SET turns = turns - 1, turns_used = turns_used + 1, ship_fighters = ship_fighters - ? WHERE ship_id = ?", array ($attackerfighters, $playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
        $res = $db->Execute("UPDATE {$db->prefix}planets SET energy=energy - ?, fighters=fighters - ?, torps=torps - ? WHERE planet_id = ?", array ($beamsused, $planetfighterslost, $planettorps, $planetinfo['planet_id']));
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
        $res = $db->Execute("UNLOCK TABLES");
        \Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
    }

    public static function planetCombat($db, $langvars)
    {
        global $playerinfo, $ownerinfo, $planetinfo, $torpedo_price, $colonist_price, $ore_price, $organics_price, $goods_price, $energy_price;
        global $planetbeams, $planetfighters, $planetshields, $planettorps, $attackerbeams, $attackerfighters, $attackershields, $upgrade_factor, $upgrade_cost;
        global $attackertorps, $attackerarmor, $torp_dmg_rate, $level_factor, $attackertorpdamage, $min_value_capture;

        if ($playerinfo['turns'] < 1)
        {
            echo $langvars['l_cmb_atleastoneturn'] . "<br><br>";
            \Bnt\Text::gotoMain($db, $lang, $langvars);
            Footer::display($pdo_db, $lang, $bntreg, $template);
            die();
        }

        // Planetary defense system calculation
        $planetbeams        = \Bnt\CalcLevels::planetBeams($db, $ownerinfo, $base_defense, $planetinfo);
        $planetfighters     = $planetinfo['fighters'];
        $planetshields      = \Bnt\CalcLevels::planetShields($db, $ownerinfo, $base_defense, $planetinfo);
        $planettorps        = \Bnt\CalcLevels::planetTorps($db, $ownerinfo, $planetinfo, $base_defense, $level_factor);

        // Attacking ship calculations

        $attackerbeams      = \Bnt\CalcLevels::beams($playerinfo['beams'], $level_factor);
        $attackerfighters   = $playerinfo['ship_fighters'];
        $attackershields    = \Bnt\CalcLevels::shields($playerinfo['shields'], $level_factor);
        $attackertorps      = round(pow($level_factor, $playerinfo['torp_launchers'])) * 2;
        $attackerarmor      = $playerinfo['armor_pts'];

        // Now modify player beams, shields and torpedos on available materiel
        $bntreg->start_energy = $playerinfo['ship_energy'];

        // Beams
        if ($attackerbeams > $playerinfo['ship_energy'])
        {
            $attackerbeams   = $playerinfo['ship_energy'];
        }
        $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $attackerbeams;

        // Shields
        if ($attackershields > $playerinfo['ship_energy'])
        {
            $attackershields = $playerinfo['ship_energy'];
        }
        $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $attackershields;

        // Torpedos
        if ($attackertorps > $playerinfo['torps'])
        {
            $attackertorps = $playerinfo['torps'];
        }
        $playerinfo['torps'] = $playerinfo['torps'] - $attackertorps;

        // Setup torp damage rate for both Planet and Ship
        $planettorpdamage   = $torp_dmg_rate * $planettorps;
        $attackertorpdamage = $torp_dmg_rate * $attackertorps;

        echo "
        <center>
        <hr>
        <table width='75%' border='0'>
        <tr align='center'>
        <td width='9%' height='27'></td>
        <td width='12%' height='27'><font color='white'>" . $langvars['l_cmb_beams'] . "</font></td>
        <td width='17%' height='27'><font color='white'>" . $langvars['l_cmb_fighters'] . "</font></td>
        <td width='18%' height='27'><font color='white'>" . $langvars['l_cmb_shields'] . "</font></td>
        <td width='11%' height='27'><font color='white'>" . $langvars['l_cmb_torps'] . "</font></td>
        <td width='22%' height='27'><font color='white'>" . $langvars['l_cmb_torpdamage'] . "</font></td>
        <td width='11%' height='27'><font color='white'>" . $langvars['l_cmb_armor'] . "</font></td>
        </tr>
        <tr align='center'>
        <td width='9%'> <font color='red'>" . $langvars['l_cmb_you'] . "</td>
        <td width='12%'><font color='red'><strong>" . $attackerbeams . "</strong></font></td>
        <td width='17%'><font color='red'><strong>" . $attackerfighters . "</strong></font></td>
        <td width='18%'><font color='red'><strong>" . $attackershields . "</strong></font></td>
        <td width='11%'><font color='red'><strong>" . $attackertorps . "</strong></font></td>
        <td width='22%'><font color='red'><strong>" . $attackertorpdamage . "</strong></font></td>
        <td width='11%'><font color='red'><strong>" . $attackerarmor . "</strong></font></td>
        </tr>
        <tr align='center'>
        <td width='9%'> <font color='#6098F8'>" . $langvars['l_cmb_planet'] . "</font></td>
        <td width='12%'><font color='#6098F8'><strong>" . $planetbeams . "</strong></font></td>
        <td width='17%'><font color='#6098F8'><strong>" . $planetfighters . "</strong></font></td>
        <td width='18%'><font color='#6098F8'><strong>" . $planetshields . "</strong></font></td>
        <td width='11%'><font color='#6098F8'><strong>" . $planettorps . "</strong></font></td>
        <td width='22%'><font color='#6098F8'><strong>" . $planettorpdamage . "</strong></font></td>
        <td width='11%'><font color='#6098F8'><strong>N/A</strong></font></td>
        </tr>
        </table>
        <hr>
        </center>
        ";

        // Begin actual combat calculations

        $planetdestroyed   = 0;
        $attackerdestroyed = 0;

        echo "<br><center><strong><font size='+2'>" . $langvars['l_cmb_combatflow'] . "</font></strong><br><br>\n";
        echo "<table width='75%' border='0'><tr align='center'><td><font color='red'>" . $langvars['l_cmb_you'] . "</font></td><td><font color='#6098F8'>" . $langvars['l_cmb_defender'] . "</font></td>\n";
        echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_attackingplanet'] . " " . $playerinfo['sector'] . "</strong></font></td><td></td>";
        echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_youfireyourbeams'] . "</strong></font></td><td></td>\n";
        if ($planetfighters > 0 && $attackerbeams > 0)
        {
            if ($attackerbeams > $planetfighters)
            {
                $langvars['l_cmb_defenselost'] = str_replace("[cmb_planetfighters]", $planetfighters, $langvars['l_cmb_defenselost']);
                echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>" . $langvars['l_cmb_defenselost'] . "</strong></font>";
                $attackerbeams = $attackerbeams - $planetfighters;
                $planetfighters = 0;
            }
            else
            {
                $langvars['l_cmb_defenselost2'] = str_replace("[cmb_attackerbeams]", $attackerbeams, $langvars['l_cmb_defenselost2']);
                $planetfighters = $planetfighters - $attackerbeams;
                echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>" . $langvars['l_cmb_defenselost2'] . "</strong></font>";
                $attackerbeams = 0;
            }
        }

        if ($attackerfighters > 0 && $planetbeams > 0)
        {
            // If there are more beams on the planet than attacker has fighters
            if ($planetbeams > round($attackerfighters / 2))
            {
                // Half the attacker fighters
                $temp = round($attackerfighters / 2);
                // Attacker loses half his fighters
                $lost = $attackerfighters - $temp;
                // Set attacker fighters to 1/2 it's original value
                $attackerfighters = $temp;
                // Subtract half the attacker fighters from available planetary beams
                $planetbeams = $planetbeams - $lost;
                $langvars['l_cmb_planetarybeams'] = str_replace("[cmb_temp]", $temp, $langvars['l_cmb_planetarybeams']);
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_planetarybeams']  . "</strong></font><td></td>";
            }
            else
            {
                $langvars['l_cmb_planetarybeams2'] = str_replace("[cmb_planetbeams]", $planetbeams, $langvars['l_cmb_planetarybeams2']);
                $attackerfighters = $attackerfighters - $planetbeams;
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_planetarybeams2'] . "</strong></font><td></td>";
                $planetbeams = 0;
            }
        }
        if ($attackerbeams > 0)
        {
            if ($attackerbeams > $planetshields)
            {
                $attackerbeams = $attackerbeams - $planetshields;
                $planetshields = 0;
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_youdestroyedplanetshields'] . "</font></strong><td></td>";
            }
            else
            {
                $langvars['l_cmb_beamsexhausted'] = str_replace("[cmb_attackerbeams]", $attackerbeams, $langvars['l_cmb_beamsexhausted']);
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_beamsexhausted'] . "</font></strong><td></td>";
                $planetshields = $planetshields - $attackerbeams;
                $attackerbeams = 0;
            }
        }
        if ($planetbeams > 0)
        {
            if ($planetbeams > $attackershields)
            {
                $planetbeams = $planetbeams - $attackershields;
                $attackershields = 0;
                echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>" . $langvars['l_cmb_breachedyourshields'] . "</font></strong></td>";
            }
            else
            {
                $attackershields = $attackershields - $planetbeams;
                $langvars['l_cmb_destroyedyourshields'] = str_replace("[cmb_planetbeams]", $planetbeams, $langvars['l_cmb_destroyedyourshields']);
                echo "<tr align='center'><td></td><font color='#6098F8'><strong>" . $langvars['l_cmb_destroyedyourshields'] . "</font></strong></td>";
                $planetbeams = 0;
            }
        }
        if ($planetbeams > 0)
        {
            if ($planetbeams > $attackerarmor)
            {
                $attackerarmor = 0;
                echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>" . $langvars['l_cmb_breachedyourarmor'] . "</strong></font></td>";
            }
            else
            {
                $attackerarmor = $attackerarmor - $planetbeams;
                $langvars['l_cmb_destroyedyourarmor'] = str_replace("[cmb_planetbeams]", $planetbeams, $langvars['l_cmb_destroyedyourarmor']);
                echo "<tr align='center'><td></td><td><font color='#6098F8'><strong>" . $langvars['l_cmb_destroyedyourarmor'] . "</font></strong></td>";
            }
        }
        echo "<tr align='center'><td><font color='YELLOW'><strong>" . $langvars['l_cmb_torpedoexchangephase'] . "</strong></font></td><td><strong><font color='YELLOW'>" . $langvars['l_cmb_torpedoexchangephase'] . "</strong></font></td><br>";
        if ($planetfighters > 0 && $attackertorpdamage > 0)
        {
            if ($attackertorpdamage > $planetfighters)
            {
                $langvars['l_cmb_nofightersleft'] = str_replace("[cmb_planetfighters]", $planetfighters, $langvars['l_cmb_nofightersleft']);
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_nofightersleft'] . "</font></strong></td><td></td>";
                $attackertorpdamage = $attackertorpdamage - $planetfighters;
                $planetfighters = 0;
            }
            else
            {
                $planetfighters = $planetfighters - $attackertorpdamage;
                $langvars['l_cmb_youdestroyfighters'] = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $langvars['l_cmb_youdestroyfighters']);
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_youdestroyfighters'] . "</font></strong></td><td></td>";
                $attackertorpdamage = 0;
            }
        }
        if ($attackerfighters > 0 && $planettorpdamage > 0)
        {
            if ($planettorpdamage > round($attackerfighters / 2))
            {
                $temp = round($attackerfighters / 2);
                $lost = $attackerfighters - $temp;
                $attackerfighters = $temp;
                $planettorpdamage = $planettorpdamage - $lost;
                $langvars['l_cmb_planettorpsdestroy'] = str_replace("[cmb_temp]", $temp, $langvars['l_cmb_planettorpsdestroy']);
                echo "<tr align='center'><td></td><td><font color='red'><strong>" . $langvars['l_cmb_planettorpsdestroy'] . "</strong></font></td>";
            }
            else
            {
                $attackerfighters = $attackerfighters - $planettorpdamage;
                $langvars['l_cmb_planettorpsdestroy2'] = str_replace("[cmb_planettorpdamage]", $planettorpdamage, $langvars['l_cmb_planettorpsdestroy2']);
                echo "<tr align='center'><td></td><td><font color='red'><strong>" . $langvars['l_cmb_planettorpsdestroy2'] . "</strong></font></td>";
                $planettorpdamage = 0;
            }
        }
        if ($planettorpdamage > 0)
        {
            if ($planettorpdamage > $attackerarmor)
            {
                $attackerarmor = 0;
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_torpsbreachedyourarmor'] . "</strong></font></td><td></td>";
            }
            else
            {
                $attackerarmor = $attackerarmor - $planettorpdamage;
                $langvars['l_cmb_planettorpsdestroy3'] = str_replace("[cmb_planettorpdamage]", $planettorpdamage, $langvars['l_cmb_planettorpsdestroy3']);
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_planettorpsdestroy3'] . "</strong></font></td><td></td>";
            }
        }
        if ($attackertorpdamage > 0 && $planetfighters > 0)
        {
            $planetfighters = $planetfighters - $attackertorpdamage;
            if ($planetfighters < 0)
            {
                $planetfighters = 0;
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_youdestroyedallfighters'] . "</strong></font></td><td></td>";
            }
            else
            {
                $langvars['l_cmb_youdestroyplanetfighters'] = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $langvars['l_cmb_youdestroyplanetfighters']);
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_youdestroyplanetfighters'] . "</strong></font></td><td></td>";
            }
        }
        echo "<tr align='center'><td><font color='YELLOW'><strong>" . $langvars['l_cmb_fightercombatphase'] . "</strong></font></td><td><strong><font color='YELLOW'>" . $langvars['l_cmb_fightercombatphase'] . "</strong></font></td><br>";
        if ($attackerfighters > 0 && $planetfighters > 0)
        {
            if ($attackerfighters > $planetfighters)
            {
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_youdestroyedallfighters2'] . "</strong></font></td><td></td>";
                $tempplanetfighters = 0;
            }
            else
            {
                $langvars['l_cmb_youdestroyplanetfighters2'] = str_replace("[cmb_attackerfighters]", $attackerfighters, $langvars['l_cmb_youdestroyplanetfighters2']);
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_youdestroyplanetfighters2'] . "</strong></font></td><td></td>";
                $tempplanetfighters = $planetfighters - $attackerfighters;
            }
            if ($planetfighters > $attackerfighters)
            {
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_allyourfightersdestroyed'] . "</strong></font></td><td></td>";
                $tempplayfighters = 0;
            }
            else
            {
                $tempplayfighters = $attackerfighters - $planetfighters;
                $langvars['l_cmb_fightertofighterlost'] = str_replace("[cmb_planetfighters]", $planetfighters, $langvars['l_cmb_fightertofighterlost']);
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_fightertofighterlost'] . "</strong></font></td><td></td>";
            }
            $attackerfighters = $tempplayfighters;
            $planetfighters = $tempplanetfighters;
        }
        if ($attackerfighters > 0 && $planetshields > 0)
        {
            if ($attackerfighters > $planetshields)
            {
                $attackerfighters = $attackerfighters - round($planetshields / 2);
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_youbreachedplanetshields'] . "</strong></font></td><td></td>";
                $planetshields = 0;
            }
            else
            {
                $langvars['l_cmb_shieldsremainup'] = str_replace("[cmb_attackerfighters]", $attackerfighters, $langvars['l_cmb_shieldsremainup']);
                echo "<tr align='center'><td></td><font color='#6098F8'><strong>" . $langvars['l_cmb_shieldsremainup'] . "</strong></font></td>";
                $planetshields = $planetshields - $attackerfighters;
            }
        }
        if ($planetfighters > 0)
        {
            if ($planetfighters > $attackerarmor)
            {
                $attackerarmor = 0;
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_fighterswarm'] . "</strong></font></td><td></td>";
            }
            else
            {
                $attackerarmor = $attackerarmor - $planetfighters;
                echo "<tr align='center'><td><font color='red'><strong>" . $langvars['l_cmb_swarmandrepel'] . "</strong></font></td><td></td>";
            }
        }

        echo "</table></center>\n";
        // Send each docked ship in sequence to attack agressor
        $result4 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE planet_id=? AND on_planet='Y'", array ($planetinfo['planet_id']));
        \Bnt\Db::logDbErrors($db, $result4, __LINE__, __FILE__);
        $shipsonplanet = $result4->RecordCount();

        if ($shipsonplanet > 0)
        {
            $langvars['l_cmb_shipdock'] = str_replace("[cmb_shipsonplanet]", $shipsonplanet, $langvars['l_cmb_shipdock']);
            echo "<br><br><center>" . $langvars['l_cmb_shipdock'] . "<br>" . $langvars['l_cmb_engshiptoshipcombat'] . "</center><br><br>\n";
            while (!$result4->EOF)
            {
                $onplanet = $result4->fields;

                if ($attackerfighters < 0)
                {
                    $attackerfighters = 0;
                }

                if ($attackertorps    < 0)
                {
                    $attackertorps = 0;
                }

                if ($attackershields  < 0)
                {
                    $attackershields = 0;
                }

                if ($attackerbeams    < 0)
                {
                    $attackerbeams = 0;
                }

                if ($attackerarmor    < 1)
                {
                    break;
                }

                echo "<br>-" . $onplanet['ship_name'] . " " . $langvars['l_cmb_approachattackvector'] . "-<br>";
                BadPlanet::shipToShip($db, $onplanet['ship_id']);
                $result4->MoveNext();
            }
        }
        else
        {
            echo "<br><br><center>" . $langvars['l_cmb_noshipsdocked'] . "</center><br><br>\n";
        }

        if ($attackerarmor < 1)
        {
            $free_ore = round($playerinfo['ship_ore'] / 2);
            $free_organics = round($playerinfo['ship_organics'] / 2);
            $free_goods = round($playerinfo['ship_goods'] / 2);
            $ship_value = $upgrade_cost * (round(pow($upgrade_factor, $playerinfo['hull'])) + round(pow($upgrade_factor, $playerinfo['engines'])) + round(pow($upgrade_factor, $playerinfo['power'])) + round(pow($upgrade_factor, $playerinfo['computer'])) + round(pow($upgrade_factor, $playerinfo['sensors'])) + round(pow($upgrade_factor, $playerinfo['beams'])) + round(pow($upgrade_factor, $playerinfo['torp_launchers'])) + round(pow($upgrade_factor, $playerinfo['shields'])) + round(pow($upgrade_factor, $playerinfo['armor'])) + round(pow($upgrade_factor, $playerinfo['cloak'])));
            $ship_salvage_rate = \Bnt\Rand::betterRand(0, 10);
            $ship_salvage = $ship_value * $ship_salvage_rate / 100;
            echo "<br><center><font size='+2' COLOR='red'><strong>" . $langvars['l_cmb_yourshipdestroyed'] . "</font></strong></center><br>";
            if ($playerinfo['dev_escapepod'] == "Y")
            {
                echo "<center><font color='white'>" . $langvars['l_cmb_escapepod'] . "</font></center><br><br>";
                $resx = $db->Execute("UPDATE {$db->prefix}ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=?,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',dev_lssd='N' WHERE ship_id=?", array ($bntreg->start_energy, $playerinfo['ship_id']));
                \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
                \Bnt\Bounty::collect($db, $langvars, $planetinfo['owner'], $playerinfo['ship_id']);
            }
            else
            {
                \Bnt\Player::kill($db, $playerinfo['ship_id'], $langvars, $bntreg, false);
                \Bnt\Bounty::collect($db, $langvars, $planetinfo['owner'], $playerinfo['ship_id']);
            }
        }
        else
        {
            $free_ore = 0;
            $free_goods = 0;
            $free_organics = 0;
            $ship_salvage_rate = 0;
            $ship_salvage = 0;
            $planetrating = $ownerinfo['hull'] + $ownerinfo['engines'] + $ownerinfo['computer'] + $ownerinfo['beams'] + $ownerinfo['torp_launchers'] + $ownerinfo['shields'] + $ownerinfo['armor'];
            if ($ownerinfo['rating'] != 0)
            {
                $rating_change = ($ownerinfo['rating'] / abs($ownerinfo['rating'])) * $planetrating * 10;
            }
            else
            {
                $rating_change=-100;
            }
            echo "<center><br><strong><font size='+2'>" . $langvars['l_cmb_finalcombatstats'] . "</font></strong><br><br>";
            $fighters_lost = $playerinfo['ship_fighters'] - $attackerfighters;
            $langvars['l_cmb_youlostfighters'] = str_replace("[cmb_fighters_lost]", $fighters_lost, $langvars['l_cmb_youlostfighters']);
            $langvars['l_cmb_youlostfighters'] = str_replace("[cmb_playerinfo_ship_fighters]", $playerinfo['ship_fighters'], $langvars['l_cmb_youlostfighters']);
            echo $langvars['l_cmb_youlostfighters'] . "<br>";
            $armor_lost = $playerinfo['armor_pts'] - $attackerarmor;
            $langvars['l_cmb_youlostarmorpoints'] = str_replace("[cmb_armor_lost]", $armor_lost, $langvars['l_cmb_youlostarmorpoints']);
            $langvars['l_cmb_youlostarmorpoints'] = str_replace("[cmb_playerinfo_armor_pts]", $playerinfo['armor_pts'], $langvars['l_cmb_youlostarmorpoints']);
            $langvars['l_cmb_youlostarmorpoints'] = str_replace("[cmb_attackerarmor]", $attackerarmor, $langvars['l_cmb_youlostarmorpoints']);
            echo $langvars['l_cmb_youlostarmorpoints'] . "<br>";
            $energy = $playerinfo['ship_energy'];
            $energy_lost = $bntreg->start_energy - $playerinfo['ship_energy'];
            $langvars['l_cmb_energyused'] = str_replace("[cmb_energy_lost]", $energy_lost, $langvars['l_cmb_energyused']);
            $langvars['l_cmb_energyused'] = str_replace("[cmb_playerinfo_ship_energy]", $bntreg->start_energy, $langvars['l_cmb_energyused']);
            echo $langvars['l_cmb_energyused'] . "<br></center>";
            $resx = $db->Execute("UPDATE {$db->prefix}ships SET ship_energy=?,ship_fighters=ship_fighters-?, torps=torps-?,armor_pts=armor_pts-?, rating=rating-? WHERE ship_id=?", array ($energy, $fighters_lost, $attackertorps, $armor_lost, $rating_change, $playerinfo['ship_id']));
            \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
        }

        $result4 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE planet_id=? AND on_planet='Y'", array ($planetinfo['planet_id']));
        \Bnt\Db::logDbErrors($db, $result4, __LINE__, __FILE__);
        $shipsonplanet = $result4->RecordCount();

        if ($planetshields < 1 && $planetfighters < 1 && $attackerarmor > 0 && $shipsonplanet == 0)
        {
            echo "<br><br><center><font color='GREEN'><strong>" . $langvars['l_cmb_planetdefeated'] . "</strong></font></center><br><br>";

            // Patch to stop players dumping credits for other players.
            $self_tech = \Bnt\CalcLevels::avgTech($playerinfo);
            $target_tech = round(\Bnt\CalcLevels::avgTech($ownerinfo));

            $roll = \Bnt\Rand::betterRand(0, (integer) $target_tech);
            if ($roll > $self_tech)
            {
                // Reset Planet Assets.
                $sql  = "UPDATE {$db->prefix}planets ";
                $sql .= "SET organics = '0', ore = '0', goods = '0', energy = '0', colonists = '2', credits = '0', fighters = '0', torps = '0', corp = '0', base = 'N', sells = 'N', prod_organics = '20', prod_ore = '20', prod_goods = '20', prod_energy = '20', prod_fighters = '10', prod_torp = '10' ";
                $sql .= "WHERE planet_id = ? LIMIT 1;";
                $resx = $db->Execute($sql, array ($planetinfo['planet_id']));
                \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
                echo "<div style='text-align:center; font-size:18px; color:#f00;'>The planet become unstable due to not being looked after, and all life and assets have been destroyed.</div>\n";
            }

            if ($min_value_capture != 0)
            {
                $playerscore = \Bnt\Score::updateScore($db, $playerinfo['ship_id'], $bntreg);
                $playerscore *= $playerscore;

                $planetscore = $planetinfo['organics'] * $organics_price + $planetinfo['ore'] * $ore_price + $planetinfo['goods'] * $goods_price + $planetinfo['energy'] * $energy_price + $planetinfo['fighters'] * $fighter_price + $planetinfo['torps'] * $torpedo_price + $planetinfo['colonists'] * $colonist_price + $planetinfo['credits'];
                $planetscore = $planetscore * $min_value_capture / 100;

                if ($playerscore < $planetscore)
                {
                    echo "<center>" . $langvars['l_cmb_citizenswanttodie'] . "</center><br><br>";
                    $resx = $db->Execute("DELETE FROM {$db->prefix}planets WHERE planet_id=?", array ($planetinfo['planet_id']));
                    \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
                    \Bnt\PlayerLog::writeLog($db, $ownerinfo['ship_id'], LOG_PLANET_DEFEATED_D, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");
                    \Bnt\AdminLog::writeLog($db, LOG_ADMIN_PLANETDEL, "$playerinfo[character_name]|$ownerinfo[character_name]|$playerinfo[sector]");
                    \Bnt\Score::updateScore($db, $ownerinfo['ship_id'], $bntreg);
                }
                else
                {
                    $langvars['l_cmb_youmaycapture'] = str_replace("[capture]", "<a href='planet.php?planet_id=". $planetinfo['planet_id'] ."&amp;command=capture'>" . $langvars['l_planet_capture1'] . "</a>", $langvars['l_cmb_youmaycapture']);
                    echo "<center><font color=red>" . $langvars['l_cmb_youmaycapture'] . "</font></center><br><br>";
                    \Bnt\PlayerLog::writeLog($db, $ownerinfo['ship_id'], LOG_PLANET_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");
                    \Bnt\Score::updateScore($db, $ownerinfo['ship_id'], $bntreg);
                    $update7a = $db->Execute("UPDATE {$db->prefix}planets SET owner=0, fighters=0, torps=torps-?, base='N', defeated='Y' WHERE planet_id=?", array ($planettorps, $planetinfo['planet_id']));
                    \Bnt\Db::logDbErrors($db, $update7a, __LINE__, __FILE__);
                }
            }
            else
            {
                $langvars['l_cmb_youmaycapture'] = str_replace("[capture]", "<a href='planet.php?planet_id=". $planetinfo['planet_id'] ."&amp;command=capture'>" . $langvars['l_planet_capture1'] . "</a>", $langvars['l_cmb_youmaycapture']);
                echo "<center>" . $langvars['l_cmb_youmaycapture'] . "</center><br><br>";
                \Bnt\PlayerLog::writeLog($db, $ownerinfo['ship_id'], LOG_PLANET_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");
                \Bnt\Score::updateScore($db, $ownerinfo['ship_id'], $bntreg);
                $update7a = $db->Execute("UPDATE {$db->prefix}planets SET owner=0,fighters=0, torps=torps-?, base='N', defeated='Y' WHERE planet_id=?", array ($planettorps, $planetinfo['planet_id']));
                \Bnt\Db::logDbErrors($db, $update7a, __LINE__, __FILE__);
            }

            \Bnt\Ownership::calc($db, $planetinfo['sector_id'], $min_bases_to_own, $langvars);
        }
        else
        {
            echo "<br><br><center><font color='#6098F8'><strong>" . $langvars['l_cmb_planetnotdefeated'] . "</strong></font></center><br><br>";
            $fighters_lost = $planetinfo['fighters'] - $planetfighters;
            $langvars['l_cmb_fighterloststat'] = str_replace("[cmb_fighters_lost]", $fighters_lost, $langvars['l_cmb_fighterloststat']);
            $langvars['l_cmb_fighterloststat'] = str_replace("[cmb_planetinfo_fighters]", $planetinfo['fighters'], $langvars['l_cmb_fighterloststat']);
            $langvars['l_cmb_fighterloststat'] = str_replace("[cmb_planetfighters]", $planetfighters, $langvars['l_cmb_fighterloststat']);
            $energy = $planetinfo['energy'];
            \Bnt\PlayerLog::writeLog($db, $ownerinfo['ship_id'], LOG_PLANET_NOT_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]|$free_ore|$free_organics|$free_goods|$ship_salvage_rate|$ship_salvage");
            \Bnt\Score::updateScore($db, $ownerinfo['ship_id'], $bntreg);
            $update7b = $db->Execute("UPDATE {$db->prefix}planets SET energy=?,fighters=fighters-?, torps=torps-?, ore=ore+?, goods=goods+?, organics=organics+?, credits=credits+? WHERE planet_id=?", array ($energy, $fighters_lost, $planettorps, $free_ore, $free_goods, $free_organics, $ship_salvage, $planetinfo['planet_id']));
            \Bnt\Db::logDbErrors($db, $update7b, __LINE__, __FILE__);
        }
        $update = $db->Execute("UPDATE {$db->prefix}ships SET turns=turns-1, turns_used=turns_used+1 WHERE ship_id=?", array ($playerinfo['ship_id']));
        \Bnt\Db::logDbErrors($db, $update, __LINE__, __FILE__);
    }

    public static function shipToShip($db, $langvars, $ship_id)
    {
        global $attackerbeams, $attackerfighters, $attackershields, $attackertorps, $attackerarmor, $attackertorpdamage, $level_factor;
        global $torp_dmg_rate, $rating_combat_factor, $upgrade_factor, $upgrade_cost, $armor_lost, $fighters_lost, $playerinfo;

        $resx = $db->Execute("LOCK TABLES {$db->prefix}ships WRITE, {$db->prefix}planets WRITE, {$db->prefix}sector_defence WRITE, {$db->prefix}universe WRITE, {$db->prefix}adodb_logsql WRITE, {$db->prefix}logs WRITE, {$db->prefix}bounty WRITE, {$db->prefix}news WRITE, {$db->prefix}zones READ");
        \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);

        $result2 = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE ship_id=?", array ($ship_id));
        \Bnt\Db::logDbErrors($db, $result2, __LINE__, __FILE__);
        $targetinfo = $result2->fields;

        echo "<br><br>-=-=-=-=-=-=-=--<br>
        " . $langvars['l_cmb_startingstats'] . ":<br>
        <br>
        " . $langvars['l_cmb_statattackerbeams'] . ": $attackerbeams<br>
        " . $langvars['l_cmb_statattackerfighters'] . ": $attackerfighters<br>
        " . $langvars['l_cmb_statattackershields'] . ": $attackershields<br>
        " . $langvars['l_cmb_statattackertorps'] . ": $attackertorps<br>
        " . $langvars['l_cmb_statattackerarmor'] . ": $attackerarmor<br>
        " . $langvars['l_cmb_statattackertorpdamage'] . ": $attackertorpdamage<br>";

        $targetbeams = \Bnt\CalcLevels::beams($targetinfo['beams'], $level_factor);
        if ($targetbeams > $targetinfo['ship_energy'])
        {
            $targetbeams = $targetinfo['ship_energy'];
        }
        $targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetbeams;
        $targetshields = \Bnt\CalcLevels::shields($targetinfo['shields'], $level_factor);
        if ($targetshields > $targetinfo['ship_energy'])
        {
            $targetshields = $targetinfo['ship_energy'];
        }
        $targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetshields;

        $targettorpnum = round(pow($level_factor, $targetinfo['torp_launchers'])) * 2;
        if ($targettorpnum > $targetinfo['torps'])
        {
            $targettorpnum = $targetinfo['torps'];
        }
        $targettorpdmg = $torp_dmg_rate * $targettorpnum;
        $targetarmor = $targetinfo['armor_pts'];
        $targetfighters = $targetinfo['ship_fighters'];
        $targetdestroyed = 0;
        $playerdestroyed = 0;
        echo "-->$targetinfo[ship_name] " . $langvars['l_cmb_isattackingyou'] . "<br><br>";
        echo $langvars['l_cmb_beamexchange'] . "<br>";
        if ($targetfighters > 0 && $attackerbeams > 0)
        {
            if ($attackerbeams > round($targetfighters / 2))
            {
                $temp = round($targetfighters / 2);
                $lost = $targetfighters - $temp;
                $targetfighters = $temp;
                $attackerbeams = $attackerbeams - $lost;
                $langvars['l_cmb_beamsdestroy'] = str_replace("[cmb_lost]", $lost, $langvars['l_cmb_beamsdestroy']);
                echo "<-- " . $langvars['l_cmb_beamsdestroy'] . "<br>";
            }
            else
            {
                $targetfighters = $targetfighters - $attackerbeams;
                $langvars['l_cmb_beamsdestroy2']  = str_replace("[cmb_attackerbeams]", $attackerbeams, $langvars['l_cmb_beamsdestroy2']);
                echo "--> " . $langvars['l_cmb_beamsdestroy2'] . "<br>";
                $attackerbeams = 0;
            }
        }
        elseif ($targetfighters > 0 && $attackerbeams < 1)
        {
            echo $langvars['l_cmb_nobeamsareleft'] . "<br>";
        }
        else
        {
            echo $langvars['l_cmb_beamshavenotarget'] . "<br>";
        }
        if ($attackerfighters > 0 && $targetbeams > 0)
        {
            if ($targetbeams > round($attackerfighters / 2))
            {
                $temp=round($attackerfighters/2);
                $lost = $attackerfighters - $temp;
                $attackerfighters = $temp;
                $targetbeams = $targetbeams - $lost;
                $langvars['l_cmb_fighterdestroyedbybeams'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_fighterdestroyedbybeams']);
                $langvars['l_cmb_fighterdestroyedbybeams'] = str_replace("[cmb_lost]", $lost, $langvars['l_cmb_fighterdestroyedbybeams']);
                echo "--> " . $langvars['l_cmb_fighterdestroyedbybeams'] . "<br>";
            }
            else
            {
                $attackerfighters = $attackerfighters - $targetbeams;
                $langvars['l_cmb_beamsdestroystillhave'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_beamsdestroystillhave']);
                $langvars['l_cmb_beamsdestroystillhave'] = str_replace("[cmb_targetbeams]", $targetbeams, $langvars['l_cmb_beamsdestroystillhave']);
                $langvars['l_cmb_beamsdestroystillhave'] = str_replace("[cmb_attackerfighters]", $attackerfighters, $langvars['l_cmb_beamsdestroystillhave']);
                echo "<-- " . $langvars['l_cmb_beamsdestroystillhave'] . "<br>";
                $targetbeams = 0;
            }
        }
        elseif ($attackerfighters > 0 && $targetbeams < 1)
        {
            echo $langvars['l_cmb_fighterunhindered'] . "<br>";
        }
        else
        {
            echo $langvars['l_cmb_youhavenofightersleft'] . "<br>";
        }

        if ($attackerbeams > 0)
        {
            if ($attackerbeams > $targetshields)
            {
                $attackerbeams = $attackerbeams - $targetshields;
                $targetshields = 0;
                $langvars['l_cmb_breachedsomeshields'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_breachedsomeshields']);
                echo "<-- " . $langvars['l_cmb_breachedsomeshields'] . "<br>";
            }
            else
            {
                $langvars['l_cmb_shieldsarehitbybeams'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_shieldsarehitbybeams']);
                $langvars['l_cmb_shieldsarehitbybeams'] = str_replace("[cmb_attackerbeams]", $attackerbeams, $langvars['l_cmb_shieldsarehitbybeams']);
                echo $langvars['l_cmb_shieldsarehitbybeams'] . "<br>";
                $targetshields = $targetshields - $attackerbeams;
                $attackerbeams = 0;
            }
        }
        else
        {
            $langvars['l_cmb_nobeamslefttoattack'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_nobeamslefttoattack']);
            echo $langvars['l_cmb_nobeamslefttoattack'] . "<br>";
        }
        if ($targetbeams > 0)
        {
            if ($targetbeams > $attackershields)
            {
                $targetbeams = $targetbeams - $attackershields;
                $attackershields = 0;
                $langvars['l_cmb_yourshieldsbreachedby'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourshieldsbreachedby']);
                echo "--> " . $langvars['l_cmb_yourshieldsbreachedby'] . "<br>";
            }
            else
            {
                $langvars['l_cmb_yourshieldsarehit'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourshieldsarehit']);
                $langvars['l_cmb_yourshieldsarehit'] = str_replace("[cmb_targetbeams]", $targetbeams, $langvars['l_cmb_yourshieldsarehit']);
                echo "<-- " . $langvars['l_cmb_yourshieldsarehit'] . "<br>";
                $attackershields = $attackershields - $targetbeams;
                $targetbeams = 0;
            }
        }
        else
        {
            $langvars['l_cmb_hehasnobeamslefttoattack'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_hehasnobeamslefttoattack']);
            echo $langvars['l_cmb_hehasnobeamslefttoattack'] . "<br>";
        }
        if ($attackerbeams > 0)
        {
            if ($attackerbeams > $targetarmor)
            {
                $targetarmor=0;
                $langvars['l_cmb_yourbeamsbreachedhim'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourbeamsbreachedhim']);
                echo "--> " . $langvars['l_cmb_yourbeamsbreachedhim'] . "<br>";
            }
            else
            {
                $targetarmor = $targetarmor - $attackerbeams;
                $langvars['l_cmb_yourbeamshavedonedamage'] = str_replace("[cmb_attackerbeams]", $attackerbeams, $langvars['l_cmb_yourbeamshavedonedamage']);
                $langvars['l_cmb_yourbeamshavedonedamage'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourbeamshavedonedamage']);
                echo $langvars['l_cmb_yourbeamshavedonedamage'] . "<br>";
            }
        }
        else
        {
            $langvars['l_cmb_nobeamstoattackarmor'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_nobeamstoattackarmor']);
            echo $langvars['l_cmb_nobeamstoattackarmor'] . "<br>";
        }
        if ($targetbeams > 0)
        {
            if ($targetbeams > $attackerarmor)
            {
                $attackerarmor = 0;
                $langvars['l_cmb_yourarmorbreachedbybeams'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourarmorbreachedbybeams']);
                echo "--> " . $langvars['l_cmb_yourarmorbreachedbybeams'] . "<br>";
            }
            else
            {
                $attackerarmor = $attackerarmor - $targetbeams;
                $langvars['l_cmb_yourarmorhitdamaged'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourarmorhitdamaged']);
                $langvars['l_cmb_yourarmorhitdamaged'] = str_replace("[cmb_targetbeams]", $targetbeams, $langvars['l_cmb_yourarmorhitdamaged']);
                echo "<-- " . $langvars['l_cmb_yourarmorhitdamaged'] . "<br>";
            }
        }
        else
        {
            $langvars['l_cmb_hehasnobeamslefttoattackyou'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_hehasnobeamslefttoattackyou']);
            echo $langvars['l_cmb_hehasnobeamslefttoattackyou'] . "<br>";
        }
        echo "<br>" . $langvars['l_cmb_torpedoexchange'] . "<br>";
        if ($targetfighters > 0 && $attackertorpdamage > 0)
        {
            if ($attackertorpdamage > round($targetfighters / 2))
            {
                $temp = round($targetfighters / 2);
                $lost = $targetfighters - $temp;
                $targetfighters = $temp;
                $attackertorpdamage = $attackertorpdamage - $lost;
                $langvars['l_cmb_yourtorpsdestroy'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourtorpsdestroy']);
                $langvars['l_cmb_yourtorpsdestroy'] = str_replace("[cmb_lost]", $lost, $langvars['l_cmb_yourtorpsdestroy']);
                echo "--> " . $langvars['l_cmb_yourtorpsdestroy'] . "<br>";
            }
            else
            {
                $targetfighters = $targetfighters - $attackertorpdamage;
                $langvars['l_cmb_yourtorpsdestroy2'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourtorpsdestroy2']);
                $langvars['l_cmb_yourtorpsdestroy2'] = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $langvars['l_cmb_yourtorpsdestroy2']);
                echo "<-- " . $langvars['l_cmb_yourtorpsdestroy2'] . "<br>";
                $attackertorpdamage = 0;
            }
        }
        elseif ($targetfighters > 0 && $attackertorpdamage < 1)
        {
            $langvars['l_cmb_youhavenotorpsleft'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_youhavenotorpsleft']);
            echo $langvars['l_cmb_youhavenotorpsleft'] . "<br>";
        }
        else
        {
            $langvars['l_cmb_hehasnofighterleft'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_hehasnofighterleft']);
            echo $langvars['l_cmb_hehasnofighterleft'] . "<br>";
        }
        if ($attackerfighters > 0 && $targettorpdmg > 0)
        {
            if ($targettorpdmg > round($attackerfighters / 2))
            {
                $temp = round($attackerfighters / 2);
                $lost = $attackerfighters - $temp;
                $attackerfighters = $temp;
                $targettorpdmg = $targettorpdmg - $lost;
                $langvars['l_cmb_torpsdestroyyou'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_torpsdestroyyou']);
                $langvars['l_cmb_torpsdestroyyou'] = str_replace("[cmb_lost]", $lost, $langvars['l_cmb_torpsdestroyyou']);
                echo "--> " . $langvars['l_cmb_torpsdestroyyou'] . "<br>";
            }
            else
            {
                $attackerfighters = $attackerfighters - $targettorpdmg;
                $langvars['l_cmb_someonedestroyedfighters'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_someonedestroyedfighters']);
                $langvars['l_cmb_someonedestroyedfighters'] = str_replace("[cmb_targettorpdmg]", $targettorpdmg, $langvars['l_cmb_someonedestroyedfighters']);
                echo "<-- " . $langvars['l_cmb_someonedestroyedfighters'] . "<br>";
                $targettorpdmg=0;
            }
        }
        elseif ($attackerfighters > 0 && $targettorpdmg < 1)
        {
            $langvars['l_cmb_hehasnotorpsleftforyou'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_hehasnotorpsleftforyou']);
            echo $langvars['l_cmb_hehasnotorpsleftforyou'] . "<br>";
        }
        else
        {
            $langvars['l_cmb_youhavenofightersanymore'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_youhavenofightersanymore']);
            echo $langvars['l_cmb_youhavenofightersanymore'] . "<br>";
        }
        if ($attackertorpdamage > 0)
        {
            if ($attackertorpdamage > $targetarmor)
            {
                $targetarmor = 0;
                $langvars['l_cmb_youbreachedwithtorps'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_youbreachedwithtorps']);
                echo "--> " . $langvars['l_cmb_youbreachedwithtorps'] . "<br>";
            }
            else
            {
                $targetarmor = $targetarmor - $attackertorpdamage;
                $langvars['l_cmb_hisarmorishitbytorps'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_hisarmorishitbytorps']);
                $langvars['l_cmb_hisarmorishitbytorps'] = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $langvars['l_cmb_hisarmorishitbytorps']);
                echo "<-- " . $langvars['l_cmb_hisarmorishitbytorps'] . "<br>";
            }
        }
        else
        {
            $langvars['l_cmb_notorpslefttoattackarmor'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_notorpslefttoattackarmor']);
            echo $langvars['l_cmb_notorpslefttoattackarmor'] . "<br>";
        }
        if ($targettorpdmg > 0)
        {
            if ($targettorpdmg > $attackerarmor)
            {
                $attackerarmor = 0;
                $langvars['l_cmb_yourarmorbreachedbytorps'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourarmorbreachedbytorps']);
                echo "<-- " . $langvars['l_cmb_yourarmorbreachedbytorps'] . "<br>";
            }
            else
            {
                $attackerarmor = $attackerarmor - $targettorpdmg;
                $langvars['l_cmb_yourarmorhitdmgtorps'] = str_replace("[cmb_targettorpdmg]", $targettorpdmg, $langvars['l_cmb_yourarmorhitdmgtorps']);
                $langvars['l_cmb_yourarmorhitdmgtorps'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourarmorhitdmgtorps']);
                echo "<-- " . $langvars['l_cmb_yourarmorhitdmgtorps'] . "<br>";
            }
        }
        else
        {
            $langvars['l_cmb_hehasnotorpsforyourarmor'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_hehasnotorpsforyourarmor']);
            echo $langvars['l_cmb_hehasnotorpsforyourarmor'] . "<br>";
        }
        echo "<br>" . $langvars['l_cmb_fightersattackexchange'] . "<br>";
        if ($attackerfighters > 0 && $targetfighters > 0)
        {
            if ($attackerfighters > $targetfighters)
            {
                $langvars['l_cmb_enemylostallfighters'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_enemylostallfighters']);
                echo "--> " . $langvars['l_cmb_enemylostallfighters'] . "<br>";
                $temptargfighters = 0;
            }
            else
            {
                $langvars['l_cmb_helostsomefighters'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_helostsomefighters']);
                $langvars['l_cmb_helostsomefighters'] = str_replace("[cmb_attackerfighters]", $attackerfighters, $langvars['l_cmb_helostsomefighters']);
                echo $langvars['l_cmb_helostsomefighters'] . "<br>";
                $temptargfighters = $targetfighters - $attackerfighters;
            }
            if ($targetfighters > $attackerfighters)
            {
                echo "<-- " . $langvars['l_cmb_youlostallfighters'] . "<br>";
                $tempplayfighters = 0;
            }
            else
            {
                $langvars['l_cmb_youalsolostsomefighters'] = str_replace("[cmb_targetfighters]", $targetfighters, $langvars['l_cmb_youalsolostsomefighters']);
                echo "<-- " . $langvars['l_cmb_youalsolostsomefighters'] . "<br>";
                $tempplayfighters = $attackerfighters - $targetfighters;
            }
            $attackerfighters = $tempplayfighters;
            $targetfighters = $temptargfighters;
        }
        elseif ($attackerfighters > 0 && $targetfighters < 1)
        {
            $langvars['l_cmb_hehasnofightersleftattack'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_hehasnofightersleftattack']);
            echo $langvars['l_cmb_hehasnofightersleftattack'] . "<br>";
        }
        else
        {
            $langvars['l_cmb_younofightersattackleft'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_younofightersattackleft']);
            echo $langvars['l_cmb_younofightersattackleft'] . "<br>";
        }
        if ($attackerfighters > 0)
        {
            if ($attackerfighters > $targetarmor)
            {
                $targetarmor = 0;
                $langvars['l_cmb_youbreachedarmorwithfighters'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_youbreachedarmorwithfighters']);
                echo "--> " . $langvars['l_cmb_youbreachedarmorwithfighters'] . "<br>";
            }
            else
            {
                $targetarmor = $targetarmor - $attackerfighters;
                $langvars['l_cmb_youhitarmordmgfighters'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_youhitarmordmgfighters']);
                $langvars['l_cmb_youhitarmordmgfighters'] = str_replace("[cmb_attackerfighters]", $attackerfighters, $langvars['l_cmb_youhitarmordmgfighters']);
                echo "<-- " . $langvars['l_cmb_youhitarmordmgfighters'] . "<br>";
            }
        }
        else
        {
            $langvars['l_cmb_youhavenofighterstoarmor'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_youhavenofighterstoarmor']);
            echo $langvars['l_cmb_youhavenofighterstoarmor'] . "<br>";
        }
        if ($targetfighters > 0)
        {
            if ($targetfighters > $attackerarmor)
            {
                $attackerarmor = 0;
                $langvars['l_cmb_hasbreachedarmorfighters'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_hasbreachedarmorfighters']);
                echo "<-- " . $langvars['l_cmb_hasbreachedarmorfighters'] . "<br>";
            }
            else
            {
                $attackerarmor = $attackerarmor - $targetfighters;
                $langvars['l_cmb_yourarmorishitfordmgby'] = str_replace("[cmb_targetfighters]", $targetfighters, $langvars['l_cmb_yourarmorishitfordmgby']);
                $langvars['l_cmb_yourarmorishitfordmgby'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_yourarmorishitfordmgby']);
                echo "--> " . $langvars['l_cmb_yourarmorishitfordmgby'] . "<br>";
            }
        }
        else
        {
            $langvars['l_cmb_nofightersleftheforyourarmor'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_nofightersleftheforyourarmor']);
            echo $langvars['l_cmb_nofightersleftheforyourarmor'] . "<br>";
        }
        if ($targetarmor < 1)
        {
            $langvars['l_cmb_hehasbeendestroyed'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_hehasbeendestroyed']);
            echo "<br>" . $langvars['l_cmb_hehasbeendestroyed'] . "<br>";
            if ($attackerarmor > 0)
            {
                $rating_change=round($targetinfo['rating'] * $rating_combat_factor);
                $free_ore = round($targetinfo['ship_ore'] / 2);
                $free_organics = round($targetinfo['ship_organics'] / 2);
                $free_goods = round($targetinfo['ship_goods'] / 2);
                $free_holds = \Bnt\CalcLevels::holds($playerinfo['hull'], $level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
                if ($free_holds > $free_goods)
                {
                    $salv_goods = $free_goods;
                    $free_holds = $free_holds - $free_goods;
                }
                elseif ($free_holds > 0)
                {
                    $salv_goods = $free_holds;
                    $free_holds = 0;
                }
                else
                {
                    $salv_goods = 0;
                }
                if ($free_holds > $free_ore)
                {
                    $salv_ore = $free_ore;
                    $free_holds = $free_holds - $free_ore;
                }
                elseif ($free_holds > 0)
                {
                    $salv_ore = $free_holds;
                    $free_holds = 0;
                }
                else
                {
                    $salv_ore = 0;
                }
                if ($free_holds > $free_organics)
                {
                    $salv_organics = $free_organics;
                    $free_holds = $free_holds - $free_organics;
                }
                elseif ($free_holds > 0)
                {
                    $salv_organics = $free_holds;
                    $free_holds = 0;
                }
                else
                {
                    $salv_organics = 0;
                }
                $ship_value = $upgrade_cost * (round(pow($upgrade_factor, $targetinfo['hull']))+round(pow($upgrade_factor, $targetinfo['engines']))+round(pow($upgrade_factor, $targetinfo['power']))+round(pow($upgrade_factor, $targetinfo['computer']))+round(pow($upgrade_factor, $targetinfo['sensors']))+round(pow($upgrade_factor, $targetinfo['beams']))+round(pow($upgrade_factor, $targetinfo['torp_launchers']))+round(pow($upgrade_factor, $targetinfo['shields']))+round(pow($upgrade_factor, $targetinfo['armor']))+round(pow($upgrade_factor, $targetinfo['cloak'])));
                $ship_salvage_rate = \Bnt\Rand::betterRand(10, 20);
                $ship_salvage = $ship_value * $ship_salvage_rate / 100;
                $langvars['l_cmb_yousalvaged'] = str_replace("[cmb_salv_ore]", $salv_ore, $langvars['l_cmb_yousalvaged']);
                $langvars['l_cmb_yousalvaged'] = str_replace("[cmb_salv_organics]", $salv_organics, $langvars['l_cmb_yousalvaged']);
                $langvars['l_cmb_yousalvaged'] = str_replace("[cmb_salv_goods]", $salv_goods, $langvars['l_cmb_yousalvaged']);
                $langvars['l_cmb_yousalvaged'] = str_replace("[cmb_salvage_rate]", $ship_salvage_rate, $langvars['l_cmb_yousalvaged']);
                $langvars['l_cmb_yousalvaged'] = str_replace("[cmb_salvage]", $ship_salvage, $langvars['l_cmb_yousalvaged']);
                $langvars['l_cmb_yousalvaged2'] = str_replace("[cmb_number_rating_change]", number_format(abs($rating_change), 0, $local_number_dec_point, $local_number_thousands_sep), $langvars['l_cmb_yousalvaged2']);
                echo $langvars['l_cmb_yousalvaged'] . "<br>" . $langvars['l_cmb_yousalvaged2'];
                $update3 = $db->Execute("UPDATE {$db->prefix}ships SET ship_ore=ship_ore+?, ship_organics=ship_organics+?, ship_goods=ship_goods+?, credits=credits+? WHERE ship_id=?", array ($salv_ore, $salv_organics, $salv_goods, $ship_salvage, $playerinfo['ship_id']));
                \Bnt\Db::logDbErrors($db, $update3, __LINE__, __FILE__);
            }

            if ($targetinfo['dev_escapepod'] == "Y")
            {
                $rating = round($targetinfo['rating'] / 2);
                echo $langvars['l_cmb_escapepodlaunched'] . "<br><br>";
                echo "<br><br>ship_id = $targetinfo[ship_id]<br><br>";
                $test = $db->Execute("UPDATE {$db->prefix}ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=?,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating=?,dev_lssd='N' WHERE ship_id=?", array ($bntreg->start_energy, $rating, $targetinfo['ship_id']));
                \Bnt\Db::logDbErrors($db, $test, __LINE__, __FILE__);
                \Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|Y");
                \Bnt\Bounty::collect($db, $langvars, $playerinfo['ship_id'], $targetinfo['ship_id']);
            }
            else
            {
                \Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|N");
                \Bnt\Player::kill($db, $targetinfo['ship_id'], $langvars, $bntreg, false);
                \Bnt\Bounty::collect($db, $langvars, $playerinfo['ship_id'], $targetinfo['ship_id']);
            }
        }
        else
        {
            $langvars['l_cmb_youdidntdestroyhim'] = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $langvars['l_cmb_youdidntdestroyhim']);
            echo $langvars['l_cmb_youdidntdestroyhim'] . "<br>";
            $target_rating_change = round($targetinfo['rating'] * .1);
            $target_armor_lost = $targetinfo['armor_pts'] - $targetarmor;
            $target_fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
            $target_energy = $targetinfo['ship_energy'];
            \Bnt\PlayerLog::writeLog($db, $targetinfo['ship_id'], LOG_ATTACKED_WIN, "$playerinfo[character_name]|$target_armor_lost|$target_fighters_lost");
            $update4 = $db->Execute("UPDATE {$db->prefix}ships SET ship_energy=?,ship_fighters=ship_fighters-?, armor_pts=armor_pts-?, torps=torps-? WHERE ship_id=?", array ($target_energy, $target_fighters_lost, $target_armor_lost, $targettorpnum, $targetinfo['ship_id']));
            \Bnt\Db::logDbErrors($db, $update4, __LINE__, __FILE__);
        }
        echo "<br>_+_+_+_+_+_+_<br>";
        echo $langvars['l_cmb_shiptoshipcombatstats'] . "<br>";
        echo $langvars['l_cmb_statattackerbeams'] . ": $attackerbeams<br>";
        echo $langvars['l_cmb_statattackerfighters'] . ": $attackerfighters<br>";
        echo $langvars['l_cmb_statattackershields'] . ": $attackershields<br>";
        echo $langvars['l_cmb_statattackertorps'] . ": $attackertorps<br>";
        echo $langvars['l_cmb_statattackerarmor'] . ": $attackerarmor<br>";
        echo $langvars['l_cmb_statattackertorpdamage'] . ": $attackertorpdamage<br>";
        echo "_+_+_+_+_+_+<br>";
        $resx = $db->Execute("UNLOCK TABLES");
        \Bnt\Db::logDbErrors($db, $resx, __LINE__, __FILE__);
    }
}
?>
