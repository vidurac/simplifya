

$(function () {
    $('.dropzone a').click(function(event) {
        event.preventDefault();
        $(this).parents('.dropzone').find('input[type="file"]').trigger('click');
    });


    $('.dropzone input[type="file"]').change(function(e) {
        var file = e.target.files[0];
        file.preview = URL.createObjectURL(file);
        $(this).parents('.dropzone').find('img').attr('src', file.preview).end().show();

    });



    $("#update-user-profile").submit(function(e) {
        var newPassword = $("#newPassword").val();
        var confirmPassword = $("#confirmPassword").val();
        if(!$('#update-user-profile').valid()){
            e.preventDefault();
        }
        else if(newPassword != ""){
            if(newPassword != confirmPassword){
                e.preventDefault();
                swal({
                    title: "Error!",
                    text: "Both New Password and Confirm Password Should be Matched."
                });
            }
        }
    });

    $("#update-user-profile").validate({
        rules: {
            name:{
                required: true
            }
        },
        messages: {
            name : {
                required: "Please enter the name"
            }
        },
        highlight: function(element) {
            $(element).closest('.form-control').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-control').removeClass('has-error');
        }
    });

    $("#profilePicture").change(function(){
        var imageSize = this.files[0].size / 1024;

        if(imageSize > 1024* 5){
            $(".img-responsive").attr('src', '');
            $(".img-responsive").attr('style', 'height:220px');
            swal({
                title: "Error!",
                text: "File size should be less than 5MB."
            });
        }




    });
});

