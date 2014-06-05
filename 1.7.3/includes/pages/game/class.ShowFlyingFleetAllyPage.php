<?php

/**
 *  2Moons
 *  Copyright (C) 2012 Jan Kröpke
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package 2Moons
 * @author Jan Kröpke <info@2moons.cc>
 * @copyright 2012 Jan Kröpke <info@2moons.cc>
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.7.3 (2013-05-19)
 * @info $Id: ShowFlyingFleetPage.php 2749 2013-05-19 11:43:20Z slaver7 $
 * @link http://2moons.cc/
 */

/*if (!allowedTo(str_replace(array(dirname(__FILE__), '\\', '/', '.php'), '', __FILE__))) throw new Exception("Permission error!");*/

class ShowFlyingFleetAllyPage extends AbstractPage {

public static $requireModule = 0;
	
function __construct() 
{
	parent::__construct();
	require('includes/classes/class.FlyingFleetsTable.php');
		
}

public function show()
{
	global $LNG, $USER;
	
	/*$id	= HTTP::_GP('id', 0);
	if(!empty($id)){
		$lock	= HTTP::_GP('lock', 0);
		$GLOBALS['DATABASE']->query("UPDATE ".FLEETS." SET `fleet_busy` = '".$lock."' WHERE `fleet_id` = '".$id."' AND `fleet_universe` = '".$_SESSION['adminuni']."';");
		
		$SQL	= ($lock == 0) ? "NULL" : "'ADM_LOCK'";
		
		$GLOBALS['DATABASE']->query("UPDATE ".FLEETS_EVENT." SET `lock` = ".$SQL." WHERE `fleetID` = ".$id.";");
	}*/

	// Chegada
	$fleetResult	= $GLOBALS['DATABASE']->query("SELECT 
	fleet.*,
	event.`lock`,
	COUNT(event.fleetID) as error,
	pstart.name as startPlanetName,
	ptarget.name as targetPlanetName,
	ustart.username as startUserName,
	utarget.username as targetUserName,
	acs.name as acsName
	FROM ".FLEETS." fleet
	LEFT JOIN ".FLEETS_EVENT." event ON fleetID = fleet_id
	LEFT JOIN ".PLANETS." pstart ON pstart.id = fleet_start_id
	LEFT JOIN ".PLANETS." ptarget ON ptarget.id = fleet_end_id
	LEFT JOIN ".USERS." ustart ON ustart.id = fleet_owner
	LEFT JOIN ".USERS." utarget ON utarget.id = fleet_target_owner
	LEFT JOIN ".AKS." acs ON acs.id = fleet_group
	WHERE fleet_universe = ".$USER['universe']."
	AND fleet_ally = ".$USER['ally_id']."
	AND fleet_mission > '0'
	AND fleet_mission < '3'
	AND fleet_start_time > ".TIMESTAMP."
	GROUP BY event.fleetID
	ORDER BY fleet_start_time ASC;");
	
	$FleetList	= array();
	
	while($fleetRow = $GLOBALS['DATABASE']->fetch_array($fleetResult)) {
		$shipList		= array();
		$shipArray		= array_filter(explode(';', $fleetRow['fleet_array']));
		foreach($shipArray as $ship) {
			$shipDetail		= explode(',', $ship);
			$shipList[$shipDetail[0]]	= $shipDetail[1];
		}
		
		$FleetList[]	= array(
			'fleetID'				=> $fleetRow['fleet_id'],
			'lock'					=> !empty($fleetRow['lock']),
			'count'					=> $fleetRow['fleet_amount'],
			'error'					=> !$fleetRow['error'],
			'ships'					=> $shipList,
			'state'					=> $fleetRow['fleet_mess'],
			'starttime'				=> _date($LNG['php_tdformat'], $fleetRow['start_time'], $USER['timezone']),
			'arrivaltime'			=> _date($LNG['php_tdformat'], $fleetRow['fleet_start_time'], $USER['timezone']),
			'stayhour'				=> round(($fleetRow['fleet_end_stay'] - $fleetRow['fleet_start_time']) / 3600),
			'staytime'				=> $fleetRow['fleet_start_time'] !== $fleetRow['fleet_end_stay'] ? _date($LNG['php_tdformat'], $fleetRow['fleet_end_stay'], $USER['timezone']) : 0,
			'endtime'				=> _date($LNG['php_tdformat'], $fleetRow['fleet_end_time'], $USER['timezone']),
			'missionID'				=> $fleetRow['fleet_mission'],
			'acsID'					=> $fleetRow['fleet_group'],
			'acsName'				=> $fleetRow['acsName'],
			'startUserID'			=> $fleetRow['fleet_owner'],
			'startUserName'			=> $fleetRow['startUserName'],
			'startPlanetID'			=> $fleetRow['fleet_start_id'],
			'startPlanetName'		=> $fleetRow['startPlanetName'],
			'startPlanetGalaxy'		=> $fleetRow['fleet_start_galaxy'],
			'startPlanetSystem'		=> $fleetRow['fleet_start_system'],
			'startPlanetPlanet'		=> $fleetRow['fleet_start_planet'],
			'startPlanetType'		=> $fleetRow['fleet_start_type'],
			'targetUserID'			=> $fleetRow['fleet_target_owner'],
			'targetUserName'		=> $fleetRow['targetUserName'],
			'targetPlanetID'		=> $fleetRow['fleet_end_id'],
			'targetPlanetName'		=> $fleetRow['targetPlanetName'],
			'targetPlanetGalaxy'	=> $fleetRow['fleet_end_galaxy'],
			'targetPlanetSystem'	=> $fleetRow['fleet_end_system'],
			'targetPlanetPlanet'	=> $fleetRow['fleet_end_planet'],
			'targetPlanetType'		=> $fleetRow['fleet_end_type'],
			'resource'				=> array(
				901	=> $fleetRow['fleet_resource_metal'],
				902	=> $fleetRow['fleet_resource_crystal'],
				903	=> $fleetRow['fleet_resource_deuterium'],
				921	=> $fleetRow['fleet_resource_darkmatter'],
			),
		);
	}
	
	$GLOBALS['DATABASE']->free_result($fleetResult);
	
	// Saída
	$fleetResult2	= $GLOBALS['DATABASE']->query("SELECT 
	fleet.*,
	event.`lock`,
	COUNT(event.fleetID) as error,
	pstart.name as startPlanetName,
	ptarget.name as targetPlanetName,
	ustart.username as startUserName,
	utarget.username as targetUserName,
	acs.name as acsName
	FROM ".FLEETS." fleet
	LEFT JOIN ".FLEETS_EVENT." event ON fleetID = fleet_id
	LEFT JOIN ".PLANETS." pstart ON pstart.id = fleet_start_id
	LEFT JOIN ".PLANETS." ptarget ON ptarget.id = fleet_end_id
	LEFT JOIN ".USERS." ustart ON ustart.id = fleet_owner
	LEFT JOIN ".USERS." utarget ON utarget.id = fleet_target_owner
	LEFT JOIN ".AKS." acs ON acs.id = fleet_group
	WHERE fleet_universe = ".$USER['universe']."
	AND fleet_ally_owner = ".$USER['ally_id']."
	AND fleet_mission > '0'
	AND fleet_mission < '3'
	AND fleet_start_time > ".TIMESTAMP."
	GROUP BY event.fleetID
	ORDER BY fleet_start_time ASC;");
	
	$FleetList2	= array();
	
	while($fleetRow2 = $GLOBALS['DATABASE']->fetch_array($fleetResult2)) {
		$shipList		= array();
		$shipArray		= array_filter(explode(';', $fleetRow['fleet_array']));
		foreach($shipArray as $ship) {
			$shipDetail		= explode(',', $ship);
			$shipList[$shipDetail[0]]	= $shipDetail[1];
		}
		
		$FleetList2[]	= array(
			'fleetID'				=> $fleetRow2['fleet_id'],
			'lock'					=> !empty($fleetRow2['lock']),
			'count'					=> $fleetRow2['fleet_amount'],
			'error'					=> !$fleetRow2['error'],
			'ships'					=> $shipList,
			'state'					=> $fleetRow2['fleet_mess'],
			'starttime'				=> _date($LNG['php_tdformat'], $fleetRow2['start_time'], $USER['timezone']),
			'arrivaltime'			=> _date($LNG['php_tdformat'], $fleetRow2['fleet_start_time'], $USER['timezone']),
			'stayhour'				=> round(($fleetRow2['fleet_end_stay'] - $fleetRow2['fleet_start_time']) / 3600),
			'staytime'				=> $fleetRow2['fleet_start_time'] !== $fleetRow2['fleet_end_stay'] ? _date($LNG['php_tdformat'], $fleetRow2['fleet_end_stay'], $USER['timezone']) : 0,
			'endtime'				=> _date($LNG['php_tdformat'], $fleetRow2['fleet_end_time'], $USER['timezone']),
			'missionID'				=> $fleetRow2['fleet_mission'],
			'acsID'					=> $fleetRow2['fleet_group'],
			'acsName'				=> $fleetRow2['acsName'],
			'startUserID'			=> $fleetRow2['fleet_owner'],
			'startUserName'			=> $fleetRow2['startUserName'],
			'startPlanetID'			=> $fleetRow2['fleet_start_id'],
			'startPlanetName'		=> $fleetRow2['startPlanetName'],
			'startPlanetGalaxy'		=> $fleetRow2['fleet_start_galaxy'],
			'startPlanetSystem'		=> $fleetRow2['fleet_start_system'],
			'startPlanetPlanet'		=> $fleetRow2['fleet_start_planet'],
			'startPlanetType'		=> $fleetRow2['fleet_start_type'],
			'targetUserID'			=> $fleetRow2['fleet_target_owner'],
			'targetUserName'		=> $fleetRow2['targetUserName'],
			'targetPlanetID'		=> $fleetRow2['fleet_end_id'],
			'targetPlanetName'		=> $fleetRow2['targetPlanetName'],
			'targetPlanetGalaxy'	=> $fleetRow2['fleet_end_galaxy'],
			'targetPlanetSystem'	=> $fleetRow2['fleet_end_system'],
			'targetPlanetPlanet'	=> $fleetRow2['fleet_end_planet'],
			'targetPlanetType'		=> $fleetRow2['fleet_end_type'],
			'resource'				=> array(
				901	=> $fleetRow2['fleet_resource_metal'],
				902	=> $fleetRow2['fleet_resource_crystal'],
				903	=> $fleetRow2['fleet_resource_deuterium'],
				921	=> $fleetRow2['fleet_resource_darkmatter'],
			),
		);
	}
	
	
	$GLOBALS['DATABASE']->free_result($fleetResult2);
	
	$this->tplObj->assign_vars(array(
		'FleetList'			=> $FleetList,
		'FleetList2'		=> $FleetList2,
	));
	
	$this->display('page.flyingfleetally.tpl');
	
}
}