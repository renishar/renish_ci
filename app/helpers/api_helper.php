<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$CI=& get_instance();

if ( ! function_exists('testHelper'))
{
    function testHelper($mid)
    {
        $ci=& get_instance();
        $ci->load->model('ch');
        $ci->load->model('ap');
        $z = $ci->ch->getNotifications($mid);
        echo "Result:";
        print_r($z);
        exit;
    }
}

if ( ! function_exists('logResponse'))
{
    function logResponse($msg, $file="logs.html")
    {
        global $CI;
        if(config_item('api_log_status'))
        {
        	$log_list = config_item('log_list');
        	foreach($log_list as $ls)
        	{
        		if($ls['link']==$file && $ls['status']==false)
        		{
        			return true;
        		}
        	}
        	$rand_color = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
            $logTxt = date(config_item('api_log_date_format')).' : <span style="color:'.$rand_color.'">'.$msg.'</span><br/><br/>';
            $lpath = config_item('api_log_path');
            if(!file_exists($lpath)) { @mkdir($lpath, 0777); @chmod($lpath, 0777); }
            @file_put_contents($lpath.$file, $logTxt, FILE_APPEND);
        }
        return true;
    }
}

if ( ! function_exists('multiJsonDecode'))
{
	function multiJsonDecode($s, $assoc = false, $depth = 512)
	{
		logResponse("multiJsonDecode : ".$s);
		if(substr($s, -1) == ',')
			$s = substr($s, 0, -1);
		return json_decode("[$s]", $assoc, $depth);
	}
}


if ( ! function_exists('getApiResponse'))
{
	function getApiResponse($req=NULL)
	{
		if($req==NULL) return false;
		$req["auth"] = config_item('api_auth');
		$fields = array('requestParam' => json_encode($req));
		$ch1 = curl_init(config_item('api_url'));
		curl_setopt($ch1, CURLOPT_POST, true);
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
		// Disabling SSL Certificate support temporarly
		curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch1, CURLOPT_POSTFIELDS, $fields);
		// Execute Post
		$result = curl_exec($ch1);
		if($result)
		{
			return json_decode($result, true); 
		}
		else
		{
			logResponse("getApiResponse Failed: ".curl_error($ch1));
		}
		// Close connection
		curl_close($ch1);
	}
}

if ( ! function_exists('getCIApiResponse'))
{
	function getCIApiResponse($req=NULL)
	{
		if($req==NULL) return false;
		$req["auth"] = config_item('api_auth');
		$fields = array('requestParam' => json_encode($req));
		$ch1 = curl_init(site_url('api/index'));
		curl_setopt($ch1, CURLOPT_POST, true);
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
		// Disabling SSL Certificate support temporarly
		curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch1, CURLOPT_POSTFIELDS, $fields);
		// Execute Post
		$result = curl_exec($ch1);
		if($result)
		{
			return json_decode($result, true);
		}
		else
		{
			logResponse("getCIApiResponse Failed: ".curl_error($ch1));
		}
		// Close connection
		curl_close($ch1);
	}
}

/*
$dat = array(
		'from' => $from,
		'to' => $to,
		'alert' => $msg,
		'badge' => $total_push_count_notification,
		'details' => array(
				'c_id' => 59,
				'c_type => 1,
            				'f_id' => 5,
            				'f_name' => 'C Profile',
            				'l_msg" => 'hvhvvv',
            				'l_mtime" => '2014-11-10 12:48:42',
				'no_u_msg" => 5
		)
);
*/


if ( ! function_exists('sendPushNotification'))
{
    function sendPushNotification($dat)
    {
    	$CI =& get_instance();
    	$CI->load->model('ch');
    	$CI->load->model('ap');
    	
        unset($dat['badge']);
    	$from = $dat['from'];	unset($dat['from']);
    	$to = $dat['to'];		unset($dat['to']);
    	$msg = $dat['alert'];
    	
    	$req = array("user_id" => $to, "request_type" => "conversation_listing");
    	$res = getCIApiResponse($req);
    	$dat['badge'] = !empty($res['badge']) ? $res['badge'] : 0;
    	
    	$dat['content-available'] = 1;
    	
    	$mid = $dat['msg_id'];
    	if(!empty($mid))
    	{
    		$mid = trim($mid);
    		unset($dat['msg_id']);
    		$pr = substr($mid, 0,1); //$mid[0];
    		$vl = substr($mid, 1);
    		if($pr=="c")
    		{
    			$resPush = $CI->ch->getMessage($vl);
			}
    		if($pr=="n")
    		{
                $resPush = $CI->ch->getNotifications($vl);
    		}
    		
    		logResponse("MID: ".$mid. "(".$pr."-".$vl.") Result: ".json_encode($resPush), "notify.html");
    		
    		if($resPush)
    		{
    			foreach($resPush as $mes);
    		}
    		$c_type = (string)$mes->type;
    		$m_type = (string)$mes->message_type;
    		$arr = array(
	    				"c_id"=>(string)$mes->conversation_id,
	    				"c_type"=>(string)$c_type,
	    				"m_type"=>(string)$mes->message_type,
	    				"u_name"=>deCode($mes->firstname)." ".deCode($mes->lastname),
	    				"msg_id"=>(string)$mid,
	    				"m_time"=>(string)$mes->datestamp,
	    				"t_stamp"=>(string)$mes->t_stamp,
    					"msg"=>$mes->message,
    					"c_name" => "",
    					"c_code" => !empty($dat['p_cc']) ? $dat['p_cc'] : (string)deCode($mes->country_code), 
    					"phone_no" => !empty($dat['p_ph']) ? $dat['p_ph'] : (string)deCode($mes->phone_no) 
    		);
    		if($c_type=="1")
    		{
    			if($to == $mes->from_id)	$arr['id'] = (string)$mes->to_id;
    			else if($to == $mes->to_id)	$arr['id'] = (string)$mes->from_id;
    		}
    		else if($c_type=="2")
    		{
    			$arr['id'] = (string)$mes->cl_id;
    			$cl = $CI->ap->getClique($mes->cl_id);
    			$arr['c_name'] = (string)$cl[0]->clique_name;
    		}
    		else if($c_type=="3")
    		{
    			$arr['id'] = (string)$mes->ga_id;
    			$ga = $CI->ap->getGathering($mes->ga_id);
    			$arr['c_name'] = (string)$ga[0]->title;
    		}
    		
    		if($m_type=="11")
    		{
    			$arr['track_start_date'] = (string)$mes->track_from;
    			$arr['track_end_date']	 = (string)$mes->track_to;
    		}
    		
    		if($m_type=="2" || $m_type=="3" || $m_type=="10" || $m_type=="11")
    		{
    			$arr['start_date']	= (string)$mes->start_date;
    			$arr['end_date'] 	= (string)$mes->end_date;
    		}
    		if($m_type=="5" || $m_type=="6" || $m_type=="7" || $m_type=="9")
    		{
    			$arr['m_url'] = (string)$mes->message_url;
    		}
    		if($m_type=="8" || $m_type=="9")
    		{
    			$arr['color'] = (string)$mes->mood_color;
    		}
    		if($m_type=="9")
    		{
    			$arr['lat']   = (string)$mes->latitude;
    			$arr['long']  = (string)$mes->longitude;
    			$arr['addr']  = (string)$mes->address;
    		}
    		$dat['msg'] = $arr;	// adding details of message
    	}

    	$x = $CI->ap->getPushInfo($to);
    	$notify = false;
    	if(!empty($x)){ foreach ($x as $ar)
    	{
            $device_type = $ar->device_type;
            $device_id = $ar->device_id;
            
            $message = $msg;
            if(!empty($device_id) && !empty($message))
            {
                if($device_type == 'android')
                {
                    $notify = send_notification($device_id, json_encode($dat));
                }
                else if($device_type == 'iphone')
                {
                	$dat['sound'] = 'default';
                    $notify = push_notification_iphone($device_id, $dat);
                }
            }
            if($notify)
            {
                logResponse("Push Notification Success for ".$device_type.": ".json_encode($dat)." - from ".$from." to ".$to, "notify.html");           
            }
            else
            {
                logResponse("Push Notification Failed from ".$from." to ".$to." ! Details: ".json_encode($ar)." Data: ".json_encode($dat), "notify.html");
                logResponse("Push Notification Failed from ".$from." to ".$to." ! Details: ".json_encode($ar)." Data: ".json_encode($dat));
            }
        }}
        return $notify;
    }
}

if ( ! function_exists('send_notification'))
{
    function send_notification($device_id, $msg)
    {
    	global $CI;
        $registatoin_ids = array($device_id);
        $message = array("message" => $msg);
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message
        );
        $headers = array(
            'Authorization: key=' . config_item('google_api_key'),
            'Content-Type: application/json'
        );
        $ch      = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            logResponse("Android Push - CURL Failed: ".curl_error($ch), "notify.html");
        }
        curl_close($ch);
        if ($result)    return 1;        
    }
}

if ( ! function_exists('push_notification_iphone'))
{
    function push_notification_iphone($deviceToken, $dat)
    {
    	global $CI;
    	$passphrase = config_item('iphone_pass_key');
        $deviceToken = trim($deviceToken);
        $passphrase  = trim($passphrase);

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', config_item('iphone_key_path'));
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        
        // Open a connection to the APNS server
        $fp = stream_socket_client(config_item('iphone_push_url'), $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if(!$fp)
        {
            logResponse("iPhone Push - Failed to connect: ". $err." ".$errstr, "notify.html");
        }
        $body['aps'] = $dat;
        
        // Encode the payload as JSON
        $payload = json_encode($body);
        
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);
        if ($result) { return 1; }
        else         { return false; }        
    }
}

if ( ! function_exists('cmp'))
{
	 function cmp($a, $b) {
	  if ($a['datestamp'] == $b['datestamp']) {
	    return 0;
	  }
	
	  return ($a['datestamp'] < $b['datestamp']) ? -1 : 1; 
	}
}

if ( ! function_exists('sendSMS'))
{
	function sendSMS($cco, $mob, $msg)
	{
		//logResponse("CC : ".$cco." MOB : ".$mob, "sms.html");
		//global $CI;
        $ci=& get_instance();
        $ci->load->model('ap');
        $ci->load->model('ch');

		$result = false;
		// setting sender id
		$cco = trim($cco);
		$mob = trim($mob);
		if($cco=="91" || $cco=="+91" || $cco==91 || $cco=='91')
		{
			$sid = "nroote";
		}
		else
		{
			$sss = $ci->ap->getSmsSender();
			$sid = $sss[0]->sid;
			$cnt = $sss[0]->day_count+1;
			$dat = array('day_count'=>$cnt, 'last_used'=>date('Y-m-d H:i:s', time()));
			$ci->ap->setSmsSender($dat, $sid);			
		}
		
		$tim = 120; // time in seconds
		$url = trim(config_item('sms_url'));
		$par = trim(config_item('sms_params'));	// live
		$mobno = urlencode($cco.$mob);
		$msg = urlencode(trim($msg));
		$api_url = $url."?".$par."&from=".$sid."&to=".$mobno."&text=".$msg;
        if(config_item('sms_send'))
        {
            $result = file_get_contents($api_url);
            logResponse("SMS from SID: ".$sid." to: ".$cco."-".$mob." | Msg: ".urldecode($msg)." - Response: ".$result, "sms.html");
        }
        else
        {
        	logResponse("(API Disabled) SMS from SID: ".$sid." to: ".$cco."-".$mob." | Msg: ".urldecode($msg)." - Response: ".$result, "sms.html");
        }
		if($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}	
}

if ( ! function_exists('cmp'))
{
	function cmp($a, $b)
	{
		if (strtotime($a['datestamp']) == strtotime($b['datestamp']) )
		{
			return 0;
		}
		return (strtotime($a['datestamp']) > strtotime($b['datestamp']) ) ? -1 : 1;
	}
}

if ( ! function_exists('array_orderby'))
{
    function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
                }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
}

?>
