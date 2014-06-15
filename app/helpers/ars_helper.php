<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$CI=& get_instance();

//Encryption & Decryption Key for Data
define("DKEY", $CI->config->item('enc_dat'));

//Encryption & Decryption Key For DB
define("PKEY", $CI->config->item('enc_pas'));

//Encryption & Decryption Algorithm Name & Mode
define("ALG", $CI->config->item('enc_alg'));
define("MOD", $CI->config->item('enc_mod'));

// Clear Cache
if ( ! function_exists('nocache'))
{
	function nocache()
	{
		header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
		header('Cache-Control: post-check=0, pre-check=0', FALSE);
		header('Pragma: no-cache');
		header('Expires: Wed, 05 Oct 1988 09:30:00 GMT');
		header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
		ob_clean();
	}
}

// getPlugins View
if ( ! function_exists('getPlugins'))
{
	function getPlugins()
	{
		$ci=& get_instance();
		$data['inc'] = true;	// this will prevent plugins from loading directly
		$ci->load->view('include/plugins', $data);
	}
}

// Password Generator
if ( ! function_exists('genPas'))
{
	function genPas()
	{
		$alp	= array('A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','m','n','p','q','r','s','t','u');
		$sym	= array('@','#','$','%');
		$c1 	= $alp[array_rand($alp)];
		$c2 	= $alp[array_rand($alp)];
		$c3 	= $alp[array_rand($alp)];
		$c4 	= $alp[array_rand($alp)];
		$s1 	= $sym[array_rand($sym)];
		$n1 	= rand(0, 9);
		$n2 	= rand(0, 9);
		$n3 	= rand(0, 9);
		$pas 	= $c1.$n1.$s1.$c3.$c2.$n2.$c4.$n3;
		return $pas;
	}
}

// Data Encrypt & Decrypt
if ( ! function_exists('encData'))
{
	function encData($str)
	{
		return enCode($str, DKEY);
	}
}

if ( ! function_exists('decData'))
{
	function decData($str)
	{
		return deCode($str, DKEY);
	}
}

// Encryption & Decryption : AES with base64
// Encrypt
if ( ! function_exists('enCode'))
{
	function enCode($value, $ekey = PKEY)
	{
		if(!$value){return false;}
		$ekey = md5(sha1(md5($ekey)));
		$iv_size = mcrypt_get_iv_size(ALGORITHM, MODE);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$crypttext = mcrypt_encrypt(ALGORITHM, $ekey, $value, MODE, $iv);
		return strtr(base64_encode($crypttext), '+/=', '-_,');
	}
}

// Decrypt
if ( ! function_exists('deCode'))
{
	function deCode($value, $ekey = PKEY)
	{
		if(!$value){return false;}
		$ekey = md5(sha1(md5($ekey)));
		$value = base64_decode(strtr($value, '-_,', '+/='));
		$iv_size = mcrypt_get_iv_size(ALGORITHM, MODE);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypttext = mcrypt_decrypt(ALGORITHM, $ekey, $value, MODE, $iv);
		return trim($decrypttext);
	}
}

if ( ! function_exists('getTimes'))
{
	function getTimes($t1, $t2)
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


if ( ! function_exists('getTimeInSec'))
{
	function getTimeInSec($t1, $t2)
	{
		$timeFirst  = strtotime($t1);
		$timeSecond = strtotime($t2);
		$differenceInSeconds = $timeSecond - $timeFirst;
		return $differenceInSeconds;
	}
}

if ( ! function_exists('convert_number_to_words'))
{
    function convert_number_to_words($number)
    {

        $hyphen      = ' ';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Fourty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            1000000             => 'Million',
            1000000000          => 'Billion',
            1000000000000       => 'Trillion',
            1000000000000000    => 'Quadrillion',
            1000000000000000000 => 'Quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
        }

}

if ( ! function_exists('hex2rgb'))
{
    function hex2rgb($hex) {
       $hex = str_replace("#", "", $hex);

       if(strlen($hex) == 3) {
          $r = hexdec(substr($hex,0,1).substr($hex,0,1));
          $g = hexdec(substr($hex,1,1).substr($hex,1,1));
          $b = hexdec(substr($hex,2,1).substr($hex,2,1));
       } else {
          $r = hexdec(substr($hex,0,2));
          $g = hexdec(substr($hex,2,2));
          $b = hexdec(substr($hex,4,2));
       }
       $rgb = array($r, $g, $b);
       //return implode(",", $rgb); // returns the rgb values separated by commas
       return $rgb; // returns an array with the rgb values
    }
}

if ( ! function_exists('delFiles'))
{
    function delFiles($path, $typ)
    {
        $path = decData($path);
        $files = glob($path.'/*.'.$typ);  // get all file names
        foreach($files as $file)
        {
            if(is_file($file))
                @unlink($file); // deleting the file
        }
    }
}
