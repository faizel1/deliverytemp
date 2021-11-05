<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST;

class Logger {

    public function __construct() { }
     
    // this method extract database error from codignitor and stores on log
    public function database_error($msg = "")
    {
        $ci =& get_instance();

        $error = $ci->db->error();

        if ($error['code'] > 0) {
            $msg .="\n Database Error : " .  $error['message'];

            log_message('error', $msg);
        }
    }

    // this method store form validation error sent by caller function
    public function form_error($error, $title)
    {
        $backtrace = debug_backtrace();
        
        $file=$line=null;

        if (!empty($backtrace[0]) && is_array($backtrace[0])) {
            $file = $backtrace[0]['file'];
            $line=$backtrace[0]['line'];
        }

        $message ="\n Title : " . $title;
        $message .="\n User info : " . "unknown user";

        if (is_array($error)) {
            $message .="\n" . $message . " " . json_encode($error, JSON_PRETTY_PRINT);

        } elseif (!is_array($error)) {
            $message .="\n" . $message;
        }

        log_message('error', $message);

    }

    // this method store unhandeld exceptions error sent by caller function
    public function exception_error($exc)
    {
        $message = $exc->getMessage() . ' \n Line : ' . $exc->getLine();            
        
        $message .="\n User info : " . "unknown user";
        $message .= "\n File : " . $exc->getFile() . '\n Code: ' . $exc->getCode();
        $message .= json_encode($exc->getTrace(), JSON_PRETTY_PRINT);

        log_message('error', $message);

    }

    // this method is used for form (array) validation, if valid return true else return false
    function validate_form($rule, $data, $table)
    {
        $ci =& get_instance();

        $ci->form_validation->reset_validation()->set_rules($rule)->set_data($data);
                        
        if($ci->form_validation->run()) {
            return ['status' => true];

        } else {
            $error = $ci->form_validation->error_array();
            $this->form_error($error,"$table Form Validation Error");

            return ['status' => false, 'statusCode'=>REST::HTTP_BAD_REQUEST, 'message' => $error];
        } 

    }
    

 
}