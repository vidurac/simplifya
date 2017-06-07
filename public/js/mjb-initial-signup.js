/** MJB Registration **/

$(document).on('click', '#mjb_reg_btn', function(){
    mjbCompanyRegistration();
});

    function mjbCompanyRegistration() {
        var entity_type = $('#entity_type').val();
        var name_of_business = $('#name_of_business').val();
        var company_registration_no = $('#company_registration_no').val();
        var your_name = $('#your_name').val();
        var email = $('#email').val();
        var conf_email = $('#conf_email').val();
        var password = $('#password').val();
        var conf_password = $('#conf_password').val();
        console.log("MJB REG")
        register_form = $("#companyRegForm");
        //if(mjbRegistrationValidate(register_form) == true){

        $.ajax({
            url: "/company/registration",
            type: 'POST',
            dataType: 'json',
            data: {
                entity_type: entity_type,
                name_of_business: name_of_business,
                company_registration_no: company_registration_no,
                your_name: your_name,
                email: email,
                conf_email: conf_email,
                password: password,
                conf_password: conf_password,
            },
            beforeSend: function () {
                $(".splash").show();
            },
            success: function (result) {
                var url = window.location.host;
                if (result.success == 'true') {
                    window.location = 'http://' + url + '/thanks';
                    return false;
                } else {
                    if (result.is_redirect == 'false') {
                        var msg = result.message;
                        var msg_type = 'error';
                        msgAlert(msg, msg_type);
                        $(".splash").hide();
                    } else {
                        window.location = 'http://' + url + '/error';
                        var msg = result.message;
                    }
                }
            },
            error: function (result) {
                $(".splash").hide();
            }
        });
        // }

    }

    function mjbRegistrationValidate(mjbregister_form) {

        //register_form = $("#companyRegForm");
        mjbregister_form.validate({
            rules: {
                name_of_business: {
                    required: true
                },
                your_name: {
                    required: true
                },
                company_registration_no: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                conf_email: {
                    equalTo: "#email"
                },
                password: {
                    required: true
                },
                conf_password: {
                    equalTo: "#password"
                }
            },
            // Specify the validation error messages
            messages: {
                name_of_business: {
                    required: "The name of business is required"
                },
                your_name: {
                    required: "The your name is required"
                },
                company_registration_no: {
                    required: "The company registration number is required"
                },
                email: {
                    required: "The email is required"
                },
                conf_email: {
                    required: "The confirm email is required"
                },
                password: {
                    required: "The password is required"
                },
                conf_password: {
                    required: "The confirm password is required"
                }
            }
        });

        return mjbregister_form.valid();

    }
