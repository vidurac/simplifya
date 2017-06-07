/**
 * Created by Nishan on 6/6/2016.
 */

$(document).ready(function(){
    var dataTable = {}
    ajax_Datatable_Loader();

    //append all countries to country manager country filter drop-down
    get_all_country();

    // jquery validation plugin
    $("#add_state").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="state_name"){
                error.insertAfter(document.getElementById('err-name'));
            } else if(element.attr('name')=="country_id"){
                error.insertAfter(document.getElementById('err-country'));
            } else if(element.attr('name')=="visibility"){
                error.insertAfter(document.getElementById('err-visibility'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "state_name" : {
                required: true
            },
            "country_id" : {
                required: true
            },
            "visibility" : {
                required: true
            }
        },
        messages: {
            "state_name": {
                required: "Please insert state"
            },
            "country_id": {
                required: "Please specify a country"
            },
            "visibility" : {
                required: "Please specify visibility"
            }
        }
    });
});

$(document).on('click', '#save_state', function(){
    var visibility = $('#visibility').val();
    var state_name = $('#state_name').val();
    var country_id = $('#country_id').val();

    if($("#add_state").valid()){
        $.ajax({
            url: "/state/new/store",
            type: 'POST',
            dataType: 'json',
            data : { visibility:visibility, state_name:state_name, country_id:country_id },
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#state_name').val('');
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                    $('#state_name').val('');
                }

            }
        });
    }
});

function editStateUpdate(state_id) {
    var dataset = {};
    //serialize form data
    var data = $( "#state-manage-form" ).serializeArray();

    $.each(data, function() {
        if (dataset[this.name] !== undefined) {
            if (!dataset[this.name].push) {
                dataset[this.name] = [dataset[this.name]];
            }
            dataset[this.name].push(this.value || '');
        } else {
            dataset[this.name] = this.value || '';
        }
    });

    $.ajax({
        url: "/state/edit/store",
        type: 'POST',
        dataType: 'json',
        data : { data:dataset, state_id:state_id },
        success: function(result){
            if(result.success == 'true') {
                var msg = result.message;
                var msg_type = 'success';
                msgAlert(msg, msg_type);
                $('#state_name').val('');
                $('#state-manage-model').modal('hide');
                $('#state-table').dataTable().fnDestroy();
                ajax_Datatable_Loader();
            } else {
                var msg = result.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
                $('#state_name').val('');
            }
        }
    });
}

/**
 * Individual state edit view popup
 * @param state_id
 */
function editState(state_id) {
   
    $.ajax({
        url: "/configuration/state/edit",
        type: 'POST',
        dataType: 'json',
        data : { state_id:state_id },
        success: function(result){
            if(result.success == 'true') {
                $('#edit_state_name').val(result.data[0].state_name);
                $('#edit-country').val(result.data[0].country_id);
                if(result.data[0].state_status==1)
                {
                    $('#visibility_yes').parent().addClass('checked');
                    $('#visibility_yes').prop('checked', true);
                }else{
                    $('#visibility_no').prop('checked', true);
                    $('#visibility_no').parent().addClass('checked');
                }

                $('#edit-state-btn').attr('onclick','editStateUpdate('+ state_id +');');

                $('#state-manage-model').modal('show');
            } else {

            }

        }
    });
}

//Close edit state model
$(document).on('click', '.close-edit-state-form', function () {
    $('#visibility_yes').parent().removeClass('checked');
    $('#visibility_no').parent().removeClass('checked');

    $('#state-manage-model').modal('hide');
});

//load bootstrap datatable
function ajax_Datatable_Loader(){
    dataTable =  $('#state-table').DataTable( {
        "dom": '<"toolbar">frtip',
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : false,
        "sAjaxSource":"/state/filter/"
    } );
    $("#state-table_filter").css("display","none");
    $("div.toolbar").html('' +
        '<div class="row">' +
        '   <div class="col-md-12 pull-right">' +
        '       <div class="col-md-5"><select name="country_search" class="form-control" id="country_search"></select></div>' +
        '       <div class="col-md-5"><select name="state_search" class="form-control" id="state_search"></select></div>' +
        '       <div class="col-md-2"><a class="btn btn-sm btn-default" id="search_state_table">Search</a></div>' +
        '   </div>' +
        '</div>');

}

/**
 * get all countries
 */
function get_all_country() {
    $.ajax({
        url: "/get/countryList",
        type: 'GET',
        dataType: 'json',
        success: function(result){
            $('#country_search').append('<option value="">Select Country</option>');
            $('#state_search').append('<option value="">Select State</option>');
            $.each(result, function (index, value) {
                $('#country_search').append('<option value="'+value.id+'">'+ value.name +'</option>');
            })
        },
        beforeSend: function () {

        }
    });
}

/**
 * on search button click filter applied
 */
$(document).on('click', '#search_state_table', function () {
    var country_id = $('#country_search').val();
    var state_id = $('#state_search').val();
    dataTable.destroy();
    ajax_Datatable_Loader();
    //append all countries to country manager country filter drop-down
    get_all_country();

    dataTable.columns(0).search(state_id, true).draw();
    dataTable.columns(1).search(country_id, true).draw();
});

/**
 * Location on change function
 * Get states according to country change
 */
$(document).on('change', '#country_search', function () {
    var country_id = $(this).val()

    $.ajax({
        url: "/question/getStates",
        type: 'GET',
        dataType: 'json',
        data : { countryId:country_id },
        success: function(result){
            if(result.success == 'true') {
                var states = result.data.master_states;
                var states_arr = $.makeArray(states);
                $('#state_search').html('<option value="">Select State</option>');
                $.each(states_arr, function( index, value ) {
                    $('#state_search').append('<option value="'+ value.id +'">'+ value.name +'</option>')
                });
            } else {
                var msg = result.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
                $('#city_id').val('');
            }
        }
    });
});

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