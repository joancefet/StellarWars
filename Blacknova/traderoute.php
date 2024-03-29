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
// File: traderoute.php

include './global_includes.php';
Bnt\Login::checkLogin($db, $pdo_db, $lang, $langvars, $bntreg, $template);

// Database driven language entries
$langvars = Bnt\Translate::load($db, $lang, array ('traderoutes', 'common', 'global_includes', 'global_funcs', 'footer', 'bounty', 'regional'));
$title = $langvars['l_tdr_title'];
Bnt\Header::display($db, $lang, $template, $title);

echo "<h1>" . $title . "</h1>\n";

$portfull = null; // This fixes an error of undefined variables on 1518

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
Bnt\Db::logDbErrors ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

$result = $db->Execute ("SELECT * FROM {$db->prefix}traderoutes WHERE owner = ?;", array ($playerinfo['ship_id']));
Bnt\Db::logDbErrors ($db, $result, __LINE__, __FILE__);
$num_traderoutes = $result->RecordCount();

if (isset ($traderoutes))
{
    Bnt\AdminLog::writeLog ($db, 902, "{$playerinfo['ship_id']}|Tried to insert a hardcoded TradeRoute.");
    Bad\Traderoute::traderouteDie ($db, $lang, $langvars, $bntreg, "<div style='color:#fff; font-size: 12px;'>[<span style='color:#ff0;'>The Governor</span>] <span style='color:#f00;'>Detected Traderoute Hack!</span></div>\n", $template);
}

$traderoutes = array ();
$i = 0;
while (!$result->EOF)
{
    $i = array_push ($traderoutes, $result->fields);
    // $traderoutes[$i] = $result->fields;
    // $i++;
    $result->MoveNext ();
}

$freeholds = Bnt\CalcLevels::holds ($playerinfo['hull'], $bntreg->level_factor) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];
$maxholds = Bnt\CalcLevels::holds ($playerinfo['hull'], $bntreg->level_factor);
$maxenergy = Bnt\CalcLevels::energy ($playerinfo['power'], $bntreg->level_factor);
if ($playerinfo['ship_colonists'] < 0 || $playerinfo['ship_ore'] < 0 || $playerinfo['ship_organics'] < 0 || $playerinfo['ship_goods'] < 0 || $playerinfo['ship_energy'] < 0 || $freeholds < 0)
{
    if ($playerinfo['ship_colonists'] < 0 || $playerinfo['ship_colonists'] > $maxholds)
    {
        Bnt\AdminLog::writeLog ($db, LOG_ADMIN_ILLEGVALUE, "$playerinfo[ship_name]|$playerinfo[ship_colonists]|colonists|$maxholds");
        $playerinfo['ship_colonists'] = 0;
    }
    if ($playerinfo['ship_ore'] < 0 || $playerinfo['ship_ore'] > $maxholds)
    {
        Bnt\AdminLog::writeLog ($db, LOG_ADMIN_ILLEGVALUE, "$playerinfo[ship_name]|$playerinfo[ship_ore]|ore|$maxholds");
        $playerinfo['ship_ore'] = 0;
    }
    if ($playerinfo['ship_organics'] < 0 || $playerinfo['ship_organics'] > $maxholds)
    {
        Bnt\AdminLog::writeLog ($db, LOG_ADMIN_ILLEGVALUE, "$playerinfo[ship_name]|$playerinfo[ship_organics]|organics|$maxholds");
        $playerinfo['ship_organics'] = 0;
    }
    if ($playerinfo['ship_goods'] < 0 || $playerinfo['ship_goods'] > $maxholds)
    {
        Bnt\AdminLog::writeLog ($db, LOG_ADMIN_ILLEGVALUE, "$playerinfo[ship_name]|$playerinfo[ship_goods]|goods|$maxholds");
        $playerinfo['ship_goods'] = 0;
    }
    if ($playerinfo['ship_energy'] < 0 || $playerinfo['ship_energy'] > $maxenergy)
    {
        Bnt\AdminLog::writeLog ($db, LOG_ADMIN_ILLEGVALUE, "$playerinfo[ship_name]|$playerinfo[ship_energy]|energy|$maxenergy");
        $playerinfo['ship_energy'] = 0;
    }
    if ($freeholds < 0)
    {
        $freeholds = 0;
    }

    $update1 = $db->Execute ("UPDATE {$db->prefix}ships SET ship_ore=?, ship_organics=?, ship_goods=?, ship_energy=?, ship_colonists=? WHERE ship_id=?;", array ($playerinfo['ship_ore'], $playerinfo['ship_organics'], $playerinfo['ship_goods'], $playerinfo['ship_energy'], $playerinfo['ship_colonists'], $playerinfo['ship_id']));
    Bnt\Db::logDbErrors ($db, $update1, __LINE__, __FILE__);
}

// Default to 1 run if we don't get a valid repeat value.
$tr_repeat = 1;
// Check if we have a $_POST['tr_repeat'] and that the type-casted value is larger than 0.
if (array_key_exists('tr_repeat', $_POST) == true && (integer) $_POST['tr_repeat'] >0)
{
    // Now type cast the repeat value into an integer.
    $tr_repeat = (integer) $_POST['tr_repeat'];
}

$command = null;
if (array_key_exists('command', $_REQUEST) == true)
{
    $command = $_REQUEST['command'];
}

if ($command == 'new')
{
    // Displays new trade route form
    Bad\Traderoute::traderouteNew ($db, $lang, $langvars, $bntreg, null, $template);
}
elseif ($command == 'create')
{
    // Enters new route in db
    Bad\Traderoute::traderouteCreate ($db, $lang, $langvars, $bntreg, $template);
}
elseif ($command == 'edit')
{
    // Displays new trade route form, edit
    Bad\Traderoute::traderouteNew ($db, $lang, $langvars, $bntreg, $traderoute_id, $template);
}
elseif ($command == 'delete')
{
    // Displays delete info
    Bad\Traderoute::traderouteDelete ($db, $lang, $langvars, $bntreg, $template);
}
elseif ($command == 'settings')
{
    // Global traderoute settings form
    Bad\Traderoute::traderouteSettings ($db, $lang, $langvars, $bntreg, $template);
}
elseif ($command == 'setsettings')
{
    // Enters settings in db
    Bad\Traderoute::traderouteSetsettings ($db, $lang, $langvars, $bntreg, $template);
}
elseif (isset ($engage))
{
    // Perform trade route
    $i = $tr_repeat;
    while ($i > 0)
    {
        $result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email=?", array ($_SESSION['username']));
        Bnt\Db::logDbErrors ($db, $result, __LINE__, __FILE__);
        $playerinfo = $result->fields;
        Bad\Traderoute::traderouteEngage ($db, $lang, $i, $langvars);
        $i--;
    }
}

if ($command != 'delete')
{
    $langvars['l_tdr_newtdr'] = str_replace ("[here]", "<a href='traderoute.php?command=new'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_newtdr']);
    echo "<p>" . $langvars['l_tdr_newtdr'] . "<p>";
    $langvars['l_tdr_modtdrset'] = str_replace ("[here]", "<a href='traderoute.php?command=settings'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_modtdrset']);
    echo "<p>" . $langvars['l_tdr_modtdrset'] . "<p>";
}
else
{
    $langvars['l_tdr_confdel'] = str_replace ("[here]", "<a href='traderoute.php?command=delete&amp;confirm=yes&amp;traderoute_id=" . $traderoute_id . "'>" . $langvars['l_here'] . "</a>", $langvars['l_tdr_confdel']);
    echo "<p>" . $langvars['l_tdr_confdel'] . "<p>";
}

if ($num_traderoutes == 0)
{
    echo $langvars['l_tdr_noactive'] . "<p>";
}
else
{
    echo '<table border=1 cellspacing=1 cellpadding=2 width="100%" align="center">' .
         '<tr bgcolor=' . $bntreg->color_line2 . '><td align="center" colspan=7><strong><font color=white>
         ';

    if ($command != 'delete')
    {
        echo $langvars['l_tdr_curtdr'];
    }
    else
    {
        echo $langvars['l_tdr_deltdr'];
    }

    echo "</font></strong>" .
         "</td></tr>" .
         "<tr align='center' bgcolor=$bntreg->color_line2>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_src'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_srctype'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_dest'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_desttype'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_move'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_circuit'] . "</strong></font></td>" .
         "<td><font size=2 color=white><strong>" . $langvars['l_tdr_change'] . "</strong></font></td>" .
         "</tr>";

    $i = 0;
    $curcolor = $bntreg->color_line1;
    while ($i < $num_traderoutes)
    {
        echo "<tr bgcolor=$curcolor>";
        if ($curcolor == $bntreg->color_line1)
        {
            $curcolor = $bntreg->color_line2;
        }
        else
        {
            $curcolor = $bntreg->color_line1;
        }

        echo "<td><font size=2 color=white>";
        if ($traderoutes[$i]['source_type'] == 'P')
        {
            echo "&nbsp;" . $langvars['l_tdr_portin'] . " <a href=rsmove.php?engage=1&destination=" . $traderoutes[$i]['source_id'] . ">" . $traderoutes[$i]['source_id'] . "</a></font></td>";
        }
        else
        {
            $result = $db->Execute ("SELECT name, sector_id FROM {$db->prefix}planets WHERE planet_id=?;", array ($traderoutes[$i]['source_id']));
            Bnt\Db::logDbErrors ($db, $result, __LINE__, __FILE__);
            if ($result)
            {
                $planet1 = $result->fields;
                echo "&nbsp;" . $langvars['l_tdr_planet'] . " <strong>$planet1[name]</strong>" . $langvars['l_tdr_within'] . "<a href=\"rsmove.php?engage=1&destination=$planet1[sector_id]\">$planet1[sector_id]</a></font></td>";
            }
            else
            {
                echo "&nbsp;" . $langvars['l_tdr_nonexistance'] . "</font></td>";
            }
        }

        echo "<td align='center'><font size=2 color=white>";
        if ($traderoutes[$i]['source_type'] == 'P')
        {
            $result = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?;", array ($traderoutes[$i]['source_id']));
            Bnt\Db::logDbErrors ($db, $result, __LINE__, __FILE__);
            $port1 = $result->fields;
            echo "&nbsp;" . Bnt\Ports::getType ($port1['port_type'], $langvars) . "</font></td>";
        }
        else
        {
            if (empty ($planet1))
            {
                echo "&nbsp;" . $langvars['l_tdr_na'] . "</font></td>";
            }
            else
            {
                echo "&nbsp;" . $langvars['l_tdr_cargo'] . "</font></td>";
            }
        }
        echo "<td><font size=2 color=white>";

        if ($traderoutes[$i]['dest_type'] == 'P')
        {
            echo "&nbsp;" . $langvars['l_tdr_portin'] . " <a href=\"rsmove.php?engage=1&destination=" . $traderoutes[$i]['dest_id'] . "\">" . $traderoutes[$i]['dest_id'] . "</a></font></td>";
        }
        else
        {
            $result = $db->Execute ("SELECT name, sector_id FROM {$db->prefix}planets WHERE planet_id=?;", array ($traderoutes[$i]['dest_id']));
            Bnt\Db::logDbErrors ($db, $result, __LINE__, __FILE__);
            if ($result)
            {
                $planet2 = $result->fields;
                echo "&nbsp;" . $langvars['l_tdr_planet'] . " <strong>$planet2[name]</strong>" . $langvars['l_tdr_within'] . "<a href=\"rsmove.php?engage=1&destination=$planet2[sector_id]\">$planet2[sector_id]</a></font></td>";
            }
            else
            {
                echo "&nbsp;" . $langvars['l_tdr_nonexistance'] . "</font></td>";
            }
        }
        echo "<td align='center'><font size=2 color=white>";

        if ($traderoutes[$i]['dest_type'] == 'P')
        {
            $result = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id=?;", array ($traderoutes[$i]['dest_id']));
            Bnt\Db::logDbErrors ($db, $result, __LINE__, __FILE__);
            $port2 = $result->fields;
            echo "&nbsp;" . Bnt\Ports::getType ($port2['port_type'], $langvars) . "</font></td>";
        }
        else
        {
            if (empty ($planet2))
            {
                echo "&nbsp;" . $langvars['l_tdr_na'] . "</font></td>";
            }
            else
            {
                echo "&nbsp;";
                if ($playerinfo['trade_colonists'] == 'N' && $playerinfo['trade_fighters'] == 'N' && $playerinfo['trade_torps'] == 'N')
                {
                    echo $langvars['l_tdr_none'];
                }
                else
                {
                    if ($playerinfo['trade_colonists'] == 'Y')
                    {
                        echo $langvars['l_tdr_colonists'];
                    }

                    if ($playerinfo['trade_fighters'] == 'Y')
                    {
                        if ($playerinfo['trade_colonists'] == 'Y')
                        {
                            echo ", ";
                        }

                        echo $langvars['l_tdr_fighters'];
                    }
                    if ($playerinfo['trade_torps'] == 'Y')
                    {
                        echo "<br>" . $langvars['l_tdr_torps'];
                    }
                }
                echo "</font></td>";
            }
        }
        echo "<td align='center'><font size=2 color=white>";

        if ($traderoutes[$i]['move_type'] == 'R')
        {
            echo "&nbsp;RS, ";

            if ($traderoutes[$i]['source_type'] == 'P')
            {
                $src = $port1;
            }
            else
            {
                $src = $planet1['sector_id'];
            }

            if ($traderoutes[$i]['dest_type'] == 'P')
            {
                $dst= $port2;
            }
            else
            {
                $dst = $planet2['sector_id'];
            }

            $dist = Bad\Traderoute::traderouteDistance ($db, $langvars, $traderoutes[$i]['source_type'], $traderoutes[$i]['dest_type'], $src, $dst, $traderoutes[$i]['circuit']);

            $langvars['l_tdr_escooped_temp'] = str_replace ("[tdr_dist_triptime]", $dist['triptime'], $langvars['l_tdr_escooped']);
            $langvars['l_tdr_escooped2_temp'] = str_replace ("[tdr_dist_scooped]", $dist['scooped'], $langvars['l_tdr_escooped2']);
            echo $langvars['l_tdr_escooped_temp'] . "<br>" . $langvars['l_tdr_escooped2_temp'];

            echo "</font></td>";
        }
        else
        {
            echo "&nbsp;" . $langvars['l_tdr_warp'];

            if ($traderoutes[$i]['circuit'] == '1')
            {
                echo ", 2 " . $langvars['l_tdr_turns'];
            }
            else
            {
                echo ", 4 " . $langvars['l_tdr_turns'];
            }

            echo "</font></td>";
        }

        echo "<td align='center'><font size=2 color=white>";

        if ($traderoutes[$i]['circuit'] == '1')
        {
            echo "&nbsp;1 " . $langvars['l_tdr_way'] . "</font></td>";
        }
        else
        {
            echo "&nbsp;2 " . $langvars['l_tdr_ways'] . "</font></td>";
        }

        echo "<td align='center'><font size=2 color=white>";
        echo "<a href=\"traderoute.php?command=edit&traderoute_id=" . $traderoutes[$i]['traderoute_id'] . "\">";
        echo $langvars['l_edit'] . "</a><br><a href=\"traderoute.php?command=delete&traderoute_id=" . $traderoutes[$i]['traderoute_id'] . "\">";
        echo $langvars['l_tdr_del'] . "</a></font></td></tr>";

        $i++;
    }
    echo "</table><p>";
}

echo "<div style='text-align:left;'>\n";
Bnt\Text::gotoMain ($db, $lang, $langvars);
echo "</div>\n";

Bad\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
