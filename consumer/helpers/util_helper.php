<?php
    use Restserver\Libraries\REST;

   function uuid_gen()
   {
      return str_replace('.','',$uniq_id = uniqid('', true));
   }

   // this method is  used to check if the given email address or phone number is not exist or not
   function is_exist($where){
      $ci =& get_instance();

      $count = $ci->db->where($where)->count_all_results('tbl_account');

      if($count > 0) {
         return true;

      } else {
         return false;
      }
            
   }

   //this method is used to generate & return random Alph-numeric password
   function password_generate() {
      $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz!@*&/?$';
      return substr(str_shuffle($data), 0, 8);

   }

   //this method is used to send randomly generated password for password reset purpose.
   function send_password($post){
      
      $ci =& get_instance();
      $ci->load->library('logger');

      $message = "Dear $post[full_name], as of your request for password reset, 
                  here is the password :  <b>$post[password]</b>.
                  <br /> we recommend you to change as  soon as you can";

      try {

          if(send($message, $post['email'],'New password from Smart Delivery')){              
              return ['status'=>true, 'statusCode'=>REST::HTTP_OK, 'message' =>'New password sent to your email address successfully.'];

          } else {
              return ['status'=>false, 'statusCode'=>REST::HTTP_INTERNAL_SERVER_ERROR, 'message' =>'unable to send password reset link. please check your network.'];
          }

      } catch (Exception $exc) {
          $ci->logger->exception_error($exc);

          return ['status'=>false, 'statusCode'=>REST::HTTP_INTERNAL_SERVER_ERROR, 'message' =>'unable to send. try again'];
      }

  }



?> 