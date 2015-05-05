<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| API Config File
|--------------------------------------------------------------------------
|
*/
$config['api_url']		= 'http://example.com/index.php';
$config['api_auth'] = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
$config['app_name'] = "App Name";
$config['app_from_email'] = "app@example.com";
$config['app_to_email'] = "app@example.com";	// used for issue report also
$config['api_server'] = "aws1";
$config['upload_path'] = "./assets/uploads/chat";
$config['upload_max_size'] = 20; // size in MB
$config['old_api_url']	= $config['api_url'];

// Chat Socket Configuration
$config['socket_host'] = "192.168.1.1";
$config['socket_port'] = 8099;
$config['read_size'] = 2048;
$config['delimiter'] = "\r\n";
$config['stopper_file'] = "stop.dat";

// Cron Configuration
$config['cron_curl_path'] = config_item('base_url').'/index.php/chat/server';

// SMS Gateway Configuration
$config['sms_send']		= true;
$config['sms_url']		= "https://rest.nexmo.com/sms/json";
$config['sms_params']	= "api_key=xxxxxxx&api_secret=xxxxxxx";	//live

// Push Notification Configuration
$config['google_api_key'] = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$config['iphone_push_url'] = "ssl://gateway.sandbox.push.apple.com:2195";
$config['iphone_key_path'] = "/var/www/html/assets/ios/server_certificates_bundle_sandbox.pem";
$config['iphone_pass_key'] = "123456";

// Logs Configuration
$config['api_log_status'] = true;
$config['api_log_date_format'] = "d-M-Y h:i:s a";
$config['api_log_path'] = "./logs/";
$config['api_log_pass'] = "123";
$config['log_list'] = array(
						array("status"=>true, "text"=>"Socket Log", "link"=>"sock.html"),
						array("status"=>true, "text"=>"Connection Logs", "link"=>"connection.html"),
						array("status"=>true, "text"=>"Content Logs", "link"=>"content.html"),
						array("status"=>true, "text"=>"Ack Logs", "link"=>"ack.html"),	
						array("status"=>true, "text"=>"Message Logs", "link"=>"msg.html"),
						array("status"=>true, "text"=>"Notification Logs", "link"=>"notify.html"),
						array("status"=>true, "text"=>"SMS Logs", "link"=>"sms.html"),
						array("status"=>true, "text"=>"Logs", "link"=>"logs.html"),
						array("status"=>true, "text"=>"API Logs", "link"=>"api.html"),
						array("status"=>true, "text"=>"API Output", "link"=>"out.html")
					);

/* End of file api.php */
/* Location: ./application/config/api.php */
