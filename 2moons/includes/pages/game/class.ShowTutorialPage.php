<?php
/*


Funzione per chiamare il template $this->template->show("nome template");
Funzione per chiamare un parse nel template $this->template->assign_vars(array('nome' =>'x',)); nel template chiamare {$nome}
Livello 1 ok = Miniere = ?page=tutorial&mode=miniera
Livello 2 ok = Difese  = ?page=tutorial&mode=difesa
Livello 3 ok = Pianeta = ?page=tutorial&mode=pianeta
Livello 4 ok = Navi    = ?page=tutorial&mode=navi
Livello 5 ok = tec     = ?page=tutorial&mode=tec

*/
//if(!defined('INSIDE')) die('Hacking attempt!');


class ShowTutorialPage {

    public function ShowTutorialPage() {

	    global $LNG, $USER, $PLANET,  $db;

	
	    $mode		= request_var('mode', '');	
		$PlanetRess = new ResourceUpdate();
		$PlanetRess->CalcResource();
		$PlanetRess->SavePlanetToDB();
		$template	= new template();	
		$parse 		= $LNG;
		
		$template->assign_vars(array(
		'tut_welcome'				=> $LNG['tut_welcome'],
		'tut_welcom_desc'			=> $LNG['tut_welcom_desc'],
		'tut_welcom_desc2'			=> $LNG['tut_welcom_desc2'],
		'tut_welcom_desc3'			=> $LNG['tut_welcom_desc3'],
		'tut_welcom_desc4'			=> $LNG['tut_welcom_desc4'],
		'tut_welcom_desc5'			=> $LNG['tut_welcom_desc5'],
		'tut_go'				=> $LNG['tut_go'],
		'tut_go_to'				=> $LNG['tut_go_to'],
		'tut_m1'				=> $LNG['tut_m1'],
		'tut_m2'				=> $LNG['tut_m2'],
		'tut_m3'				=> $LNG['tut_m3'],
		'tut_m4'				=> $LNG['tut_m4'],
		'tut_m5'				=> $LNG['tut_m5'],
		'tut_m6'				=> $LNG['tut_m6'],
		'tut_m7'				=> $LNG['tut_m7'],
		'tut_m8'				=> $LNG['tut_m8'],
		'tut_m9'				=> $LNG['tut_m9'],
		'tut_objects'				=> $LNG['tut_objects'],
		'tut_m1_name'				=> $LNG['tut_m1_name'],
		'tut_m1_desc'				=> $LNG['tut_m1_desc'],
		'tut_m1_quest'				=> $LNG['tut_m1_quest'],
		'tut_m1_quest2'				=> $LNG['tut_m1_quest2'],
		'tut_m1_quest3'				=> $LNG['tut_m1_quest3'],
		'tut_m1_quest4'				=> $LNG['tut_m1_quest4'],
		'tut_m1_quest5'				=> $LNG['tut_m1_quest5'],
		'tut_m1_gain'				=> $LNG['tut_m1_gain'],
		'tut_m1_ready'				=> $LNG['tut_m1_ready'],
		'tut_m2_name'				=> $LNG['tut_m2_name'],
		'tut_m2_desc'				=> $LNG['tut_m2_desc'],
		'tut_m2_quest'				=> $LNG['tut_m2_quest'],
		'tut_m2_quest2'				=> $LNG['tut_m2_quest2'],
		'tut_m2_quest3'				=> $LNG['tut_m2_quest3'],
		'tut_m2_quest4'				=> $LNG['tut_m2_quest4'],
		'tut_m2_quest5'				=> $LNG['tut_m2_quest5'],
		'tut_m2_gain'				=> $LNG['tut_m2_gain'],
		'tut_m2_ready'				=> $LNG['tut_m2_ready'],
		'tut_m3_name'				=> $LNG['tut_m3_name'],
		'tut_m3_desc'				=> $LNG['tut_m3_desc'],
		'tut_m3_quest'				=> $LNG['tut_m3_quest'],
		'tut_m3_quest2'				=> $LNG['tut_m3_quest2'],
		'tut_m3_quest3'				=> $LNG['tut_m3_quest3'],
		'tut_m3_quest4'				=> $LNG['tut_m3_quest4'],
		'tut_m3_quest5'				=> $LNG['tut_m3_quest5'],
		'tut_m3_gain'				=> $LNG['tut_m3_gain'],
		'tut_m3_ready'				=> $LNG['tut_m3_ready'],
		'tut_m4_name'				=> $LNG['tut_m4_name'],
		'tut_m4_desc'				=> $LNG['tut_m4_desc'],
		'tut_m4_quest'				=> $LNG['tut_m4_quest'],
		'tut_m4_quest2'				=> $LNG['tut_m4_quest2'],
		'tut_m4_quest3'				=> $LNG['tut_m4_quest3'],
		'tut_m4_quest4'				=> $LNG['tut_m4_quest4'],
		'tut_m4_quest5'				=> $LNG['tut_m4_quest5'],
		'tut_m4_gain'				=> $LNG['tut_m4_gain'],
		'tut_m4_ready'				=> $LNG['tut_m4_ready'],
		'tut_m5_name'				=> $LNG['tut_m5_name'],
		'tut_m5_desc'				=> $LNG['tut_m5_desc'],
		'tut_m5_quest'				=> $LNG['tut_m5_quest'],
		'tut_m5_quest2'				=> $LNG['tut_m5_quest2'],
		'tut_m5_quest3'				=> $LNG['tut_m5_quest3'],
		'tut_m5_quest4'				=> $LNG['tut_m5_quest4'],
		'tut_m5_quest5'				=> $LNG['tut_m5_quest5'],
		'tut_m5_gain'				=> $LNG['tut_m5_gain'],
		'tut_m5_ready'				=> $LNG['tut_m5_ready'],
		'tut_m6_name'				=> $LNG['tut_m6_name'],
		'tut_m6_desc'				=> $LNG['tut_m6_desc'],
		'tut_m6_quest'				=> $LNG['tut_m6_quest'],
		'tut_m6_quest2'				=> $LNG['tut_m6_quest2'],
		'tut_m6_quest3'				=> $LNG['tut_m6_quest3'],
		'tut_m6_quest4'				=> $LNG['tut_m6_quest4'],
		'tut_m6_quest5'				=> $LNG['tut_m6_quest5'],
		'tut_m6_gain'				=> $LNG['tut_m6_gain'],
		'tut_m6_ready'				=> $LNG['tut_m6_ready'],
		'tut_m7_name'				=> $LNG['tut_m7_name'],
		'tut_m7_desc'				=> $LNG['tut_m7_desc'],
		'tut_m7_quest'				=> $LNG['tut_m7_quest'],
		'tut_m7_quest2'				=> $LNG['tut_m7_quest2'],
		'tut_m7_quest3'				=> $LNG['tut_m7_quest3'],
		'tut_m7_quest4'				=> $LNG['tut_m7_quest4'],
		'tut_m7_quest5'				=> $LNG['tut_m7_quest5'],
		'tut_m7_gain'				=> $LNG['tut_m7_gain'],
		'tut_m7_ready'				=> $LNG['tut_m7_ready'],
		'tut_m8_name'				=> $LNG['tut_m8_name'],
		'tut_m8_desc'				=> $LNG['tut_m8_desc'],
		'tut_m8_quest'				=> $LNG['tut_m8_quest'],
		'tut_m8_quest2'				=> $LNG['tut_m8_quest2'],
		'tut_m8_quest3'				=> $LNG['tut_m8_quest3'],
		'tut_m8_quest4'				=> $LNG['tut_m8_quest4'],
		'tut_m8_quest5'				=> $LNG['tut_m8_quest5'],
		'tut_m8_gain'				=> $LNG['tut_m8_gain'],
		'tut_m8_ready'				=> $LNG['tut_m8_ready'],
		'tut_m9_name'				=> $LNG['tut_m9_name'],
		'tut_m9_desc'				=> $LNG['tut_m9_desc'],
		'tut_m9_quest'				=> $LNG['tut_m9_quest'],
		'tut_m9_quest2'				=> $LNG['tut_m9_quest2'],
		'tut_m9_quest3'				=> $LNG['tut_m9_quest3'],
		'tut_m9_quest4'				=> $LNG['tut_m9_quest4'],
		'tut_m9_quest5'				=> $LNG['tut_m9_quest5'],
		'tut_m9_gain'				=> $LNG['tut_m9_gain'],
		'tut_m9_ready'				=> $LNG['tut_m9_ready'],
		'tut_compleat'				=> $LNG['tut_compleat'],
		
	));
			
	switch($mode){
	
	case'm1':
	if($USER['started_tut'] == 0)
	{
		$aendern=$db->query("UPDATE ".USERS." SET `started_tut`=started_tut+1 WHERE id=".$USER['id']."");
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m1'] >= 1){	
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				'livello1'=> $LNG['tut_ready'],
				));	
	$template->show('mission_1.tpl');		
	}else{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png">',
				'livello1'=> $LNG['tut_not_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	//Miniera di metallo
	if($PLANET['metal_mine'] >=4){
	$template->assign_vars(array(
				'Si_m1_1'=>'<img src="styles/images/gruener-haken.png" >',
				));
	}else{
	$template->assign_vars(array(
				'No_m1_1'=>'<img src="styles/images/roter-haken.png" >',
				));
	}
	//Miniera di cristallo
	if($PLANET['crystal_mine'] >=2){
	$template->assign_vars(array(
				'Si_m1_2'=>'<img src="styles/images/gruener-haken.png" >',
				));
	}else{
	$template->assign_vars(array(
				'No_m1_2'=>'<img src="styles/images/roter-haken.png" >',
				));
	}
	//Centrare solare
	if($PLANET['solar_plant'] >=4){
	$template->assign_vars(array(
				'Si_m1_3'=>'<img src="styles/images/gruener-haken.png" >',
				));
	}else{
	$template->assign_vars(array(
				'No_m1_3'=>'<img src="styles/images/roter-haken.png" >',
				));
	}
			
	if($USER['tut_m1']==0){
	if($PLANET['metal_mine'] >=4 && $PLANET['crystal_mine'] >=2 && $PLANET['solar_plant'] >=4){
	$db->query("UPDATE ".USERS." SET `tut_m1`=tut_m1+1 WHERE id=".$USER['id']."");
	$db->query("UPDATE ".USERS." SET `darkmatter`=darkmatter+200 WHERE id=".$USER['id']."");
	$template->message(sprintf($LNG['tut_m1_ready']), "?page=tutorial&mode=m2", 3);
	exit;
	}
	}
		
	$template->show('mission_1.tpl');
	//Fine else
	}

	
	break;
//------------------------------------------------MISSSION 2-----------------------------------------------------------------
	case'm2':
	if($USER['tut_m2'] >= 1){	
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png">',
				'livello2'=> $LNG['tut_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	$template->show('mission_2.tpl');	
	}else{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png">',
				'livello2'=> $LNG['tut_not_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	
	//Deuterio
	if($PLANET['deuterium_sintetizer'] >=2){
	$template->assign_vars(array(
				'Si_m2_1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m2_1'=>'<img src="styles/images/roter-haken.png" >',
				));	
				}
				
	//Fabrica
	if($PLANET['robot_factory'] >=2){
	$template->assign_vars(array(
				'Si_m2_2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m2_2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	//Cantiere
	}if($PLANET['hangar'] >=1){
	$template->assign_vars(array(
				'Si_m2_3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m2_3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	//Missili
	}if($PLANET['misil_launcher'] >=1){
	$template->assign_vars(array(
				'Si_m2_4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m2_4'=>'<img src="styles/images/roter-haken.png" >',
				));	
				}		
	
	
	if($USER['tut_m2']==0){
	if($PLANET['deuterium_sintetizer'] >=2 && $PLANET['robot_factory'] >=2 && $PLANET['hangar'] >=1&& $PLANET['misil_launcher'] >=1){
	$db->query("UPDATE ".USERS." SET `tut_m2`=tut_m2+1 WHERE id=".$USER['id']."");
	$db->query("UPDATE ".USERS." SET `darkmatter`=darkmatter+200 WHERE id=".$USER['id']."");
	$template->message(sprintf($LNG['tut_m2_ready']), "?page=tutorial&mode=m3", 3);
	exit;
	}
	}
		
	$template->show('mission_2.tpl');
	}
	//finde difesa
	break;	
//--------------------------------------------MISSSION 3-----------------------------------------------------------------------	
	case'm3':
	if($USER['tut_m3'] >= 1){	
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				'livello3'=> $LNG['tut_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	$template->show('mission_3.tpl');		
	}else{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png">',
				'livello3'=> $LNG['tut_not_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	//Metallo
	if($PLANET['metal_mine'] >=10){
	$template->assign_vars(array(
				'Si_m3_1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m3_1'=>'<img src="styles/images/roter-haken.png" >',
				));	
				}
				
	//Fabrica
	if($PLANET['crystal_mine'] >=7){
	$template->assign_vars(array(
				'Si_m3_2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m3_2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	//Cantiere
				}
	if($PLANET['deuterium_sintetizer'] >=5){
	$template->assign_vars(array(
				'Si_m3_3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m3_3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	
	
	if($USER['tut_m3']==0){
	if($PLANET['metal_mine'] >=10 && $PLANET['crystal_mine'] >=7 && $PLANET['deuterium_sintetizer'] >=5){
	$db->query("UPDATE ".USERS." SET `tut_m3`=tut_m3+1 WHERE id=".$USER['id']."");
	$db->query("UPDATE ".USERS." SET `darkmatter`=darkmatter+200 WHERE id=".$USER['id']."");
	$template->message(sprintf($LNG['tut_m3_ready']), "?page=tutorial&mode=m4", 3);
	exit;
	}
	}
		
	$template->show("mission_3.tpl");
	}
	//finde difesa
	break;
//-----------------------------------------------MISSSION 4----------------------------------------------------------------	
	case'm4':
	if($USER['tut_m4'] >= 1){	
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				'livello4'=> $LNG['tut_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	$template->show('mission_4.tpl');		
	}else{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png">',
				'livello4'=> $LNG['tut_not_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	//Laboratorio
	if($PLANET['laboratory'] >=1){
	$template->assign_vars(array(
				'Si_m4_1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m4_1'=>'<img src="styles/images/roter-haken.png" >',
				));	
				}	
	//Hangar
	if($PLANET['hangar'] >=2){
	$template->assign_vars(array(
				'Si_m4_2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m4_2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	//Cantiere
	if($USER['combustion_tech'] >=2){
	$template->assign_vars(array(
				'Si_m4_3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m4_3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	
	//Carco
	if($PLANET['small_ship_cargo'] >=1){
	$template->assign_vars(array(
				'Si_m4_4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m4_4'=>'<img src="styles/images/roter-haken.png" >',
				));
	}
	
	
	if($USER['tut_m4']==0){
	if($PLANET['laboratory'] >=1 &&  $PLANET['hangar'] >=2 && $USER['combustion_tech'] >=2 && $PLANET['small_ship_cargo'] >=1){
	$db->query("UPDATE ".USERS." SET `tut_m4`=tut_m4+1 WHERE id=".$USER['id']."");
	$db->query("UPDATE ".USERS." SET `darkmatter`=darkmatter+200 WHERE id=".$USER['id']."");
	$template->message(sprintf($LNG['tut_m4_ready']), "?page=tutorial&mode=m5", 3);
	exit;
	}
	}
		
	$template->show('mission_4.tpl');
	}
	break;
//-------------------------------------------MISSSION 5----------------------------------------------------------------------
   case'm5':
   if($USER['tut_m5'] >=1){
   $template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				'livello5'=> $LNG['tut_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	$template->show('mission_5.tpl');
	}else{
	$template->assign_vars(array(
	  			'No5'=>'<img src="styles/images/roter-haken.png">',
				'livello5'=> $LNG['tut_not_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	//Gestione alleanza
	if($USER['ally_id'] > 0){
	$template->assign_vars(array(
				'Si_m5_1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m5_1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	
	$query = $db->uniquequery("SELECT * FROM ".BUDDY." WHERE `sender` = '". $USER['id'] ."';");
	if($query>1){
	$template->assign_vars(array(
				'Si_m5_2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m5_2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0){
	if($USER['ally_id'] > 0 && $query>1){
	$db->query("UPDATE ".USERS." SET `tut_m5`=tut_m5+1 WHERE id=".$USER['id']."");
	$db->query("UPDATE ".USERS." SET `darkmatter`=darkmatter+800 WHERE id=".$USER['id']."");
	$template->message(sprintf($LNG['tut_m5_ready']), "?page=tutorial&mode=m6", 3);
	exit;
	}
	}
	$template->show('mission_5.tpl');
   }
   break;
//------------------------------------------MISSSION 6--------------------------------------------------------------------
   	case'm6':
   	if($USER['tut_m6'] == 1){
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				'livello6'=> $LNG['tut_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	$template->show('mission_6.tpl');
	}else{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png">',
				'livello6'=> $LNG['tut_not_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if(	$PLANET['metal_store'] >= 1 or $PLANET['crystal_store'] >= 1 or $PLANET['deuterium_store'] >=1 ){
	$template->assign_vars(array(
				'Si_m6_1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m6_1'=>'<img src="styles/images/roter-haken.png" >',
				));	
}
if($USER['tut_m6_2'] >=0){
	$template->assign_vars(array(
				'Si_m6_2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m6_2'=>'<img src="styles/images/roter-haken.png" >',
				));	
}


//da fare il caso se viene scambiato il mercante
if($USER['tut_m6'] == 0){
	if($USER['tut_m6_2'] >= 0 && $PLANET['metal_store'] >= 1 or $PLANET['crystal_store'] >= 1 or $PLANET['deuterium_store'] >=1 ){
	$db->query("UPDATE ".USERS." SET `tut_m6`=tut_m6+1 WHERE id=".$USER['id']."");
	$db->query("UPDATE ".USERS." SET `darkmatter`=darkmatter+200 WHERE id=".$USER['id']."");
	$template->message(sprintf($LNG['tut_m6_ready']), "?page=tutorial&mode=m7", 3);
	exit;

}
}
$template->show('mission_6.tpl');
}
    break;
//--------------------------------------------------MISSSION 7--------------------------------------------------------------		
   case 'm7':
   if($USER['tut_m7'] >= 1){	
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				'livello7'=> $LNG['tut_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	$template->show('mission_7.tpl');	
	}else{	
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png">',
				'livello7'=> $LNG['tut_not_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
//Sonda
	if($PLANET['spy_sonde'] >=1){
	$template->assign_vars(array(
				'Si_m7_1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m7_1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	
	}
	//Flotta
	$query = $db->uniquequery("SELECT `fleet_mission` FROM ".FLEETS." WHERE `fleet_owner` = '". $USER['id'] ."' AND `fleet_mission` = '6';");
	if($query){
	$template->assign_vars(array(
				'Si_m7_2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	$db->query("UPDATE ".USERS." SET `tut_m7_2`=tut_m7_2+1 WHERE id=".$USER['id']."");
	}else{
	$template->assign_vars(array(
				'No_m7_2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	
	}
	if($USER['tut_m7'] == 0){
	if($PLANET['spy_sonde'] >=1 && $USER['tut_m7_2'] >= 1){
	$db->query("UPDATE ".USERS." SET `tut_m7`=tut_m7+1 WHERE id=".$USER['id']."");
	$db->query("UPDATE ".USERS." SET `darkmatter`=darkmatter+200 WHERE id=".$USER['id']."");
	$template->message(sprintf($LNG['tut_m7_ready']), "?page=tutorial&mode=m8", 3);
	break;
	}
	}
		
	$template->show('mission_7.tpl');
}
   break;
   
//----------------------------------------------------MISSSION 8----------------------------------------------------------
   case'm8':
   if($USER['tut_m8'] >= 1){	
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				'livello8'=> $LNG['tut_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	$template->show('mission_8.tpl');		
	}else{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png">',
				'livello8'=> $LNG['tut_not_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 1)
	{
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m9'] == 0)
	{
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
$query = $db->uniquequery("SELECT count(*) AS planet_count FROM ".PLANETS." WHERE `id_owner` = '". $USER['id'] ."';");
$planet_count = $query['planet_count'];
	if($planet_count >=2){
	$template->assign_vars(array(
				'Si_m8_1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m8_1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] ==0){
	$query = $db->uniquequery("SELECT count(*) AS planet_count FROM ".PLANETS." WHERE `id_owner` = '". $USER['id'] ."';");
	$planet_count = $query['planet_count'];
	if($planet_count >=2){
	$db->query("UPDATE ".USERS." SET `tut_m8`=tut_m8+1 WHERE id=".$USER['id']."");
	$db->query("UPDATE ".USERS." SET `darkmatter`=darkmatter+1000 WHERE id=".$USER['id']."");
	$template->message(sprintf($LNG['tut_m8_ready']), "?page=tutorial&mode=m9", 3);
			exit;
		}
	}
	$template->show('mission_8.tpl');
	}
	break;
//------------------------------------------------------MISSSION 9----------------------------------------------------------	
     case'm9':
   if($USER['tut_m9']>=1){
	$template->assign_vars(array(
				'Si9'=>'<img src="styles/images/gruener-haken.png" >',
				'livello9'=> $LNG['tut_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	$template->show('mission_9.tpl');	
	}else{	
	$template->assign_vars(array(
				'No9'=>'<img src="styles/images/roter-haken.png">',
				'livello9'=> $LNG['tut_not_ready'],
				));	
		if($USER['tut_m1'] == 1)
	{
	$template->assign_vars(array(
				'Si1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m1'] == 0)
	{
	$template->assign_vars(array(
				'No1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 1)
	{
	$template->assign_vars(array(
				'Si2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m2'] == 0)
	{
	$template->assign_vars(array(
				'No2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 1)
	{
	$template->assign_vars(array(
				'Si3'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m3'] == 0)
	{
	$template->assign_vars(array(
				'No3'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 1)
	{
	$template->assign_vars(array(
				'Si4'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m4'] == 0)
	{
	$template->assign_vars(array(
				'No4'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 1)
	{
	$template->assign_vars(array(
				'Si5'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m5'] == 0)
	{
	$template->assign_vars(array(
				'No5'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 1)
	{
	$template->assign_vars(array(
				'Si6'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m6'] == 0)
	{
	$template->assign_vars(array(
				'No6'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 1)
	{
	$template->assign_vars(array(
				'Si7'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m7'] == 0)
	{
	$template->assign_vars(array(
				'No7'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 1)
	{
	$template->assign_vars(array(
				'Si8'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}
	if($USER['tut_m8'] == 0)
	{
	$template->assign_vars(array(
				'No8'=>'<img src="styles/images/roter-haken.png" >',
				));	
	}

   	$query2= $db->uniquequery("SELECT `fleet_mission` FROM ".FLEETS." WHERE `fleet_owner` = '". $USER['id'] ."' AND `fleet_mission` = '8';");
	if($query2){
	$template->assign_vars(array(
				'Si_m9_1'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	$db->query("UPDATE ".USERS." SET `tut_m9_2`=tut_m9_2+1 WHERE id=".$USER['id']."");			
	}else{
	$template->assign_vars(array(
				'No_m9_1'=>'<img src="styles/images/roter-haken.png" >',
				));	
	
	}
	if($PLANET['recycler'] >= 1){
	$template->assign_vars(array(
				'Si_m9_2'=>'<img src="styles/images/gruener-haken.png" >',
				));	
	}else{
	$template->assign_vars(array(
				'No_m9_2'=>'<img src="styles/images/roter-haken.png" >',
				));	
	
	}
	
	if($USER['tut_m9']==0){
	$query = $db->uniquequery("SELECT `fleet_mission` FROM ".FLEETS." WHERE `fleet_owner` = '". $USER['id'] ."' AND `fleet_mission` = '8';");
	if($USER['tut_m9_2']>=1 && $PLANET['recycler'] >= 1){
	$db->query("UPDATE ".USERS." SET `tut_m9`=tut_m9+1 WHERE id=".$USER['id']."");
	$db->query("UPDATE ".USERS." SET `darkmatter`=darkmatter+2000 WHERE id=".$USER['id']."");	
	$template->message(sprintf($LNG['tut_m9_ready']), "?page=tutorial&mode=m9", 3);
			exit;
		}
	}
	$template->show('mission_9.tpl');
   }
   break;
   
//-----------------------------------------------------------------------------------------------------------------------------	
//Tutorial Startseite

	case'index':
	default:
if($USER['started_tut'] == 0)
{
$template->show('inizio.tpl');
}

else if($USER['tut_m8'] == 1)
{
redirectTo('game.php?page=tutorial&mode=m9');
}
else if($USER['tut_m7'] == 1)
{
redirectTo('game.php?page=tutorial&mode=m8');
}
else if($USER['tut_m6'] == 1)
{
redirectTo('game.php?page=tutorial&mode=m7');
}
else if($USER['tut_m5'] == 1)
{
redirectTo('game.php?page=tutorial&mode=m6');
}
else if($USER['tut_m4'] == 1)
{
redirectTo('game.php?page=tutorial&mode=m5');
}
else if($USER['tut_m3'] == 1)
{
redirectTo('game.php?page=tutorial&mode=m4');
}
else if($USER['tut_m2'] == 1)
{
redirectTo('game.php?page=tutorial&mode=m3');
}
else if($USER['tut_m1'] == 1)
{
redirectTo('game.php?page=tutorial&mode=m2');
}
else
{
redirectTo('game.php?page=tutorial&mode=m1');
}
break;
}
}
}

?>
