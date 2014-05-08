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
 * @info $Id: ShowBannedPage.php 1913 2011-07-10 18:13:22Z slaver7 $
 * @link http://code.google.com/p/2moons/
 */

//require_once(ROOT_PATH . 'includes/classes/class.FleetFunctions.php');

function ShowFederacaoPage()
{
	global $USER, $PLANET, $LNG, $db, $UNI;
	
	$PlanetRess = new ResourceUpdate();
	$PlanetRess->CalcResource();
	$PlanetRess->SavePlanetToDB();

	$mode		= request_var('mode', '');
	$template	= new template();
	
	
	switch($mode){
	case'm1':
		if ($PLANET['planet_type'] != 1) {
			$template->message($LNG['federacao_help_fail2'], "?page=federacao", 5);
				exit;
		}
		if ($USER['lasthelpfed'] > TIMESTAMP + 432000) {
			$template->message($LNG['federacao_help_fail3'], "?page=federacao", 5);
				exit;
		}
		
		$numfleet = $db->countquery("SELECT COUNT(*) FROM ".FLEETS." WHERE `fleet_owner` = '".FEDERACAO_ID."' AND `fleet_target_owner` = '".$USER['id']."' AND `fleet_mission` = '5'");
		
		if($numfleet > 0) {
  				$template->message($LNG['federacao_help_fail4'], "?page=overview", 5);
				exit;
  			}
		
	$num = $db->countquery("SELECT COUNT(*) FROM ".TOPKB." WHERE `id_owner2` = '".$PLANET['id_owner']."' AND `time` > ".TIMESTAMP." - ".BASHING_MIN_TIME."");
	$TargetPlanetAllyid    = $db->uniquequery("SELECT `ally_id` FROM ".USERS." WHERE `id` = '".$USER['id']."';");
		$timefinal = TIMESTAMP + 86400 + 28800;
		$timestart = TIMESTAMP + 14400;
		$timestayfinal = TIMESTAMP + 86400 + 14400;
		
			if($num >= FEDERACAO_VALOR) {
				$SQL = "INSERT INTO ".FLEETS." SET ";
                		$SQL .= "`fleet_owner` = '".FEDERACAO_ID."', ";
                		$SQL .= "`fleet_ally` = '".$TargetPlanetAllyid['ally_id']."', ";
                		$SQL .= "`fleet_mission` = '5', ";
                		$SQL .= "`fleet_amount` = '5', ";
                		$SQL .= "`fleet_array` = '214,5', ";
               			$SQL .= "`fleet_universe` = '".$UNI."', ";
                		$SQL .= "`fleet_start_time` = '".$timestart."', ";
                		$SQL .= "`fleet_start_id` = '".FEDERACAO_PLANET_ID."', ";
                		$SQL .= "`fleet_start_galaxy` = '2', ";
                		$SQL .= "`fleet_start_system` = '1', ";
                		$SQL .= "`fleet_start_planet` = '1', ";
                		$SQL .= "`fleet_start_type` = '1', ";
                		$SQL .= "`fleet_end_time` = '".$timefinal."', ";
                		$SQL .= "`fleet_end_stay` = '".$timestayfinal."', ";
                		$SQL .= "`fleet_end_id` = '".$PLANET['id']."', ";
                		$SQL .= "`fleet_end_galaxy` = '".$PLANET['galaxy']."', ";
                		$SQL .= "`fleet_end_system` = '".$PLANET['system']."', ";
                		$SQL .= "`fleet_end_planet` = '".$PLANET['planet']."', ";
                		$SQL .= "`fleet_end_type` = '1', ";
                		$SQL .= "`fleet_target_owner` = '".$USER['id']."', ";
                		$SQL .= "`start_time` = '".TIMESTAMP."';";
                		$SQL .= "UPDATE ".USERS." SET ";
                		$SQL .= "`lasthelpfed` = '".$timefinal."' " ;
                		$SQL .= "WHERE ";
                		$SQL .= "`id` = '".$USER['id']."';";
                
                if(connection_aborted())
                        exit;
                        
                $db->multi_query($SQL);
                		

                		
  				$template->message($LNG['federacao_help_sucess'], "?page=overview", 5);
				exit;

  			} else {
  				$template->message($LNG['federacao_help_fail'], "?page=federacao", 5);
				exit;
				}
  			
  	break; 
  	$db->free_result($num);
  	}
	
	
	
	$template->assign_vars(array(	
		'fs_req1'				=> $LNG['fs_req1'],
		'fs_req2'				=> $LNG['fs_req2'],
		'fs_req3'				=> $LNG['fs_req3'],
		'fs_req4'				=> $LNG['fs_req4'],
	));
	
	$template->show("federacao.tpl");
}
?>
