<?php
use Restserver\Libraries\REST;

class Type extends API
{
    public function __construct(){
        parent::__construct();
        $this->load->model('Type_Model');
        
    }
 
    public function index_post()
    {
        $result = $this->Type_Model->Saves($this->post());
        $this->api_response($result, $result['statusCode']);

    } 
  
    public function index_get()
    {
        $result = $this->Type_Model->List();
        $this->api_response($result, $result['statusCode']);

    }

    public function detail_get($id)
    {
        $result = $this->Type_Model->Detail('tbl_vehicle_type',$id);
        $this->api_response($result, $result['statusCode']);

    }

    public function delete_post()
    {
        $result = $this->Type_Model->Delete('tbl_vehicle_type',$this->post());
        $this->api_response($result, $result['statusCode']);

    }
     
}