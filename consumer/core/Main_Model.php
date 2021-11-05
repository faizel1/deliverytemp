<?php
    use Restserver\Libraries\REST;

   class Main_Model extends CI_Model
   {
        public function __construct(){ 
           parent::__construct();

           $this->load->library('File');
           $this->load->library('logger');
           
        }
 
        // this method is used to save sigular value (record) at a time
        // $table : for store record (data)
        // $rule : for validation 
        // $data : for actual data to be store in the table 

        public function Save($table, $data, $rule, $path = ""){
            $result = $this->logger->validate_form($rule, $data, $table);
                            
            if($result['status']) {
                
                $this->db->trans_begin();
                    
                try {  

                    if(empty($data['id'])){

                        $data['id'] = uuid_gen();
                        $this->db->insert($table, $data);
        
                    } else {
                        $this->db->update($table, $data, ['id' => $data['id']]);
                    }

                    if($this->db->trans_status()){
                        $this->db->trans_commit();

                        return ['status'=>true, 'statusCode'=>REST::HTTP_OK, 'message' =>"saved successfully."];

                    } else {
                        $this->logger->database_error();

                        return ['status'=>false, 'statusCode'=>REST::HTTP_INTERNAL_SERVER_ERROR, 'message' =>"unable to save the data."];

                    }
        
                } catch (Exception $exc) {
                    $this->logger->exception_error($exc);
        
                    return ['status'=>false, 'statusCode'=>REST::HTTP_INTERNAL_SERVER_ERROR, 'message' =>"unable to save the data."];
                }
                
            } else {
                return $result;
            } 
     
        }


        // this method is used to save multiple value (records) at a time
        // $table : for store primary record
        // $rule : for validation 
        // $data : for actual data to be store in the tables 
        // $fk_table : for to store secondery (detail) data
        // $fk : for a column for link two table (forign key)

        public function Master_Detail_Save($table, $data, $rule, $fk_table, $fk){
            
            $result = $this->logger->validate_form($rule, $data, $table);
                            
            if($result['status']) {
                    
                try { 

                    $deleted = $data['deleted'];  $detail = $data['detail'];
                    unset($data['detail'], $data['deleted']);

                    $id = is_null($data['id']) ? uuid_gen() : $data['id'];

                    foreach($detail as &$value){
                        if(is_null($value['id'])){

                            $value['id'] = uuid_gen();
                            $value[$fk] = $id; 
                            
                            $new[][] = $value;
                        }
                    }

                    $this->db->trans_begin();

                    if(is_null($data['id'])){

                        $data['id'] = $id;

                        $this->db->insert($table, $data);
                        $this->db->trans_status() ? $this->db->insert_batch($fk_table, $new) : null;

                    } else {

                        $this->db->update($table, $data, ['id' => $data['id']]);

                        $this->db->trans_status() ? (count($detail) > 0 ? $this->db->update_batch($fk_table, $detail,'id') : null) : null;
 
                        $this->db->trans_status() ? (count($new) > 0 ? $this->db->insert_batch($fk_table, $new) : null) : null;

                        $this->db->trans_status() ? (count($deleted) > 0 ? $this->db->where_in("id",$deleted)->delete($fk_table) : null) : null;

                    }
        
                    if($this->db->trans_status()){
                        $this->db->trans_commit();

                        return ['status'=>true,  'statusCode'=>REST::HTTP_OK, 'message' =>"saved successfully."];

                    } else {
                        $this->logger->database_error();
                        
                        $this->db->trans_rollback();

                        return ['status'=>false,  'statusCode'=>REST::HTTP_INTERNAL_SERVER_ERROR, 'message' =>"unable to save the data."];
                    }
        
                } catch (Exception $exc) {
                    $this->logger->exception_error($exc);
        
                    return ['status'=>false,'statusCode'=>REST::HTTP_INTERNAL_SERVER_ERROR,'message' =>"unable to save the data."];
                }
                
            } else {
                return $result;

            } 
    
        }
    
        // to do
        public function Batch_Save(){

        }
        // $table : for from where the data to be deleted
        // $ids : are array of ids, for which the data to be deleted (ids can be folder name)
        // $file : if true delete also folder that hold actual file related to the id

        public function Delete($table, $ids, $file = false){    
                    
            try {

                if($file){

                    $result = $this->file->delete($ids);

                    if(!$result['status']){
                        return ['status'=>false, 'statusCode'=>REST::HTTP_CONFLICT, 'message' =>"unable to delete file. please review folder permissions."];
                    }
                } 

                $this->db->trans_begin();

                $ids = explode(",",$ids);
                $this->db->simple_query("delete from $table where id in ($ids)");

                $error = $this->db->error();

                $code = $error['code'];
                
                if($code === 0) {
                    $message = "deleted successfully.";

                } else if ($code === 1451) {
                    $message = "you can not delete this record, because other record use this record as a source of data.";
    
                } else {
                    $message = "unable to delete this record.";

                }

                !$code ? $this->db->trans_commit() : $this->db->trans_rollback();

                $this->logger->database_error();

                return ['status'=>!$code ? true : false, 'message' =>$message,
                        'statusCode'=>!$code ? REST::HTTP_OK : REST::HTTP_INTERNAL_SERVER_ERROR];

            } catch (Exception $exc) {
                $this->logger->exception_error($exc);

            }
            
        }

        // this method is return specific single row of record from $table (without & with relationship)
        // if the $fk_table is null it return row without relationship vice versa
        // $table : for primary table
        // $id : are id of the primary table
        // $fk_table : are secondary table join by relationship with primary table
        // $fk : are id of the secondary table

        public function Detail($table, $id, $related_table = []){ 
            $result =  $this->db->get_where($table, ["id" => $id])->row();

            if(count($related_table) > 0){
                foreach($related_table as $key => $value){
                    if($value['relation'] === 'child' ){
                        $this->db->where($value['fk_id'], $id);
    
                    } else {
                        $this->db->where('id', $result->$value['fk_id']);
    
                    }
    
                    $result->$key = $this->db->from($value['fk_table'])->result();
    
                }
            }

            return ['statusCode'=>$result ? REST::HTTP_OK : REST::HTTP_NOT_FOUND, 'message' =>$result];

        }


   }   