<?php

use Restserver\Libraries\REST;

class User extends API
{
    public function __construct()
    {
        parent::__construct(false);

        $this->load->model('UserModel');
        $this->load->library('logger');
    }

    public function sign_up_post()
    {


        $result = $this->UserModel->sign_up($this->post());
        $this->api_response($result, REST::HTTP_OK);
    }

    public function index_get()
    {
        $result = $this->UserModel->List();
        $this->api_response($result, REST::HTTP_OK);
    }

    public function detail_get($id)
    {
        $result = $this->UserModel->Detail('tbl_account', $id);
        $this->api_response($result, REST::HTTP_OK);
    }

    public function delete_post()
    {
        $result = $this->UserModel->Delete('tbl_account', $this->post());
        $this->api_response($result, REST::HTTP_OK);
    }
}
