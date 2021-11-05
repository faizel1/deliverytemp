<?php defined('BASEPATH') or exit('No direct script access allowed');

class File
{
    public $path = "/";
    public $type = "jpeg|jpg|png";
    public $size = "10000";
    public $field = "image";

    public function __construct() { }
     
    public function upload(){
        $data = $config = [];

        $ci =& get_instance();

        $ci->load->helper(['security']);
        $ci->load->library('upload');

        $config['upload_path']  = $this->path;
        $config['allowed_types'] = $this->type;

        $config['overwrite'] = true;
        $config['file_ext_tolower'] = true;

        
        if(!empty($_FILES)){

            !is_dir($this->path) ? mkdir($this->path, 0777, true) : null;
            
            $total = count($_FILES[$this->field]['name']);
    
            for($i = 0; $i < $total; $i++){

                $_FILES['file']['name'] = $_FILES[$this->field]['name'][$i];
                $_FILES['file']['type'] = $_FILES[$this->field]['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES[$this->field]['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES[$this->field]['error'][$i];
                $_FILES['file']['size'] = $_FILES[$this->field]['size'][$i];
        
                $config['file_name'] = $_FILES[$this->field]['name'][$i];
        
                $ci->upload->initialize($config);
    
                $_FILES['file'] = xss_clean($_FILES['file'], true);
    
                if(!$ci->upload->do_upload('file')) {
                    delete_files(FCPATH.$this->path, TRUE);

                    return false;
                }
            }
            
        }

        return true;

    }


    public function delete($ids){
        foreach($ids as $value){

            $dest = config_item('upload_url') . $this->path . $value;
    
            if (file_exists($dest)) {
                if(is_dir($dest)){

                    if(delete_files($dest, TRUE)){
                        if(!rmdir($dest)) {
                           return ['status' => false, 'message' => 'unable to delete folder.'];
                        } 
    
                    } else {
                        return ['status' => false, 'message' => 'unable to delete file or folder.'];
                    }
    
                } else {
                    if (!unlink($dest)){
                        return ['status' => false, 'message' => 'unable to delete file.'];
                    }
                }
    
            } else {
                return ['status' => false, 'message' => 'file not exist.'];
            }

        }
        
    } 

 
}