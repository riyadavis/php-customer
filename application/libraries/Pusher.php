<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
 class Pusher{

        public function push($message)
        {
          require (__DIR__ . '/vendor/autoload.php');    
          $options = array(
            'cluster' => 'ap2',
            'useTLS' => true
          );
          $pusher = new Pusher\Pusher(
            '8d2c0950595211c09ec7',
            '81a3cd66af36e264dc04',
            '838370',
            $options
          );
        
          $data['message'] = 'hello world';
          $pusher->trigger('my-channel', 'my-event', $data);
        }   
    }
?>