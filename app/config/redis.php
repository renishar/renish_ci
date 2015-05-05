<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Config for the CodeIgniter Redis library
 *
 * @see ../libraries/Redis.php
 */
// Default connection group
$config['redis_default']['host'] = 'pub-redis-18675.us-east-1-3.1.ec2.garantiadata.com';		// IP address or host
$config['redis_default']['port'] = '18675';			// Default Redis port is 6379
$config['redis_default']['password'] = 'oltdev123#';			// Can be left empty when the server does not require AUTH

$config['redis_slave']['host'] = '';
$config['redis_slave']['port'] = '6379';
$config['redis_slave']['password'] = '';
