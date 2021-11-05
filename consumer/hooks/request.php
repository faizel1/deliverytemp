<?php 

defined('BASEPATH') or exit('No direct script access allowed');

class Request
{
	public function set_header(){	
		$token = AUTHORIZATION::generateToken(mt_rand());
		$CI->output->set_header("surafel : $token");
		return;

	}

}


?>