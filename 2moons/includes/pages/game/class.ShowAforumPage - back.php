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
 * @info $Id: ShowSupportPage.php 1913 2011-07-10 18:13:22Z slaver7 $
 * @link http://code.google.com/p/2moons/
 */

class ShowAforumPage 
{
	public function ShowAforumPage()
	{
		$action 		= request_var('action', "");
		$id 			= request_var('id', 0);
		
		$PlanetRess = new ResourceUpdate();
		$PlanetRess->CalcResource();
		$PlanetRess->SavePlanetToDB();
		
		switch($action){
			case 'newticket':
				$this->CreateTopic();
			break;
			case 'send':
				$this->UpdateTopic($id);
			break;
			case 'open':
				$this->OpenTopic($id);
			break;
			case 'close':
				$this->CloseTopic($id);
			break;
			case 'delete':
				$this->DeleteTopic($id);
			break;
			default:
				$this->ShowAforumTopics();
			break;
		}
	}
	
	private function CreateTopic()
	{
		global $USER, $UNI, $db, $LNG;
		
		$subject = request_var('subject','',true);
		$text 	 = makebr(request_var('text','',true));

		$template	= new template();
		
		if(empty($text) || empty($subject) || $_SESSION['aforumtoken'] != $USER['id']) {
			$template->message($LNG['af_sendit_error_msg'], "game.php?page=aforum", 3);
			exit;
		}
		
		$SQL  = "INSERT ".AFORUM." SET ";
		$SQL .= "`player_id` = '". $USER['id'] ."',";
		$SQL .= "`subject` = '". $GLOBALS['DATABASE']->sql_escape($subject) ."',";
		$SQL .= "`text` = '" .$GLOBALS['DATABASE']->sql_escape($text) ."',";
		$SQL .= "`time` = '". TIMESTAMP ."',";
		$SQL .= "`status` = '1',";
		$SQL .= "`universe` = '".$UNI."',";
		$SQL .= "`ally_id` = '". $USER['ally_id'] ."';";
		$GLOBALS['DATABASE']->query($SQL);
		
		$template->message($LNG['af_sendit_t'], "game.php?page=aforum", 2);
	}
	
	private function UpdateTopic($TicketID) 
	{
		global $USER, $db, $LNG;
		
		$text = request_var('text','',true);
		$template	= new template();	
		if(empty($text) || $_SESSION['aforumtoken'] != $USER['id'])
			exit($template->message($LNG['af_sendit_error_msg'],"game.php?page=aforum", 3));
		
		$ticket = $GLOBALS['DATABASE']->uniquequery("SELECT text FROM ".AFORUM." WHERE `id` = '".$TicketID."';");

		$text 	= $ticket['text'].'<br><br><hr>'.sprintf($LNG['af_player_write'], $USER['username'], tz_date(TIMESTAMP)).'<br><br>'.makebr($text).'';
		$GLOBALS['DATABASE']->query("UPDATE ".AFORUM." SET `text` = '".$GLOBALS['DATABASE']->sql_escape($text) ."', `time` = '". TIMESTAMP ."', lastpostplayer = '".$USER['username']."' WHERE `id` = '". $GLOBALS['DATABASE']->sql_escape($TicketID) ."';");
		$template->message($LNG['af_sendit_a'],"game.php?page=aforum", 2);
	}
	
	private function CloseTopic($TicketID)
	{
		global $USER, $db, $LNG;
		
		$template	= new template();
		$ticket = $GLOBALS['DATABASE']->uniquequery("SELECT text FROM ".AFORUM." WHERE `id` = '".$TicketID."';");
		//$newtext = $ticket['text'].'<br><br><hr>'.sprintf($LNG['sp_admin_closed'], $USER['username'], date(TDFORMAT, TIMESTAMP));
		$SQL  = "UPDATE ".AFORUM." SET ";
		//$SQL .= "`text` = '".$db->sql_escape($newtext)."',";
		$SQL .= "`status` = '0'";
		$SQL .= "WHERE ";
		$SQL .= "`id` = '".$TicketID."' ";
		$GLOBALS['DATABASE']->query($SQL);
		$template->message($LNG['af_close_t'],"game.php?page=aforum", 2);
	}
	
	private function DeleteTopic($TicketID)
	{
		global $USER, $LNG;
		
		$template = new template();
		$SQL .= "DELETE FROM ".AFORUM." WHERE `id` = '".$TicketID."';";
		$GLOBALS['DATABASE']->query($SQL);
		$template->message($LNG['af_delete_t'],"game.php?page=aforum", 2);
	}
	
	private function OpenTopic($TicketID)
	{
		global $USER, $db, $LNG;
		
		$template = new template();
		$ticket = $GLOBALS['DATABASE']->uniquequery("SELECT text FROM ".AFORUM." WHERE `id` = '".$TicketID."';");
		//$newtext = $ticket['text'].'<br><br><hr>'.sprintf($LNG['sp_admin_closed'], $USER['username'], date(TDFORMAT, TIMESTAMP));
		$SQL  = "UPDATE ".AFORUM." SET ";
		//$SQL .= "`text` = '".$db->sql_escape($newtext)."',";
		$SQL .= "`status` = '1'";
		$SQL .= "WHERE ";
		$SQL .= "`id` = '".$TicketID."' ";
		$GLOBALS['DATABASE']->query($SQL);
		$template->message($LNG['af_open_t'],"game.php?page=aforum", 2);
	}
	
	private function ShowAforumTopics()
	{
		global $USER, $PLANET, $LNG;
		
		if ($USER['authlevel'] >= 2) {		
			$query			= $GLOBALS['DATABASE']->query("SELECT ID,player_id,time,text,subject,status,lastpostplayer FROM ".AFORUM." WHERE `ally_id` > '0' ORDER BY time DESC;"); 
		} else {
			if ($USER['ally_id'] == 0) redirectTo("game.php?page=alliance");
			$query			= $GLOBALS['DATABASE']->query("SELECT ID,player_id,time,text,subject,status,lastpostplayer FROM ".AFORUM." WHERE `ally_id` = '".$USER['ally_id']."' ORDER BY time DESC;"); 
		}
		
		$TicketsList	= array();
		while($ticket = $GLOBALS['DATABASE']->fetch_array($query)){	
			
			$ticket3 = $GLOBALS['DATABASE']->uniquequery("SELECT username FROM ".USERS." WHERE `id` = '".$ticket['player_id']."';");
			//$ticket3 = $db->fetch_array($query3);
			$player_post = $ticket3['username'];
			
			if ( $USER['lastaforum'] < $ticket['time'] ) { 
			$TicketsList[$ticket['ID']]	= array(
				'af_status'	=> $ticket['status'],
				'af_subject'	=> $ticket['subject'],
				'af_date'		=> tz_date($ticket['time']),
				'af_text'		=> html_entity_decode($ticket['text'], ENT_NOQUOTES, "UTF-8"),
				'af_lastpostplayer'	=> $ticket['lastpostplayer'],
				'af_lastpostaction'	=> 1,
				'af_player_post'	=> $player_post,
				'af_player_id'		=> $ticket['player_id'],
				'af_user_id'		=> $USER['id'],
			);
			} else {
			$TicketsList[$ticket['ID']]	= array(
				'af_status'	=> $ticket['status'],
				'af_subject'	=> $ticket['subject'],
				'af_date'		=> tz_date($ticket['time']),
				'af_text'		=> html_entity_decode($ticket['text'], ENT_NOQUOTES, "UTF-8"),
				'af_lastpostplayer'	=> $ticket['lastpostplayer'],
				'af_lastpostaction'	=> 0,
				'af_player_post'	=> $player_post,
				'af_player_id'		=> $ticket['player_id'],
				'af_user_id'		=> $USER['id'],
			);
			}
			
		}
		$query2	= $GLOBALS['DATABASE']->query("SELECT * FROM ".AFORUM." WHERE `ally_id` = '".$USER['ally_id']."' AND `player_id` = '".$USER['id']."';");
		$ticket2 = $GLOBALS['DATABASE']->fetch_array($query2);
		$aff = $ticket2['player_id'];
		//$player_post_id = $ticket2['player_id'];
		//$query3 = $db->query("SELECT username FROM ".USERS." WHERE `id` = '".$player_post_id."';");
		//$ticket3 = $db->fetch_array($query3);
		//$player_post = $ticket3['username'];
		if ($aff == $USER['id']) { $actions = 1; } else { $actions = 0; }

		
		
		$_SESSION['aforumtoken']	= $USER['id'];
		$GLOBALS['DATABASE']->free_result($query);
		$GLOBALS['DATABASE']->free_result($query2);
		$template	= new template();
		$template->loadscript('aforum.js');
		

		
		$template->assign_vars(array(	
			'TicketsList'			=> $TicketsList,
			'af_text'					=> $LNG['af_text'],
			'af_header'			=> $LNG['af_header'],
			'af_ticket_id'				=> $LNG['af_ticket_id'],
			'af_subject'				=> $LNG['af_subject'],
			'af_status'				=> $LNG['af_status'],
			'af_ticket_posted'			=> $LNG['af_ticket_posted'],
			'af_send'				=> $LNG['af_send'],
			'af_close'			=> $LNG['af_close'],
			'af_open'				=> $LNG['af_open'],
			'af_admin_answer'		=> $LNG['af_admin_answer'],
			'af_player_answer'	=> $LNG['af_player_answer'],
			'af_ticket_close'		=> $LNG['af_ticket_close'],	
			'af_subject'				=> $LNG['af_subject'],
			'af_status'				=> $LNG['af_status'],
			'af_ticket_new'			=> $LNG['af_ticket_new'],
			'actions'			=> $actions,
		));
			
		$template->show("aforum_overview.tpl");
		$GLOBALS['DATABASE']->query("UPDATE ".USERS." SET `lastaforum` = '". TIMESTAMP ."' WHERE `id` = '".$USER['id']."';");
	}
}
?>