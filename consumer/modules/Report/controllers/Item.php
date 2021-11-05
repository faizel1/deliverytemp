<?php
use Restserver\Libraries\REST;

class Item extends API
{
    public function __construct(){
        parent::__construct();
        $this->load->model('Item_Model');
    }
 
    public function index_post()
    {
        $result = $this->Item_Model->Saves($this->post());
        $this->api_response($result, $result['statusCode']);

    } 
  
    public function index_get()
    {
        $result = $this->Item_Model->List();
        $this->api_response($result, $result['statusCode']);

    }

    public function detail_get($id)
    {
        $result = $this->Item_Model->Detail('tbl_vehicle',$id);
        $this->api_response($result, $result['statusCode']);

    }

    public function delete_post()
    {
        $result = $this->Item_Model->Delete('tbl_vehicle',$this->post());
        $this->api_response($result, $result['statusCode']);

    }

    public function load_drivers_get()
    {
        $result = $this->Item_Model->Load_drivers();
        $this->api_response($result, REST::HTTP_OK);

    }

    public function load_vehicle_type_get()
    {
        $result = $this->Item_Model->Load_vehicle_type();
        $this->api_response($result, REST::HTTP_OK);

    }
  
     
}