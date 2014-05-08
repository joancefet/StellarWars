<?php

/**
 *  2Moons
 *  Copyright (C) 2011  Slaver
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
 * @author Slaver <slaver7@gmail.com>
 * @copyright 2009 Lucky <lucky@xgproyect.net> (XGProyecto)
 * @copyright 2011 Slaver <slaver7@gmail.com> (Fork/2Moons)
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @version 1.4 (2011-07-10)
 * @info $Id: ShowFlyingFleetPage.php 1913 2011-07-10 18:13:22Z slaver7 $
 * @link http://code.google.com/p/2moons/
 */

//if (!allowedTo(str_replace(array(dirname(__FILE__), '\\', '/', '.php'), '', __FILE__))) exit;

require_once(ROOT_PATH. 'includes/classes/class.FlyingFleetsTable.php');

function ShowFlyingFleetPageAlly()
{
	global $LNG, $db, $USER;
	
	if ($USER['ally_id'] == 0) redirectTo("game.php?page=alliance");
	
//	$id	= request_var('id', 0);
	/*if(!empty($id)){
		$db->query("UPDATE ".FLEETS." SET `fleet_busy` = '".request_var('lock', 0)."' WHERE `fleet_id` = '".$id."' AND `fleet_universe` = '".$_SESSION['adminuni']."';;");
	} */

	$FlyingFleetsTable 	= new FlyingFleetsTable();
	$FlyingFleetsTable2 = new FlyingFleetsTable();
	$template			= new template();
	

	$template->assign_vars(array(
		'FleetList'		=> $FlyingFleetsTable->BuildFlyingFleetTableAllyC(),
		'FleetList2'		=> $FlyingFleetsTable2->BuildFlyingFleetTableAllyS(),
		'ta_beginning'		=> $LNG['ta_beginning'],
		'ta_departure'		=> $LNG['ta_departure'],
		'ta_departure_hour'	=> $LNG['ta_departure_hour'],
		'ta_objective'		=> $LNG['ta_objective'],
		'ta_arrival'		=> $LNG['ta_arrival'],
		'ta_arrival_hour'	=> $LNG['ta_arrival_hour'],
	));
	$template->show('FlyingFleetPageAlly.tpl');
}
?>
