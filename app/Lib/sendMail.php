<?php namespace App\Lib;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sendMail
 *
 * @author Harsha
 */
use Mail;

class sendMail {
    
    public function mailSender($layout, $email, $name, $subject, array $data){
		
     	Mail::send($layout, array('name' => $name, 'data' => $data),
        	function($message) use ($email, $name, $subject, $data) {

        		$message->from($data['from'], $data['system']);
           		$message->to($email, $name)->subject($subject);

    		});
    }
}
