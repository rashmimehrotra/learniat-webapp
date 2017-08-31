<?php

require_once 'SunDataBaseAuthConst.php';

class SunDataBaseManager
{

    private $conn=null;
    private static $instance;

    private function __construct()
    {
  	      $this->setConnection();
    }

    public function setConnection()
    {  	
        $this->conn=mysql_connect(SunDataBaseAuthConst::$DBHOST,SunDataBaseAuthConst::$DBUSERNAME,SunDataBaseAuthConst::$DBPASSWORD);
        if ($this->conn==null)
		{
			$this->conn = mysql_connect(SunDataBaseAuthConst::$DBALTERNATEHOST,SunDataBaseAuthConst::$DBUSERNAME,SunDataBaseAuthConst::$DBPASSWORD);
			if ($this->conn==null)
				{
					echo "DB CONNECT FAILURE";
					return false;
				}
		}
        $db = mysql_select_db(SunDataBaseAuthConst::$DBNAME);
        return true;     
    }

    public function QueryDB($queryString)
    {   	
		$result = mysql_query($queryString);

        	if(!$result){
			echo 'MySqlError - '.mysql_error();
		}
		return $result;
    }
    
    public function getnoOfrows($result)
    {
        	$count = mysql_num_rows($result);
        	return $count;
    }
    
    public function getLastInsertId()
    {
        	$id = mysql_insert_id();
        	return $id;
    }

    public function Disconnect()
    {
        try{
            	if($this->conn != null) mysql_close($this->conn);
        }
        catch (Exception $e) {
	}   
    }

    public static function getSingleton()
    {
    	
        if(!isset(self::$instance))
        {
            $classname = __CLASS__;
            self::$instance = new $classname;
        }
        return self::$instance;
    }

}


?>
