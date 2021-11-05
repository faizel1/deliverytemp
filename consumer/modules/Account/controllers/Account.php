<?php

use Restserver\Libraries\REST;

class Account extends API
{
    public function __construct()
    {
        parent::__construct(false);
        $this->load->model('AccountModel');
 
        $this->load->library('logger');
    }

    public function add_address_post()
    {
        $result = $this->AccountModel->add_address($this->post());
        $this->api_response($result, REST::HTTP_OK);
    }

    public function get_address_get($id)
    {
        $result = $this->AccountModel->get_address($id);
        $this->api_response($result, REST::HTTP_OK);
    }



    public function change_password_post()
    {
        $result = $this->AccountModel->change_password($this->post());
        $this->api_response($result, REST::HTTP_OK);
    }


    public function change_profile_image_post()
    {
        $this->response([
            "status" => true,
            "message" => "Profile image changed successfully."
        ]);
    }


    public function find_post()
    {
        $this->response([
            "status" => true,
            "message" => [
                "id" => "",
                "name" => "",
            ]
        ]);
    }


    public function sign_up_post()
    {
        $post=$this->post();
        $result = $this->AccountModel->sign_up($this->post());
        $this->api_response($result, REST::HTTP_OK);
    }

    public function index_get($id)
    {
        $this->response([
            "full_name" => "john mike",
            "email" => "john@gamil.comk",
            "phone_number" => "8768726575",
            "profile_image" => "image"
        ]);
    }
    public function login_post()
    {
        $this->response([
            "status" => true,
            "message" => [
                "id" => "",
                "key" => "",
                "name" => ""
            ]
        ]);
    }


    public function update_profile_post()
    {
        $this->response([
            "status" => true,
            "message" => "Profile succesfully updated!"
        ]);
    }




    public function send_post()
    {
        $this->response([
            "status" => true,
            "message" => "Code sent successfully check your email!"
        ]);
    }


    public function setPassword_post()
    {
        $this->response([
            "status" => true,
            "message" => "new password set successfully."
        ]);
    }




    public function verify_post()
    {
        $this->response([
            "status" => true,
            "message" => "successfully verified!"
        ]);
    }
}
