/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {
    
    $("#loginForm").validate({
        
        // Specify the validation rules
        rules: {
            "email" : {
                required: true,
                email: true
            },
            "password" : {
                required: true
            }
         },
        // Specify the validation error messages
        messages: { 
            "email": {
                required: "The email is required"
            },
            "password" : {
                required: "The password is required"  
            }
        }
    });
 });