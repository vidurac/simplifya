/**
 * Created by Nishan on 5/6/2016.
 */
$(function () {
    jQuery('#startDatePicker').datetimepicker();

    getLicenses();

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    // Initialize summernote plugin
    $('.summernote').summernote({
        disableDragAndDrop: true,
        shortcuts: false,
        toolbar: [
            ['headline', ['style']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['textsize', ['fontsize']],
            ['alignment', ['ul', 'ol', 'paragraph', 'lineheight']]
        ]
    });
    var sHTML = $('.summernote').code();

    $('.summernote1').summernote({
        disableDragAndDrop: true,
        shortcuts: false,
        toolbar: [
            ['headline', ['style']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['textsize', ['fontsize']],
            ['alignment', ['ul', 'ol', 'paragraph', 'lineheight']]
        ]
    });

    $('.summernote2').summernote({
        airMode: true
    });

    // jquery validation plugin
    $("#companyReqForm").validate({

        // Specify the validation rules
        rules: {
            "company_name":{
                required: true
            },
            "company_location":{
                required: true
            },
            "message" :{
                required: false
            }
        },
        // Specify the validation error messages
        messages: {
            "company_name":{
                required: "The company name is required"
            },
            "company_location":{
                required: "The company location is required"
            }
        },
        submitHandler:function(form){
            $(".splash").show();
            $(form).ajaxSubmit({
                dataType: "json",
                type: 'POST',
                success: function(data){
                    $(".splash").hide();
                    Command: toastr["success"]("Audit request has been sent successfully", "Add New Audit");
                    window.location="/request/manage";
                    clear_fields();
                },
                error: function(){
                    Command: toastr["error"]("Audit Report Insertion Failed", "Add New Audit");
                },
                beforeSend: function() {

                }
            });
        }
    });

    $('.note-toolbar').removeClass('btn-toolbar');
});

/**
 * clear fields after submission
 */
function clear_fields(){
    $('#company_name').val('');
    $('#entity_type').val('');
    $('.note-editable').html('');
}

function getLicenses() {
    var location = $('#company_location').val();
    $.ajax({
        url: '/get/company/licenses',
        type: 'POST',
        data: { location_id : location },
        success: function(response) {
            for (i = 0; i < response.data.length; i++)
            {
                $('#license_types').append('<option value="'+ response.data[i].master_license.id +'">'+ response.data[i].master_license.name +'</option>');
            }
        }
    });
}

$(document).on('change', '#license_types', function(){
    var license_id = $('#license_types').val();

    $.ajax({
        type: 'POST',
        url: '/get/licenses/amount',
        data: { location_id : license_id },
        success: function(response) {
            $('#cost_amount').html(response.data[0].checklist_fee);
        }
    });

});

function getMJBLocation()
{
    var company_id  = $('#company_name').val();

    $.ajax({
        type: 'POST',
        url: '/company/location/'+company_id,
        success: function(response) {
            $('#entity_type').html('');
            if(response.message.length > 0){
                for (i = 0; i < response.message.length; i++)
                {
                    $('#entity_type').append('<option value="'+ response.message[i].id +'">'+ response.message[i].name +'</option>');
                }
            }else{
                $('#entity_type').html('');
                $('#entity_type').append('<option value="">Select</option>');
            }

        }
    });
}
