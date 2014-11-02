<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Home extends CI_Controller
{
	public $uid, $usr, $utp, $enm;
	public function __construct()
	{
		parent::__construct();
		$this->load->library('antispam');
		$this->load->model('auth','',TRUE); 
		$session_data = $this->session->userdata('login_ses');
		if($session_data)
		{
			$this->uid = $session_data['uid'];
			$this->usr = $session_data['usr'];
			$this->utp = $session_data['utp'];
			$this->enm = $session_data['enm'];
			$this->load->model('hom','',TRUE);
		}
		else
		{
			redirect(site_url('authenticate/logout'), 'location');
		}
	}

	public function index()
	{
	}
	
	public function downloadFile($path)
	{
		/*$data = file_get_contents($path); // Read the file's contents
		$name = basename($path);
		echo force_download($name, $data);
		exit;*/
		$path = decData($path);
		if(file_exists($path))
		{
			if(is_file($path))
			{
				// get the file mime type using the file extension
				$mime = get_mime_by_extension($path); // Build the headers to push out the file properly.
				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: private',false);
				header('Content-Type: '.$mime);
				header('Content-Disposition: attachment; filename="'.basename($path).'"');
				header('Content-Transfer-Encoding: binary');
				header('Connection: close');
				readfile($path);
				exit;
			}
		}
		else
		{
			show_404($path);
		}
		
	}

}
?>
