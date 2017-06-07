/**
 * Created by Nishan on 6/6/2016.
 */

$(document).ready(function(){
    var dataTable = {}
    ajax_Datatable_Loader();

    //Append all countries to country filter
    get_all_country();

    var i=1;
    $("#add_row").click(function(){
        $('#addr'+i).html("<td><input type='text' name='city_id' class='form-control'/></td>");
        $('#tab_city_list').append('<tr id="addr'+(i+1)+'"></tr>');
        i++;
    });
    $("#delete_row").click(function(){
        if(i>1){
            $("#addr"+(i-1)).html('');
            i--;
        }
    });

    // jquery validation plugin
    $("#add_city").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="country_id"){
                error.insertAfter(document.getElementById('err-country'));
            } else if(element.attr('name')=="state_id"){
                error.insertAfter(document.getElementById('err-state'));
            } else if(element.attr('name')=="city_id"){
                error.insertAfter(document.getElementById('err-city'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "country_id" : {
                required: true
            },
            "state_id" : {
                required: true
            },
            "city_id" : {
                required: true
            }
        },
        messages: {
            "state_id": {
                required: "Please specify a state"
            },
            "country_id": {
                required: "Please specify a country"
            },
            "city_id" : {
                required: "Please specify one or more city"
            }
        }
    });
});

/**
 * get all countries
 */
function get_all_country() {
    $.ajax({
        url: "/get/countryList",
        type: 'GET',
        dataType: 'json',
        success: function(result){
            $('#country').append('<option value="">Select Country</option>');
            $('#state_id').append('<option value="">Select State</option>');
            $.each(result, function (index, value) {
                $('#country').append('<option value="'+value.id+'">'+ value.name +'</option>');
            })
        },
        beforeSend: function () {

        }
    });
}

/**
 * on search button click filter applied
 */
$(document).on('click', '#search_city_table', function () {
    var country_id = $('#country').val();
    var state_id = $('#state_id').val();
    dataTable.destroy();
    ajax_Datatable_Loader();
    //append all countries to country manager country filter drop-down
    get_all_country();

    dataTable.columns(0).search(country_id, true).draw();
    dataTable.columns(1).search(state_id, true).draw();
});

/**
 * Location on change function
 * Get states according to country change
 */
$(document).on('change', '#country', function () {
    var country_id = $(this).val()

    $.ajax({
        url: "/question/getStates",
        type: 'GET',
        dataType: 'json',
        data : { countryId:country_id },
        success: function(result){
            if(result.success == 'true') {
                $(".splash").hide();
                var states = result.data.master_states;
                var states_arr = $.makeArray(states);
                $('#state_id').html('<option value="">Select State</option>');
                $('#state_selection').html('<option value="">Select State</option>');
                $('#city').html('<option value="">Select City</option>');
                $.each(states_arr, function( index, value ) {
                    $('#state_id').append('<option value="'+ value.id +'">'+ value.name +'</option>')
                    $('#state_selection').append('<option value="'+ value.id +'">'+ value.name +'</option>')
                });
            } else {
                var msg = result.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
                $('#city_id').val('');
            }
        },
        beforeSend: function () {
            $(".splash").show();
        }
    });
});

/**
 * Location on change function
 * Get states according to country change
 */
$(document).on('change', '#country_select', function () {
    var country_id = $(this).val()

    $.ajax({
        url: "/question/getStates",
        type: 'GET',
        dataType: 'json',
        data : { countryId:country_id },
        success: function(result){
            if(result.success == 'true') {
                $(".splash").hide();
                var states = result.data.master_states;
                var states_arr = $.makeArray(states);
                $('#state_id').html('<option value="">Select State</option>');
                $('#city').html('<option value="">Select City</option>');
                $.each(states_arr, function( index, value ) {
                    $('#state_id').append('<option value="'+ value.id +'">'+ value.name +'</option>')
                });
            } else {
                var msg = result.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
                $('#city_id').val('');
            }
        },
        beforeSend: function () {
            $(".splash").show();
        }
    });
});

/**
 * Location on change function
 * Get states according to country change
 */
$(document).on('change', '#state_id', function () {
    var state_id = $(this).val()

    $.ajax({
        url: "/question/getCities",
        type: 'GET',
        dataType: 'json',
        data : { stateId:state_id },
        success: function(result){
            if(result.success == 'true') {
                $(".splash").hide();
                var cities = result.data.master_city;
                var city_arr = $.makeArray(cities);
                $('#city_id').html('<option value="">Select City</option>');
                $('#city').html('<option value="">Select City</option>');
                $.each(city_arr, function( index, value ) {
                    $('#city').append('<option value="'+ value.id +'">'+ value.name +'</option>')
                });
            } else {
                var msg = result.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
                $('#city_id').val('');
            }
        },
        beforeSend: function () {
            $(".splash").show();
        }
    });
});

/**
 * Save all cities & assign them to country & state
 */
$(document).on('click', '#save_city', function(){
    var city_data = {};
    var all_data = $('#add_city').serializeArray();

    $.each(all_data, function() {
        if (city_data[this.name] !== undefined) {
            if (!city_data[this.name].push) {
                city_data[this.name] = [city_data[this.name]];
            }
            city_data[this.name].push(this.value || '');
        } else {
            city_data[this.name] = this.value || '';
        }
    });

    if($("#add_city").valid()){

        $.ajax({
            url: "/city/new/store",
            type: 'POST',
            dataType: 'json',
            data : { dataset:city_data },
            success: function(result){
                if(result.success == 'true') {

                    $('select option[value=""]').attr("selected",true);

                    $('#state_id')
                        .find('option')
                        .remove()
                        .end()
                        .append('<option value="">Select State</option>')
                        .val('')
                    ;

                    $("#tab_city_list tr").remove();

                    $('#tab_city_list').append('<tr><th class="text-center">City Name</th></tr><tr id="addr0"></tr>');

                    $('#addr0').html("<td><input type='text' name='city_id' class='form-control'/></td>");

                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#city_id').val('');
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                    $('#city_id').val('');
                }
            }
        });
    }
});

/**
 * Edit city model data passing
 * @param city_id
 */
function editCityUpdate(city_id) {
    var dataset = {};
    //serialize form data
    var data = $( "#city-manage-form" ).serializeArray();

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
        url: "/city/edit/store",
        type: 'POST',
        dataType: 'json',
        data : { data:dataset, city_id:state_id },
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
 * @param city_id
 */
function editState(city_id) {
   
    $.ajax({
        url: "/configuration/city/edit",
        type: 'POST',
        dataType: 'json',
        data : { city_id:city_id },
        success: function(result){
            if(result.success == 'true') {
                $('#country').val(result.data[0].country_id);
                $('#state_name').val(result.data[0].state_id);
                //need to set city data

                $('#city-manage-model').modal('show');
            } else {

            }

        }
    });
}

//Close edit state model
$(document).on('click', '.close-edit-city-form', function () {
    $('#city-manage-model').modal('hide');
});

//load bootstrap datatable
function ajax_Datatable_Loader(){
    dataTable =  $('#city-table').DataTable( {
        "dom": '<"toolbar">frtip',
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : false,
        "sAjaxSource":"/city/filter/"

    } );
    $("#city-table_filter").css("display","none");
    $("div.toolbar").html( '' +
    '<div class="row">' +
    '   <div class="col-md-12 pull-right">' +
    '       <div class="col-md-5"><select name="country" class="form-control" id="country"></select></div>' +
    '       <div class="col-md-5"><select name="state_id" class="form-control" id="state_id"></select></div>' +
    '       <div class="col-md-2"><a class="btn btn-sm btn-default" id="search_city_table">Search</a></div>' +
    '   </div>' +
    '</div>');
}

/**
 * Edit city popup
 * @param city_id
 * @param state_id
 */
function editCity(city_id, state_id, country_id){

    $.ajax({
        url: "/question/getStates",
        type: 'GET',
        dataType: 'json',
        data : { countryId:country_id },
        beforeSend: function () {
            $(".splash").show();
        },
        success: function(result){
            if(result.success == 'true') {
                $(".splash").hide();
                var states = result.data.master_states;
                var states_arr = $.makeArray(states);

                //$('#country_id').append('<option value="">Select Country</option>');
                $('#state_selection').html('<option value="">Select State</option>');

                $.each(states_arr, function( index, value ) {
                    $('#state_selection').append('<option value="'+ value.id +'">'+ value.name +'</option>')
                });

                $('#country_id').val(country_id);
                $('#state_selection').val(state_id);
                $('#state-id').val(state_id);

                $.ajax({
                    url: "/question/getCities",
                    type: 'GET',
                    dataType: 'json',
                    data : { stateId:state_id },
                    success: function(result){
                        $('.input_fields_wrap').html('');
                        if(result.success == 'true') {
                            $(".splash").hide();
                            var cities = result.data.master_city;
                            var city_arr = $.makeArray(cities);

                            $.each(city_arr, function( index, value ) {
                                $('#tab_city_list').append('<div class="row form-group">' +
                                    '   <div class="col-md-9">' +
                                    '       <input type="text" name="city[]" class="form-control city-'+ value.id +'" data-id="'+ value.id +'" value="'+ value.name +'"/>' +
                                    '   </div>' +
                                    '   <div class="col-md-3">' +
                                    '       <a href="#" class="remove_city_field btn btn-danger" data-id="'+ value.id +'">Remove</a>' +
                                    '   </div>' +
                                    '</div>');
                            });
                            $('#tab_city_list').append('<div class="row form-group"><div class="col-md-12"><a class="add_field_button btn btn-info form-control">Add City</a>' +
                                '   </div></div>');
                            add_city_field();
                        } else {
                            var msg = result.message;
                            var msg_type = 'error';

                            msgAlert(msg, msg_type);
                        }
                    },
                    beforeSend: function () {
                        $(".splash").show();
                    }
                });
            } else {
                var msg = result.message;
                var msg_type = 'error';
                msgAlert(msg, msg_type);
                $('#city_id').val('');
            }
        }
    });

    $('#city-manage-model').modal('show');
}

$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="row form-group"><div class="col-md-9"><input type="text" name="city[]" class="form-control"/></div><div class="col-md-3"><a href="#" class="remove_field btn btn-danger">Remove</a></div></div>'); //add input box
        }
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent().parent('div').remove(); x--;
    });

    $(wrapper).on("click",".remove_city_field", function(e){ //user click on remove text
        e.preventDefault();
        var city_id = $(this).data('id');
        $.ajax({
            url: "/configuration/checkCitiesOccupied",
            type: 'GET',
            dataType: 'json',
            data: {city_id: city_id},
            success: function (result) {
                swal({
                        title: "Are you sure?",
                        text: "Do you want to remove this city?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            if(result.status==1){
                                swal("Deleted!", "City has been queued to remove. Update to save action.", "success");
                                $('.city-'+city_id).parent().parent('div').remove(); x--;
                            }else if(result.status==2){
                                swal("Permission Denied", "This city is currently occupied in the System. This will cause an error to other dependencies if it is deleted. Action aborted for security reasons!", "error");
                            }

                        }else {
                            swal("Cancelled", "Action aborted! city is safe :)", "success");
                        }
                    });
            }
        });

    })
});

function add_city_field() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="row form-group"><div class="col-md-9"><input type="text" name="city[]" class="form-control" data-id=""/></div><div class="col-md-3"><a href="#" class="remove_field btn btn-danger">Remove</a></div></div>'); //add input box
        }
    });
}

$(document).on('click', '.close-save-city-form', function(){
    $('#city-manage-model').modal('hide');
});

/**
 * Update city list function
 */
$(document).on('click', '#update-city-btn', function(){

    var values = [];
    var status = "";

    var state_id = $('#state-id').val();
    //get all option list
    $("input[name='city[]']").each(function() {
        var data = $(this).data('id');
        var dataset = {
            'id' : data,
            'value' : $(this).val()
        };
        values.push(dataset);
    });

    status = checkCityListEmpty();

    if(status){

        $.ajax({
            url: "/configuration/city/update",
            type: 'POST',
            dataType: 'json',
            data : { city_list:values, state_id:state_id },
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#city-manage-model').modal('hide');

                    $('#city-table').dataTable().fnDestroy();
                    ajax_Datatable_Loader();
                    get_all_country();
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            }
        });
    }
});

//quesiton category edit mode option list validation
function checkCityListEmpty() {
    var isValid = true;
    $("#tab_city_list").find('input').each (function(index, value) {
        var value = $(this).val();

        if(value == ""){
            $(this).css( "border", "solid 1px #ff0000" );

            if($("#err-"+index).length == 0) {
                $(this).parent().append('<span id="err-'+ index +'" style="color: #ff0000;">This field cannot be empty</span>');
            }

            isValid = false;
        }
        else {
            $(this).css( "border", "solid 1px #e4e5e7" );
        }

    });
    return isValid;
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