<?php
class SessLib
{
	var $sessionKeyName = "sess";
	var $authEndpoint = "https://web2.cc.ntu.edu.tw/p/s/login2/p6.php";
	var $redirectUri;
	var $checkUserIP;
	var $locale;
	function SessLib($checkUserIP = true,$locale ="zh_TW")
	{
		if(is_bool($checkUserIP))
		$this->checkUserIP= $checkUserIP;
		if(is_string($locale))
		$this->locale= $locale;

		$this->redirectUri= $this->getredirectUri();
		$this->F362509d2();
	}
	function getredirectUri()
	{
		$V03c7c0ac = empty($_SERVER["HTTPS"])
		? ""
		: ($_SERVER["HTTPS"] == "on")
		? "s"
		: "";
		$protocol = $this->getStringBefore(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$V03c7c0ac;
		$PortIfNotStandard = ($_SERVER["SERVER_PORT"] == "80")
		? ""
		: (":".$_SERVER["SERVER_PORT"]);
		return $protocol."://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];

	}
	function getStringBefore($haystack, $needle)
	{
		return substr($haystack, 0, strpos($haystack, $needle));
	}
	function F362509d2()
	{
		if($this->hasSessionID())
		if($_SERVER["QUERY_STRING"]!=null){
			header('Location:'.$this->redirectUri);
			exit;
		}
		else return;
		if($this->hasValidSessionID())
		{
			$this->setSessionIDFromRequest();
			if($this->checkUserIP)
			$this->validateSourceIP();
			header('Location:'.$this->redirectUri);
			exit;
		}
		$this->redirectToNtuLoginPage();
	}
	function hasSessionID()
	{
		session_start();
		return ($_SESSION[$this->sessionKeyName]!=null)&&(strlen($_SESSION[$this->sessionKeyName])==64);
	}
	function setSessionIDFromRequest()
	{
		session_start();
		$_SESSION[$this->sessionKeyName] = $_GET[$this->sessionKeyName];
	}

	function validateSourceIP()
	{
		$originalIP = $_SERVER['HTTP_X_FORWARDED_FOR']!=null
		? $_SERVER['HTTP_X_FORWARDED_FOR']
		: $_SERVER['REMOTE_ADDR'];
		session_start();
		if($_SESSION['FromIP']!=$originalIP)
		{
			session_destroy();
			echo("您的 sessionid 不應該在此 IP 使用。<BR>");
			echo("五秒鐘後自動轉向登入網站。<BR>");
			echo("<script language=\"javascript\">");
			echo("setTimeout(\"location.href='".$this->redirectUri."'\",5000)");
			echo("</script>");
			exit;
		}
	}

	function redirectToNtuLoginPage()
	{

		$argv="";
		if ($this->locale== "en_US")
		$argv = "argv12=01";
		header('Location:'.$this->authEndpoint."?url=".$this->redirectUri."&".$argv);

	}
	function hasValidSessionID()
	{
		if($_GET[$this->sessionKeyName]==null||strlen(Trim($_GET[$this->sessionKeyName]))!=64)
		return false;
		require_once 'SOAP/Client.php';
		$client = new SOAP_Client('https://qsl.cc.ntu.edu.tw/s/v1.3/session.php');
		$client-> setOpt('curl',CURLOPT_SSL_VERIFYPEER,'0');
		$client-> setOpt('curl',CURLOPT_TIMEOUT,'300');
		$parameters = array('SessionID' => $_GET[$this->sessionKeyName] );
		$response = $client->call('checkSession',$parameters,'http://tempuri.org/');

		session_start();
		while (list($key, $value) = each($response)) {
			$_SESSION[$key]=UrlDecode($value);
		}
		return true;
	}
}
?>
