<?php

   class Setting_Model extends Main_Model
   {
        public function __construct()
        {  
            parent::__construct();
        }

        public function Saves($post){
            $post['value'] = json_encode($post['value']);
            
            return $this->save('tbl_setting',$post,$this->validation());     
        }

        //Validation
        private function validation(){
            return [
                ['field' => 'value','label' => 'value','rules' => 'required']
            ];
        }

   }   