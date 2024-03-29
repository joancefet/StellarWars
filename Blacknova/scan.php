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
// File: scan.php

include './global_includes.php';

Bnt\Login::checkLogin ($db, $pdo_db, $lang, $langvars, $bntreg, $template);

$title = $langvars['l_scan_title'];
Bnt\Header::display($db, $lang, $template, $title);

// Database driven language entries
$langvars = Bnt\Translate::load ($db, $lang, array ('scan', 'common', 'bounty', 'report', 'main', 'global_includes', 'global_funcs', 'footer', 'news', 'planet', 'regional'));

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email=?", array ($_SESSION['username']));
Bnt\Db::logDbErrors ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

$filtered_ship_id = filter_input (INPUT_GET, 'ship_id', FILTER_SANITIZE_NUMBER_INT);
$result2 = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE ship_id=?", array ($filtered_ship_id));
Bnt\Db::logDbErrors ($db, $result2, __LINE__, __FILE__);
$targetinfo = $result2->fields;

$playerscore = Bnt\Score::updateScore ($db, $playerinfo['ship_id'], $bntreg);
$targetscore = Bnt\Score::updateScore ($db, $targetinfo['ship_id'], $bntreg);

$playerscore = $playerscore * $playerscore;
$targetscore = $targetscore * $targetscore;

echo "<h1>" . $title . "</h1>\n";

// Kami Multi Browser Window Attack Fix
if (array_key_exists ('ship_selected', $_SESSION) == false || $_SESSION['ship_selected'] != $_GET['ship_id'])
{
    echo "You need to Click on the ship first.<br><br>";
    Bnt\Text::gotoMain ($db, $lang, $langvars);
    Bad\Footer::display($pdo_db, $lang, $bntreg, $template);
    die ();
}
unset ($_SESSION['ship_selected']);

// Check to ensure target is in the same sector as player
if ($targetinfo['sector'] != $playerinfo['sector'])
{
    echo $langvars['l_planet_noscan'];
}
else
{
    if ($playerinfo['turns'] < 1)
    {
        echo $langvars['l_scan_turn'];
    }
    else
    {
        // Determine per cent chance of success in scanning target ship - based on player's sensors and opponent's cloak
        $success = Bnt\Scan::success ($playerinfo['sensors'], $targetinfo['cloak']);
        if ($success < 5)
        {
            $success = 5;
        }
        if ($success > 95)
        {
            $success = 95;
        }

        $roll = Bnt\Rand::betterRand (1, 100);
        if ($roll > $success)
        {
            // If scan fails - inform both player and target.
            echo $langvars['l_planet_noscan'];
            Bnt\PlayerLog::writeLog ($db, $targetinfo['ship_id'], LOG_SHIP_SCAN_FAIL, $playerinfo['character_name']);
        }
        else
        {
            // If scan succeeds, show results and inform target. Scramble results by scan error factor.

            // Get total bounty on this player, if any
            $btyamount = 0;
            $hasbounty = $db->Execute ("SELECT SUM(amount) AS btytotal FROM {$db->prefix}bounty WHERE bounty_on = ?", array ($targetinfo['ship_id']));
            Bnt\Db::logDbErrors ($db, $hasbounty, __LINE__, __FILE__);

            if ($hasbounty)
            {
                $resx = $hasbounty->fields;
                if ($resx['btytotal'] > 0)
                {
                    $btyamount = number_format ($resx['btytotal'], 0, $langvars['local_number_dec_point'], $langvars['local_number_thousands_sep']);
                    $langvars['l_scan_bounty'] = str_replace ("[amount]", $btyamount, $langvars['l_scan_bounty']);
                    echo $langvars['l_scan_bounty'] . "<br>";
                    $btyamount = 0;

                    // Check for Federation bounty
                    $hasfedbounty = $db->Execute ("SELECT SUM(amount) AS btytotal FROM {$db->prefix}bounty WHERE bounty_on = ? AND placed_by = 0", array ($targetinfo['ship_id']));
                    Bnt\Db::logDbErrors ($db, $hasfedbounty, __LINE__, __FILE__);
                    if ($hasfedbounty)
                    {
                        $resy = $hasfedbounty->fields;
                        if ($resy['btytotal'] > 0)
                        {
                            $btyamount = $resy['btytotal'];
                            echo $langvars['l_scan_fedbounty'] . "<br>";
                        }
                    }
                }
            }

            // Player will get a Federation Bounty on themselves if they attack a player who's score is less than bounty_ratio of
            // themselves. If the target has a Federation Bounty, they can attack without attracting a bounty on themselves.
            if ($btyamount == 0 && ((($targetscore / $playerscore) < $bounty_ratio) || $targetinfo['turns_used'] < $bounty_minturns))
            {
                echo $langvars['l_by_fedbounty'] . "<br><br>";
            }
            else
            {
                echo $langvars['l_by_nofedbounty'] . "<br><br>";
            }

            $sc_error = Bnt\Scan::error ($playerinfo['sensors'], $targetinfo['cloak'], $scan_error_factor);
            echo $langvars['l_scan_ron'] ." " . $targetinfo['ship_name'] . ", " . $langvars['l_scan_capt'] . " " . $targetinfo['character_name'] . "<br><br>";
            echo "<strong>" . $langvars['l_ship_levels'] . ":</strong><br><br>";
            echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
            echo "<tr><td>" . $langvars['l_hull'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_hull = round ($targetinfo['hull'] * $sc_error / 100);
                echo "<td>$sc_hull</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_engines'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_engines = round ($targetinfo['engines'] * $sc_error / 100);
                echo "<td>$sc_engines</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_power'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_power = round ($targetinfo['power'] * $sc_error / 100);
                echo "<td>$sc_power</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_computer'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_computer = round ($targetinfo['computer'] * $sc_error / 100);
                echo "<td>$sc_computer</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_sensors'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_sensors = round ($targetinfo['sensors'] * $sc_error / 100);
                echo "<td>$sc_sensors</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_beams'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_beams = round ($targetinfo['beams'] * $sc_error / 100);
                echo "<td>$sc_beams</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_torp_launch'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_torp_launchers = round ($targetinfo['torp_launchers'] * $sc_error / 100);
                echo "<td>$sc_torp_launchers</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_armor'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_armor = round ($targetinfo['armor'] * $sc_error / 100);
                echo "<td>$sc_armor</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_shields'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_shields = round ($targetinfo['shields'] * $sc_error / 100);
                echo "<td>$sc_shields</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_cloak'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_cloak = round ($targetinfo['cloak'] * $sc_error / 100);
                echo "<td>" . $sc_cloak . "</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "</table><br>";
            echo "<strong>" . $langvars['l_scan_arma'] . "</strong><br><br>";
            echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
            echo "<tr><td>" . $langvars['l_armorpts'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_armor_pts = round ($targetinfo['armor_pts'] * $sc_error / 100);
                echo "<td>" . $sc_armor_pts . "</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_fighters'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_ship_fighters = round ($targetinfo['ship_fighters'] * $sc_error / 100);
                echo "<td>$sc_ship_fighters</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_torps'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_torps = round ($targetinfo['torps'] * $sc_error / 100);
                echo "<td>$sc_torps</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "</table><br>";
            echo "<strong>" . $langvars['l_scan_carry'] . "</strong><br><br>";
            echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
            echo "<tr><td>Credits:</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_credits = round ($targetinfo['credits'] * $sc_error / 100);
                echo "<td>$sc_credits</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_colonists'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_ship_colonists = round ($targetinfo['ship_colonists'] * $sc_error / 100);
                echo "<td>$sc_ship_colonists</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_energy'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_ship_energy = round ($targetinfo['ship_energy'] * $sc_error / 100);
                echo "<td>$sc_ship_energy</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_ore'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_ship_ore = round ($targetinfo['ship_ore'] * $sc_error / 100);
                echo "<td>$sc_ship_ore</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_organics'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_ship_organics = round ($targetinfo['ship_organics'] * $sc_error / 100);
                echo "<td>$sc_ship_organics</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_goods'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_ship_goods = round ($targetinfo['ship_goods'] * $sc_error / 100);
                echo "<td>$sc_ship_goods</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "</table><br>";
            echo "<strong>" . $langvars['l_devices'] . ":</strong><br><br>";
            echo "<table  width=\"\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">";
            echo "<tr><td>" . $langvars['l_warpedit'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_dev_warpedit = round ($targetinfo['dev_warpedit'] * $sc_error / 100);
                echo "<td>$sc_dev_warpedit</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_genesis'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_dev_genesis = round ($targetinfo['dev_genesis'] * $sc_error / 100);
                echo "<td>$sc_dev_genesis</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_deflect'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_dev_minedeflector = round ($targetinfo['dev_minedeflector'] * $sc_error / 100);
                echo "<td>$sc_dev_minedeflector</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_ewd'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                $sc_dev_emerwarp = round ($targetinfo['dev_emerwarp'] * $sc_error / 100);
                echo "<td>$sc_dev_emerwarp</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_escape_pod'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                echo "<td>$targetinfo[dev_escapepod]</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "<tr><td>" . $langvars['l_fuel_scoop'] . ":</td>";
            $roll = Bnt\Rand::betterRand (1, 100);
            if ($roll < $success)
            {
                echo "<td>" . $targetinfo['dev_fuelscoop'] . "</td></tr>";
            }
            else
            {
                echo "<td>???</td></tr>";
            }

            echo "</table><br>";
            Bnt\PlayerLog::writeLog ($db, $targetinfo['ship_id'], LOG_SHIP_SCAN, "$playerinfo[character_name]");
        }

        $resx = $db->Execute ("UPDATE {$db->prefix}ships SET turns=turns-1,turns_used=turns_used+1 WHERE ship_id=?", array ($playerinfo['ship_id']));
        Bnt\Db::logDbErrors ($db, $resx, __LINE__, __FILE__);
    }
}

echo "<br><br>";
Bnt\Text::gotoMain ($db, $lang, $langvars);
Bad\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
