<?php
use Restserver\Libraries\REST_Controller;
use Restserver\Libraries\REST;

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'libraries/REST_Controller.php';
require_once APPPATH . 'libraries/Format.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");

class API extends CI_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
  }

    public function __construct($Auth=true)
    {
        parent::__construct();
        $this->__resTraitConstruct();

        $this->load->library('Authorization');

        if($Auth){
    
            $token = $this->input->get_request_header('Authorization',TRUE);
                
            try 
            {
                if(!AUTHORIZATION::validateToken($token)) {
                    $this->api_response('Not authorized', REST::HTTP_UNAUTHORIZED);
                } 
                
            } catch (Exception $e) {
                return $e;
            }

        }

    }

    public function api_response($result, $status){
        $token = AUTHORIZATION::generateToken(mt_rand());
        header("Authorization: $token");

        $this->response($result, $status);

    }

}
 