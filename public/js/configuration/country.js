/**
 * Created by Nishan on 6/6/2016.
 */

$(document).ready(function(){
    var dataTable = {}
    ajax_Datatable_Loader();

    // jquery validation plugin
    $("#add_country").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="country_name"){
                error.insertAfter(document.getElementById('err-name'));
            } else if(element.attr('name')=="visibility"){
                error.insertAfter(document.getElementById('err-visibility'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "country_name" : {
                required: true
            },
            "visibility" : {
                required: true
            }
        },
        messages: {
            "country_name": {
                required: "Please insert country"
            },
            "visibility" : {
                required: "Please specify visibility"
            }
        }
    });
});

$(document).on('click', '#save_country', function(){
    var visibility = $('#visibility').val();
    var country_name = $('#country_name').val();

    if($("#add_country").valid()){
        $.ajax({
            url: "/country/new/store",
            type: 'POST',
            dataType: 'json',
            data : { visibility:visibility,country_name:country_name },
            success: function(result){
                $('#country_name').val('');
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }

            }
        });
    }
});

function activeCountry(status, country_id) {
    if(status==1){
        $.ajax({
            url: '/configuration/country/manage',
            type: 'GET',
            data: { id : country_id, status: 2 },
            success: function(response) {
                if(response.success == 'true') {
                    $('#country-table').dataTable().fnDestroy();
                    ajax_Datatable_Loader();

                    var msg = response.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                } else {
                    var msg = response.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            }
        });
    }
    else if(status==2)
    {
        $.ajax({
            url: '/configuration/country/manage',
            type: 'GET',
            data: { id : country_id, status: 1 },
            success: function(response) {
                if(response.success == 'true') {
                    $('#country-table').dataTable().fnDestroy();
                    ajax_Datatable_Loader();

                    var msg = response.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                } else {
                    var msg = response.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            }
        });
    }
}

//load bootstrap datatable
function ajax_Datatable_Loader(){
    dataTable =  $('#country-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : false,
        "sAjaxSource":"/country/filter/"

    } );
    $("#country-table_filter").css("display","none");
}

function msgAlert(msg, msg_type) {
    toastr.options = {
        "debug": false,
        "newestOnTop": false,
        "positionClass": "toast-top-center",
        "closeButton": true,
        "toastClass": "animated fadeInDown",
    };
    if(msg_type == 'success') {
        toastr.success(msg);
    } else if(msg_type == 'error') {
        toastr.error(msg);
    }

}