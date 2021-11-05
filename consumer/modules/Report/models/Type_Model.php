<?php
use Restserver\Libraries\REST;

   class Type_Model extends Main_Model
   {
        public function __construct()
        {  
            parent::__construct();
        }

        public function Saves($post){            
            return $this->save('tbl_vehicle_type',$post,$this->validation(), "/vehicle/type");     
        }

        public function List(){ 
            $result = $this->db->get("tbl_vehicle_type")->result();  

            return ['message' =>$result, 'statusCode'=>$result ? REST::HTTP_OK : REST::HTTP_NOT_FOUND];

        }

    
        //Validation
        private function validation(){
            return [
                ['field' => 'title','label' => 'title','rules' => 'required'],
                ['field' => 'per_km','label' => 'per_km','rules' => 'required'],
                ['field' => 'per_kg','label' => 'per_kg','rules' => 'required']          
            ];
        }

   }   

