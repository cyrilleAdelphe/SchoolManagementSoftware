<?php

class GcmController extends Controller
{
    
    public function send_gcm_notify($reg_id, $message) 
    {
       $reg_id = is_array($reg_id) ? $reg_id : (array) $reg_id;
         
        if(count($reg_id))
        {
            
            $fields = array(
                'registration_ids'  => array_values($reg_id),//array($reg_id),
                'data'              => array('title' => $message, 'body' => $message),
            );

            $headers = array(
                'Authorization:key=' . GOOGLE_API_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        
            $result = curl_exec($ch);
            /*echo '<pre>';
            print_r($result);
            die();*/
            
            
            if ($result === FALSE) 
            {
                $arr = array('status' => 'error', 'message' => curl_error($ch));
                return json_encode($arr);
            }

            curl_close($ch);
        
            $arr = array('status' => 'success', 'message' => $result);
            return json_encode($arr);    
        }
        else
        {
            $arr = array('status' => 'error', 'message' => 'No registration id found');
            return json_encode($arr);
        }
        
        //die();
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public function send($reg_id, $msg, $user_ids = array(), $user_group = 'student')
    {
        //$reg_id = Input::get('reg_id');
        //$msg = Input::get('json_string');

        //TODO: add code to save the user ids 
        if(is_null($user_ids) || is_null($msg))
        {
            $arr = array('status' => 'error', 'message' => 'RegID and/or Message not sent');
            return json_encode($arr);
            //die();
        }
        else
        {
            $result = $this->send_gcm_notify($reg_id, $msg);
            $this->storeInTable($user_ids, $msg, $user_group);
            $arr = array('status'=>'success', 'message' => $result);
        }
        
        return $arr;
    }

    public function storeInTable($user_ids, $message, $user_group)
    {
        $user_ids = is_array($user_ids) ? $user_ids : (array) $user_ids;
        
        $last_message_group_id = SavePushNotifications::max('message_group_id');
        if (!$last_message_group_id)
        {
            $last_message_group_id = 0;
        }

        $message_group_id = $last_message_group_id + 1;

        $base_controller = new BaseController;

        //also store sender_id and sender_role
        $current_user = HelperController::getCurrentUser();
        try 
        {
            foreach($user_ids as $user_id)
            {
                $data = array();
                $data['user_id'] = $user_id;
                $data['user_group'] = $user_group;
                $data['message'] = $message;
                $data['message_group_id'] = $message_group_id;
                $data['is_active'] = 'yes';
                $data['sender_id'] = $current_user->user_id;
                $data['sender_role'] = $current_user->role;
                
                $base_controller->storeInDatabase($data, $model_name = 'SavePushNotifications');
            }    

           
        } 
        catch (Exception $e) 
        {
           echo $e->getMessage();
           die();
            //do nothing   
        }
    }

    public function extractUrl()
    {
       $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        if ($result === FALSE) {
            $arr = array('status' => 'error', 'message' => curl_error($ch));
            return json_encode($arr);

            die();
        }        
       
    }


}

?>