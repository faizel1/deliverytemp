<?php
function send($message, $email,$subject){
    $ci=&get_instance();
    
    $config = $ci->db->get("tbl_setting")->row()->email;
    $config = json_decode($config, true);

    if($config){
        $connected = @fsockopen($config['host'], $config['port']);

        if ($connected) {
            $ci->load->library('email');
    
            $ci->email->from($config['user'], 'Smart Delivery');
            $ci->email->to($email);
            $ci->email->subject($subject);
            $ci->email->message($message);
    
            $sent= $ci->email->send();

            return $sent;
            
        } else {
            return false;
        }

    }

    return false;
  
}