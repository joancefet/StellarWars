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
// File: admin/sector_editor.php

if (strpos($_SERVER['PHP_SELF'], 'sector_editor.php')) // Prevent direct access to this file
{
    $error_file = $_SERVER['SCRIPT_NAME'];
    include_once './error.php';
}

$variables['operation'] = '';

if (!isset($_POST['sector']))
{
    $_POST['sector'] = '';
}

if (!isset($_POST['operation']))
{
    $_POST['operation'] = '';
}

$variables['sector'] = $_POST['sector'];
if ($_POST['sector'] == '')
{
    $res = $db->Execute("SELECT sector_id FROM {$db->prefix}universe ORDER BY sector_id");
    Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
    while (!$res->EOF)
    {
        $sectors[] = $res->fields;
        $res->MoveNext();
    }
    $variables['sectors'] = $sectors;
}
else
{
    if ($_POST['operation'] == '')
    {
        $res = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id = ?;", array ($_POST['sector']));
        Bnt\Db::logDbErrors($db, $res, __LINE__, __FILE__);
        $row = $res->fields;

        $variables['sector_name'] = $row['sector_name'];

        $ressubb = $db->Execute("SELECT zone_id,zone_name FROM {$db->prefix}zones ORDER BY zone_name");
        Bnt\Db::logDbErrors($db, $ressubb, __LINE__, __FILE__);
        while (!$ressubb->EOF)
        {
            $rowsubb = $ressubb->fields;
            if ($rowsubb['zone_id'] == $row['zone_id'])
            {
                $variables['selected_zone'] = $rowsubb['zone_id'];
            }
            $zones[] = $rowsubb;
            $ressubb->MoveNext();
        }

        $variables['zones'] = $zones;
        $variables['beacon'] = $row['beacon'];
        $variables['distance'] = $row['distance'];
        $variables['angle1'] = $row['angle1'];
        $variables['angle2'] = $row['angle2'];
        $variables['port_organics'] = $row['port_organics'];
        $variables['port_ore'] = $row['port_ore'];
        $variables['port_goods'] = $row['port_goods'];
        $variables['port_energy'] = $row['port_energy'];
        $variables['sector'] = $row['sector_id'];

/*        $oportnon = $oportspe = $oportorg = $oportore = $oportgoo = $oportene = "value";
        if ($row['port_type'] == "none") $oportnon = "selected='none' value";
        if ($row['port_type'] == "special") $oportspe = "selected='special' value";
        if ($row['port_type'] == "organics") $oportorg = "selected='organics' value";
        if ($row['port_type'] == "ore") $oportore = "selected='ore' value";
        if ($row['port_type'] == "goods") $oportgoo = "selected='goods' value";
        if ($row['port_type'] == "energy") $oportene = "selected='energy' value";
        echo "<option $oportnon='none'>" . $langvars['l_none'] . "</option>";
        echo "<option $oportspe='special'>" . $langvars['l_special'] . "</option>";
        echo "<option $oportorg='organics'>" . $langvars['l_organics'] . "</option>";
        echo "<option $oportore='ore'>" . $langvars['l_ore'] . "</option>";
        echo "<option $oportgoo='goods'>" . $langvars['l_goods'] . "</option>";
        echo "<option $oportene='energy'>" . $langvars['l_energy'] . "</option>";*/
    }
    elseif ($_POST['operation'] == "save")
    {
        // Update database
        $secupdate = $db->Execute("UPDATE {$db->prefix}universe SET sector_name=?, zone_id=?, beacon=?, port_type=?, port_organics=?, port_ore=?, port_goods=?, port_energy=?, distance=?, angle1=?, angle2=? WHERE sector_id=?;", array ($_POST['sector_name'], $_POST['zone_id'], $_POST['beacon'], $_POST['port_type'], $_POST['port_organics'], $_POST['port_ore'], $_POST['port_goods'], $_POST['port_energy'], $_POST['distance'], $_POST['angle1'], $_POST['angle2'], $_POST['sector']));
        Bnt\Db::logDbErrors($db, $secupdate, __LINE__, __FILE__);

        if (!$secupdate)
        {
            $variables['secupdate'] = false;
            $variables['db_error_msg'] = $db->ErrorMsg();
        }
        else
        {
            $variables['secupdate'] = true;
        }

        $variables['button_main'] = false;
        $variables['operation'] = 'save';
    }
}

$variables['lang'] = $lang;
$variables['swordfish'] = $swordfish;

// Set the module name.
$variables['module'] = $module_name;

// Now set a container for the variables and langvars and send them off to the template system
$variables['container'] = "variable";
$langvars['container'] = "langvar";

$template->addVariables('langvars', $langvars);
$template->addVariables('variables', $variables);
?>
