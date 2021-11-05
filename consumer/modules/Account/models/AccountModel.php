<?php

use Restserver\Libraries\REST;

class AccountModel extends Main_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('logger');
    }

    // login
    public function Login($post)
    {
        $validation = [
            ['field' => 'email', 'label' => 'email', 'rules' => 'required|valid_email'],
            ['field' => 'password', 'label' => 'password', 'rules' => 'required|min_length[5]']
        ];

        $result = $this->logger->validate_form($validation, $post, 'login');

        if ($result['status']) {

            $result = $this->db->select("acc.*,gr.role")
                ->from("tbl_account as acc")
                ->join('tbl_group as gr', 'acc.group_id = gr.id')
                ->where(["email" => $post["email"]])
                ->get()->row();

            if ($result) {

                if ($result->status === "0") {
                    return [
                        'status' => false, 'statusCode' => REST::HTTP_LOCKED,
                        'message' => 'account is locked now. please contact administrator to unlock.'
                    ];
                } else {
                    if (password_verify($post['password'], $result->password)) {

                        $result->key = AUTHORIZATION::generateToken($result->id);
                        unset($result->password);

                        $this->load->library("session");

                        $result->role = json_decode($result->role, true);
                        $this->session->set_userdata('active_user', $result);

                        return ['status' => true, 'statusCode' => REST::HTTP_OK, 'message' => $result];
                    } else {
                        return ['status' => false, 'statusCode' => REST::HTTP_UNAUTHORIZED, 'message' => 'Password is wrong'];
                    }
                }
            } else {
                return ['status' => false, 'statusCode' => REST::HTTP_NOT_FOUND, 'message' => 'email address is wrong'];
            }
        } else {
            return $result;
        }
    }


    // this method send auto generated password with a given email address once the user is valid
//ghjghjgjh
    public function Send_Password($post)
    {
        $validation = [
            ['field' => 'id', 'label' => 'id', 'rules' => 'required'],
            ['field' => 'full_name', 'label' => 'full name', 'rules' => 'required'],
            ['field' => 'email', 'label' => 'email', 'rules' => 'required|valid_email'],
        ];

        $result = $this->logger->validate_form($validation, $post, 'sen');

        if ($result['status']) {

            $password = password_generate();
            $post['password'] = password_hash($password, PASSWORD_BCRYPT);

            $result = send_password($post);

            if ($result['status']) {
                $this->db->update('tbl_account', $post, ['id' => $post['id']]);
            }

            return $result;
        } else {
            return $result;
        }
    }


    public function change_password($post)
    {
        $validation = [
            ['field' => 'id', 'label' => 'id', 'rules' => 'required'],
            ['field' => 'new_password', 'label' => 'New Password', 'rules' => 'required|min_length[5]'],
            ['field' => 'old_password', 'label' => 'Old Password', 'rules' => 'required|min_length[5]'],
        ];

        $result = $this->logger->validate_form($validation, $post, 'change password');

        if ($result['status']) {

            $select = "acc.password";
            $valid = $this->db->select($select)
                ->from('tbl_account as acc')
                ->where("acc.id", $post['id'])
                ->get()->row();



            if (!($valid && (password_verify($post['old_password'], $valid->password)))) {
                return ['status' => false, 'message' => 'The old Password is wrong'];
            }
            $pass = password_hash($post['new_password'], PASSWORD_BCRYPT);

            if ($this->db->update('tbl_account',  ["password" => $pass])) {
                return ['status' => true, 'message' => "Your password is changed successfully"];
            }
            return ['status' => false, 'message' => 'Something is wrong try again'];
        } else {
            return $result;
        }
    }



// validation problem

    public function sign_up($post)
    {
        $validation = [
            ['field' => 'full_name',        'label' => 'Full Name', 'rule' => 'required'],
            ['field' => 'type',             'label' => 'type', 'rule' => 'required'],
            ['field' => 'phone_number',     'label' => 'Phone Number', 'rules' => 'required'],
            ['field' => 'email',            'label' => 'Email', 'rules' => 'required|valid_email'],
            ['field' => 'password',         'label' => 'Password', 'rules' => 'required|min_length[5]']
        ];


        $email = $this->db->select('count(id) as count')->from('tbl_account')->where('email', $post['email'])->get()->row();
        $phone_number = $this->db->select('count(id) as count')->from('tbl_account')->where('phone_number', $post['phone_number'])->get()->row();
     
     
        if (!$email->count > 0) {
            if (!$phone_number->count > 0) {
                $post['password'] = password_hash($post['password'], PASSWORD_BCRYPT);

                $result = $this->Save('tbl_account', $post, $validation);
            } else {
                return ['status'=>false, 'statusCode'=>REST::HTTP_INTERNAL_SERVER_ERROR, 'message' =>"your Phone Number is already taken"];
            }
        } else {
            return ['status'=>false, 'statusCode'=>REST::HTTP_INTERNAL_SERVER_ERROR, 'message' =>"your Email is already taken"];
        }

        return $result;
    }




    public function add_address($post)
    {

        $validation = [
            ['field' => 'account_id', 'label' => 'User Id', 'rule Name' => 'required'],
            ['field' => 'region', 'label' => 'Region', 'rule' => 'required'],
            ['field' => 'city', 'label' => 'City', 'rules' => 'required'],
            ['field' => 'sub_city', 'label' => 'Subcity', 'rules' => 'required'],
            ['field' => 'wereda', 'label' => 'Wereda', 'rules' => 'required'],
            ['field' => 'location', 'label' => 'location', 'rules' => 'required'],
        ];
        $result = $this->Save('tbl_address', $post, $validation);

        return $result;
    }


    public function get_address($id)
    {
        return $this->db->select('*')->from('tbl_address')->where('account_id', $id)->get()->row();
    }

    public function find_account($post)
    {
        $message=['status' => 'false','message' => "your account could not be found"]  ;

        if (isset($post['email'])) {
            $result = $this->db->select('full_name,id')->from('tbl_account')->where('email', $post['email'])->get()->row();
         if($result) {
           $message=['status' => 'true','message' => $result]  ;
         }
        }   
         elseif(isset($post['phone_number'])) {
            $result = $this->db->select('full_name,id')->from('tbl_account')->where('phone_number', $post['phone_number'])->get()->row();
            if($result) {
                $message=['status' => 'true','message' => $result]  ;
              }
        }
        return $message; 
    }
}
