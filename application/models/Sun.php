<?php
require_once ("SunMainManager.php");
class sun
{
	public $mManager;
	public function __construct()
	{
		$mManager = new SunMainManager();
	}
	public function callManager($getxml,$postxml)
	{
		//Data sent can be either in the form of a GET or POST request
		//echo 'REQUEST FROM CLIENT - '.$postxml;


		if(empty($getxml) && empty($postxml))
		{
			//If both GET and POST do not have any parameters, send error
			echo "<Root><SunStone><Action><Service>API</Service><Status>APICALLFAIL</Status></Action></SunStone></Root>";
			die('');
		}
		//Get the formatted XML from the function Process located in SunMainManager.php file
		$mManager = new SunMainManager();
		$retval = $mManager->Process($getxml,$postxml);
		echo $retval;
	}
}


?>
