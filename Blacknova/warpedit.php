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
// File: warpedit.php

include './global_includes.php';

Bnt\Login::checkLogin ($db, $pdo_db, $lang, $langvars, $bntreg, $template);

$title = $langvars['l_warp_title'];
Bnt\Header::display($db, $lang, $template, $title);

// Database driven language entries
$langvars = Bnt\Translate::load ($db, $lang, array ('warpedit', 'common', 'global_includes', 'global_funcs', 'footer', 'news'));
echo "<h1>" . $title . "</h1>\n";

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
Bnt\Db::logDbErrors ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

$result4 = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id = ?;", array ($playerinfo['sector']));
Bnt\Db::logDbErrors ($db, $result4, __LINE__, __FILE__);
$sectorinfo = $result4->fields;

if ($playerinfo['turns'] < 1)
{
    echo $langvars['l_warp_turn'] . "<br><br>";
    Bnt\Text::gotoMain ($db, $lang, $langvars);
    Bad\Footer::display($pdo_db, $lang, $bntreg, $template);
    die ();
}

if ($playerinfo['dev_warpedit'] < 1)
{
    echo $langvars['l_warp_none'] . ".<br><br>";
    Bnt\Text::gotoMain ($db, $lang, $langvars);
    Bad\Footer::display($pdo_db, $lang, $bntreg, $template);
    die ();
}

$res = $db->Execute ("SELECT allow_warpedit FROM {$db->prefix}zones WHERE zone_id = ?;", array ($sectorinfo['zone_id']));
Bnt\Db::logDbErrors ($db, $res, __LINE__, __FILE__);
$zoneinfo = $res->fields;
if ($zoneinfo['allow_warpedit'] == 'N')
{
    echo $langvars['l_warp_forbid'] . "<br><br>";
    Bnt\Text::gotoMain ($db, $lang, $langvars);
    Bad\Footer::display($pdo_db, $lang, $bntreg, $template);
    die ();
}

if ($zoneinfo['allow_warpedit'] == 'L')
{
    $result3 = $db->Execute ("SELECT * FROM {$db->prefix}zones WHERE zone_id = ?;", array ($sectorinfo['zone_id']));
    Bnt\Db::logDbErrors ($db, $result3, __LINE__, __FILE__);
    $zoneowner_info = $result3->fields;

    $result5 = $db->Execute ("SELECT team FROM {$db->prefix}ships WHERE ship_id = ?;", array ($zoneowner_info['owner']));
    Bnt\Db::logDbErrors ($db, $result5, __LINE__, __FILE__);
    $zoneteam = $result5->fields;

    if ($zoneowner_info['owner'] != $playerinfo['ship_id'])
    {
        if (($zoneteam['team'] != $playerinfo['team']) || ($playerinfo['team'] == 0))
        {
            echo $langvars['l_warp_forbid'] . "<br><br>";
            Bnt\Text::gotoMain ($db, $lang, $langvars);
            Bad\Footer::display($pdo_db, $lang, $bntreg, $template);
            die ();
        }
    }
}

$result2 = $db->Execute ("SELECT * FROM {$db->prefix}links WHERE link_start = ? ORDER BY link_dest ASC;", array ($playerinfo['sector']));
Bnt\Db::logDbErrors ($db, $result2, __LINE__, __FILE__);
if (!$result2 instanceof ADORecordSet)
{
    echo $langvars['l_warp_nolink'] . "<br><br>";
}
else
{
    echo $langvars['l_warp_linkto'] ." ";
    while (!$result2->EOF)
    {
        echo $result2->fields['link_dest'] . " ";
        $result2->MoveNext();
    }
    echo "<br><br>";
}

echo "<form action=\"warpedit2.php\" method=\"post\">";
echo "<table>";
echo "<tr><td>" . $langvars['l_warp_query'] . "</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"\"></td></tr>";
echo "<tr><td>" . $langvars['l_warp_oneway'] . "?</td><td><input type=\"checkbox\" name=\"oneway\" value=\"oneway\"></td></tr>";
echo "</table>";
echo "<input type=\"submit\" value=\"" . $langvars['l_submit'] . "\"><input type=\"reset\" value=\"" . $langvars['l_reset'] . "\">";
echo "</form>";
echo "<br><br>" . $langvars['l_warp_dest'] . "<br><br>";
echo "<form action=\"warpedit3.php\" method=\"post\">";
echo "<table>";
echo "<tr><td>" . $langvars['l_warp_destquery'] . "</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"\"></td></tr>";
echo "<tr><td>" . $langvars['l_warp_bothway'] . "?</td><td><input type=\"checkbox\" name=\"bothway\" value=\"bothway\"></td></tr>";
echo "</table>";
echo "<input type=\"submit\" value=\"" . $langvars['l_submit'] . "\"><input type=\"reset\" value=\"" . $langvars['l_reset'] . "\">";
echo "</form>";

Bnt\Text::gotoMain ($db, $lang, $langvars);
Bad\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
