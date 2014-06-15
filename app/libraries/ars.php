<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//Encryption & Decryption Key for Data
define("DAS_KEY", DAT_KEY);

//Encryption & Decryption Key For DB
define("PAS_KEY", ENC_KEY);

//Encryption & Decryption Algorithm Name & Mode
define("ALGORITHM", MCRYPT_RIJNDAEL_256);
define("MODE", MCRYPT_MODE_ECB);

class Ars
{
	public $ekey;
	// No Browser Cache Function
	public function nocache()
	{
		header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
		header('Cache-Control: post-check=0, pre-check=0', FALSE);
		header('Pragma: no-cache');
		header('Expires: Wed, 05 Oct 1988 09:30:00 GMT');
		header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
	}
	
	// Password Encryption
	public function encPas($ps, $pKey = PAS_KEY)
	{
		$p1 = md5($ps);
		$p2 = $pKey.$p1.md5($pKey);
		$pas = md5(sha1($p2));
		return $pas;
	}
	// Password Generator
	public function genPas()
	{
		$alp	= array('A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','m','n','p','q','r','s','t','u');
		$sym	= array('@','#','$','%');
		$c1 = $alp[array_rand($alp)];
		$c2 = $alp[array_rand($alp)];
		$c3 = $alp[array_rand($alp)];
		$c4 = $alp[array_rand($alp)];
		$s1 = $sym[array_rand($sym)];
		$n1 = rand(0, 9);
		$n2 = rand(0, 9);
		$n3 = rand(0, 9);
		$pas = $c1.$n1.$s1.$c3.$c2.$n2.$c4.$n3;
		return $pas;
	}

	// Data Encrypt & Decrypt
	public function encData($str)
	{
		return $this->enCode($str, DAS_KEY);
	}
	public function decData($str)
	{
		return $this->deCode($str, DAS_KEY);
	}

	// Encryption & Decryption : AES with base64
	// Encrypt
	public  function enCode($value, $ekey = PAS_KEY)
	{
		if(!$value){return false;}
		$ekey = md5(sha1(md5($ekey)));
		$iv_size = mcrypt_get_iv_size(ALGORITHM, MODE);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$crypttext = mcrypt_encrypt(ALGORITHM, $ekey, $value, MODE, $iv);
		return strtr(base64_encode($crypttext), '+/=', '-_,');
	}
	// Decrypt
	public function deCode($value, $ekey = PAS_KEY)
	{
		if(!$value){return false;}
		$ekey = md5(sha1(md5($ekey)));
		$value = base64_decode(strtr($value, '-_,', '+/='));
		$iv_size = mcrypt_get_iv_size(ALGORITHM, MODE);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypttext = mcrypt_decrypt(ALGORITHM, $ekey, $value, MODE, $iv);
		return trim($decrypttext);
	}
	
	// No HTML
	public function noHtml($str, $encoding='UTF-8')
	{
		return htmlspecialchars($str, ENT_QUOTES, $encoding);
	}
	
	// Time Diff
	public function getTimes($t1, $t2)
	{
		$timeFirst  = strtotime($t1);
		$timeSecond = strtotime($t2);
		$differenceInSeconds = $timeSecond - $timeFirst;
		$h=0;
		$m	= floor($differenceInSeconds / 60);
		$s	= $differenceInSeconds % 60;
		if ($m>=60)
		{
			$h = floor($m / 60);
			$m = $m % 60;
		}
		$tim = $h.':'.$m.':'.$s;
		return $tim;
	}
	
}
?>
