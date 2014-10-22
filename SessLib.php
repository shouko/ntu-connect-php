<?php
class SessLib
{
	var $V97ee5bfa = "sess";
	var $Vf82b6359 = "https://web2.cc.ntu.edu.tw/p/s/login2/p6.php";
	var $V9ad542a5;
	var $V26e63326;
	var $locale;
	function SessLib($V26e63326 = true,$locale ="zh_TW")
	{
		if(is_bool($V26e63326))
		$this->V26e63326= $V26e63326;
		if(is_string($locale))
		$this->locale= $locale;

		$this->V9ad542a5= $this->F9ad542a5();
		$this->F362509d2();
	}
	function F9ad542a5()
	{
		$V03c7c0ac = empty($_SERVER["HTTPS"])
		? ""
		: ($_SERVER["HTTPS"] == "on")
		? "s"
		: "";
		$V81788ba0 = $this->Fdfee0119(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$V03c7c0ac;
		$V901555fb = ($_SERVER["SERVER_PORT"] == "80")
		? ""
		: (":".$_SERVER["SERVER_PORT"]);
		return $V81788ba0."://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];

	}
	function Fdfee0119($V8ddf8780, $Vfac98944)
	{
		return substr($V8ddf8780, 0, strpos($V8ddf8780, $Vfac98944));
	}
	function F362509d2()
	{
		if($this->F43007317())
		if($_SERVER["QUERY_STRING"]!=null){
			header('Location:'.$this->V9ad542a5);
			exit;
		}
		else return;
		if($this->F89474043())
		{
			$this->F7a31eab3();
			if($this->V26e63326)
			$this->F174a5915();
			header('Location:'.$this->V9ad542a5);
			exit;
		}
		$this->Fc59d4660();
	}
	function F43007317()
	{
		session_start();
		return ($_SESSION[$this->V97ee5bfa]!=null)&&(strlen($_SESSION[$this->V97ee5bfa])==64);
	}
	function F7a31eab3()
	{
		session_start();
		$_SESSION[$this->V97ee5bfa] = $_GET[$this->V97ee5bfa];
	}

	function F174a5915()
	{
		$Va7c2f81f = $_SERVER['HTTP_X_FORWARDED_FOR']!=null
		? $_SERVER['HTTP_X_FORWARDED_FOR']
		: $_SERVER['REMOTE_ADDR'];
		session_start();
		if($_SESSION['FromIP']!=$Va7c2f81f)
		{
			session_destroy();
			echo("您的 sessionid 不應該在此 IP 使用。<BR>");
			echo("五秒鐘後自動轉向登入網站。<BR>");
			echo("<script language=\"javascript\">");
			echo("setTimeout(\"location.href='".$this->V9ad542a5."'\",5000)");
			echo("</script>");
			exit;
		}
	}

	function Fc59d4660()
	{

		$V61dd86c2="";
		if ($this->locale== "en_US")
		$V61dd86c2 = "argv12=01";
		header('Location:'.$this->Vf82b6359."?url=".$this->V9ad542a5."&".$V61dd86c2);

	}
	function F89474043()
	{
		if($_GET[$this->V97ee5bfa]==null||strlen(Trim($_GET[$this->V97ee5bfa]))!=64)
		return false;
		require_once 'SOAP/Client.php';
		$V62608e08 = new SOAP_Client('https://qsl.cc.ntu.edu.tw/s/v1.3/session.php');
		$V62608e08-> setOpt('curl',CURLOPT_SSL_VERIFYPEER,'0');
		$V62608e08-> setOpt('curl',CURLOPT_TIMEOUT,'300');
		$V21ffce5b = array('SessionID' => $_GET[$this->V97ee5bfa] );
		$Vd1fc8eaf = $V62608e08->call('checkSession',$V21ffce5b,'http://tempuri.org/');

		session_start();
		while (list($V3c6e0b8a, $V3a6d0284) = each($Vd1fc8eaf)) {
			$_SESSION[$V3c6e0b8a]=UrlDecode($V3a6d0284);
		}
		return true;
	}
}
?>
