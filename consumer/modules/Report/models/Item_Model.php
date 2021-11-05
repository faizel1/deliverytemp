<?php
use Restserver\Libraries\REST;

   class Item_Model extends Main_Model
   {
        public function __construct()
        {  
            parent::__construct();
        }

        public function Saves($post){            
            return $this->save('tbl_vehicle',$post,$this->validation());     
        }

        public function List(){ 
            $select = "";

            $result = $this->db->select($select)->get("tbl_vehicle")->result();  

            return ['message' =>$result, 'statusCode'=>$result ? REST::HTTP_OK : REST::HTTP_NOT_FOUND];

        }

        public  function Load_drivers(){
            $result = $this->db->select("id value, full_name text")
                            ->order_by("created_at")
                            ->get_where('tbl_account',['type'=>'driver'])
                            ->result();

            return ['message' =>$result, 'statusCode'=>$result ? REST::HTTP_OK : REST::HTTP_NOT_FOUND];
                            
        }

        public  function Load_vehicle_type(){
            $result = $this->db->select("id value, title text")
                               ->get_where('tbl_vehicle_type')
                               ->result();

            return ['message' =>$result, 'statusCode'=>$result ? REST::HTTP_OK : REST::HTTP_NOT_FOUND];
                                
        }
    
        //Validation
        private function validation(){
            return [
                ['field' => 'plate_number','label' => 'plate_number','rules' => 'required'],
                ['field' => 'model','label' => 'model','rules' => 'required'],
                ['field' => 'load_capacity','label' => 'load_capacity','rules' => 'required'],
                ['field' => 'chassis_number','label' => 'chassis_number','rules' => 'required'],
                ['field' => 'owner_full_name','label' => 'owner_full_name','rules' => 'required'],
                ['field' => 'owner_email','label' => 'owner_email','rules' => 'required|valid_email'],
                ['field' => 'owner_phone_number','label' => 'owner_phone_number','rules' => 'required'],
                ['field' => 'vehicle_type_id','label' => 'vehicle_type_id','rules' => 'required']
            ];
        }

   }   
