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
// File: navcomp.php

include './global_includes.php';

Bnt\Login::checkLogin ($db, $pdo_db, $lang, $langvars, $bntreg, $template);

// Database driven language entries
$langvars = Bnt\Translate::load ($db, $lang, array ('navcomp', 'common', 'global_includes', 'global_funcs', 'footer'));
$title = $langvars['l_nav_title'];
Bnt\Header::display($db, $lang, $template, $title);

echo "<h1>" . $title . "</h1>\n";

if (!$bntreg->allow_navcomp)
{
    echo $langvars['l_nav_nocomp'] . '<br><br>';
    Bnt\Text::gotoMain ($db, $lang, $langvars);
    Bad\Footer::display($pdo_db, $lang, $bntreg, $template);
    die ();
}

$state = (int) filter_input (INPUT_POST, 'state', FILTER_SANITIZE_NUMBER_INT);
$stop_sector = (int) filter_input (INPUT_POST, 'stop_sector', FILTER_SANITIZE_NUMBER_INT);

$result = $db->Execute ("SELECT * FROM {$db->prefix}ships WHERE email = ?;", array ($_SESSION['username']));
Bnt\Db::logDbErrors ($db, $result, __LINE__, __FILE__);
$playerinfo = $result->fields;

$current_sector = $playerinfo['sector'];
$computer_tech  = $playerinfo['computer'];

$result2 = $db->Execute ("SELECT * FROM {$db->prefix}universe WHERE sector_id = ?;", array ($current_sector));
Bnt\Db::logDbErrors ($db, $result2, __LINE__, __FILE__);
$sectorinfo = $result2->fields;

if ($state == 0)
{
    echo "<form action=\"navcomp.php\" method=post>";
    echo $langvars['l_nav_query'] . " <input name=\"stop_sector\">&nbsp;<input type=submit value=" . $langvars['l_submit'] . "><br>\n";
    echo "<input name=\"state\" value=1 TYPE=HIDDEN>";
    echo "</form>\n";
}
elseif ($state == 1)
{
    if ($computer_tech < 5)
    {
        $max_search_depth = 2;
    }
    elseif ($computer_tech < 10)
    {
        $max_search_depth = 3;
    }
    elseif ($computer_tech < 15)
    {
        $max_search_depth = 4;
    }
    elseif ($computer_tech < 20)
    {
        $max_search_depth = 5;
    }
    else
    {
        $max_search_depth = 6;
    }

    for ($search_depth = 1; $search_depth <= $max_search_depth; $search_depth++)
    {
        $search_query = "SELECT distinct a1.link_start, a1.link_dest ";
        for ($i = 2; $i <= $search_depth;$i++)
        {
            $search_query = $search_query . " ,a". $i . ".link_dest ";
        }

        $search_query = $search_query . "FROM     {$db->prefix}links AS a1 ";

        for ($i = 2; $i <= $search_depth;$i++)
        {
            $search_query = $search_query . "    ,{$db->prefix}links AS a". $i . " ";
        }

        $search_query = $search_query . "WHERE         a1.link_start = $current_sector ";

        for ($i = 2; $i <= $search_depth; $i++)
        {
            $k = $i-1;
            $search_query = $search_query . "    AND a" . $k . ".link_dest = a" . $i . ".link_start ";
        }

        $search_query = $search_query . "    AND a" . $search_depth . ".link_dest = $stop_sector ";
        $search_query = $search_query . "    AND a1.link_dest != a1.link_start ";

        for ($i=2; $i <= $search_depth;$i++)
        {
            $search_query = $search_query . "    AND a" . $i . ".link_dest not in (a1.link_dest, a1.link_start ";

            for ($j=2; $j<$i;$j++)
            {
                $search_query = $search_query . ",a".$j.".link_dest ";
            }
            $search_query = $search_query . ")";
        }

        $search_query = $search_query . "ORDER BY a1.link_start, a1.link_dest ";
        for ($i=2;$i <= $search_depth;$i++)
        {
            $search_query = $search_query . ", a" . $i . ".link_dest";
        }

        $search_query = $search_query . " LIMIT 1";
        //echo "$search_query\n\n";

        $db->SetFetchMode (ADODB_FETCH_NUM);

        $search_result = $db->Execute ($search_query) or die ("Invalid Query");
        Bnt\Db::logDbErrors ($db, $search_result, __LINE__, __FILE__);
        $found = $search_result->RecordCount();
        if ($found > 0)
        {
            break;
        }
    }

    if ($found > 0)
    {
        echo "<h3>" . $langvars['l_nav_pathfnd'] . "</h3>\n";
        $links = $search_result->fields;
        echo $links[0];
        for ($i = 1; $i < $search_depth + 1; $i++)
        {
            echo " >> " . $links[$i];
        }

        $db->SetFetchMode (ADODB_FETCH_ASSOC);

        echo "<br><br>";
        echo $langvars['l_nav_answ1'] . " " . $search_depth . " " . $langvars['l_nav_answ2'] . "<br><br>";
    }
    else
    {
        echo $langvars['l_nav_proper'] . "<br><br>";
    }
}

$db->SetFetchMode (ADODB_FETCH_ASSOC);

Bnt\Text::gotoMain ($db, $lang, $langvars);
Bad\Footer::display($pdo_db, $lang, $bntreg, $template);
?>
