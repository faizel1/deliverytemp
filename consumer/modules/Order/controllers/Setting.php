<?php
use Restserver\Libraries\REST;

class Setting extends API
{
    public function __construct(){
        parent::__construct('Setting_Model');
    }
 
    public function index_post()
    {
        $result = $this->Setting_Model->Saves($this->post());
        $this->api_response($result, $result['statusCode']);

    } 
  
    public function index_get()
    {
        $result = $this->Setting_Model->Detail('tbl_setting',1);
        $this->api_response($result, $result['statusCode']);

    }

 
  
     
}
