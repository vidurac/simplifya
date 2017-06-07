$(function(){
    $("#resetPasswordId").click(function(e){
        e.preventDefault();
        resetPassword($("#resetPasswordEmail").val());
    });

    $("#resetPasswordForm").validate({
        rules: {
            resetPasswordEmail: {
                required: true,
                email: true
            }
        }
    });

    $("#resetPasswordFromEmail").click(function(e){
        if($("#password").val() != $("#conf_password").val()){
            e.preventDefault();
            swal({
                title: "Error!",
                text: "Both Password and Confirm Password Should be Same"
            });

        }
        else if(!$("#resetPasswordFromEmailForm").valid()){
            e.preventDefault();
        }
    });

    $("#resetPasswordFromEmailForm").validate({
        rules: {
            password: {
                required: true
            },
            password: {
                conf_password: true
            }
        }
    });


});


function resetPassword(email){
    if($("#resetPasswordForm").valid()){
        $.ajax({
            url: "/resetPassword/reset",
            type: 'POST',
            dataType: 'json',
            data : {email: email},
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result){
                $(".splash").hide();

                if(result.success == "false"){
                    swal({
                        title: "Error!",
                        text: result.message
                    });
                }

                if(result.success == "true"){
                    $("#resetPasswordEmail").val("");
                    $("#resetPasswordStatus").append('<div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong> An email has been sent to < '+result.email +'> with instructions to reset password. </strong> </div>');
                }

            },
            error: function(xhr){
                $(".splash").hide();
            }
        });
    }

}