/**
 * Created by Nishan on 6/6/2016.
 */

$(document).ready(function(){

    // jquery validation plugin
    $("#masterDataForm").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="company_name"){
                error.insertAfter(document.getElementById('err-company_name'));
            } else if(element.attr('name')=="email"){
                error.insertAfter(document.getElementById('err-email'));
            } else if(element.attr('name')=="phone_no"){
                error.insertAfter(document.getElementById('err-phone_no'));
            } else if(element.attr('name')=="address1"){
                error.insertAfter(document.getElementById('err-address1'));
            } else if(element.attr('name')=="country_id"){
                error.insertAfter(document.getElementById('err-country_id'));
            } else if(element.attr('name')=="state_id"){
                error.insertAfter(document.getElementById('err-state_id'));
            } else if(element.attr('name')=="city"){
                error.insertAfter(document.getElementById('err-city'));
            } else if(element.attr('name')=="header"){
                error.insertAfter(document.getElementById('err-header'));
            } else if(element.attr('name')=="footer"){
                error.insertAfter(document.getElementById('err-footer'));
            } else if(element.attr('name')=="sub_question_lvls"){
                error.insertAfter(document.getElementById('err-sub_question_lvls'));
            } else if(element.attr('name')=="pagination_lvl"){
                error.insertAfter(document.getElementById('err-pagination_lvl'));
            } else if(element.attr('name')=="mjb_sub"){
                error.insertAfter(document.getElementById('err-mjb_sub'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "company_name" : {
                required: false
            },
            "email" : {
                required: false,
                email: true
            },
            "phone_no" : {
                required: false,
                number: true
            },
            "address1" : {
                required: false
            },
            "country_id" : {
                required: false
            },
            "state_id" : {
                required: false
            },
            "city" : {
                required: false
            },
            "header" : {
                required: false
            },
            "footer" : {
                required: false
            },
            "sub_question_lvls" : {
                required: true,
                number: true
            },
            "pagination_lvl" : {
                required: false,
                number: true
            },
            "mjb_sub" : {
                required: false
            }
        },
        messages: {
            "company_name" : {
                required: "Please insert a company name"
            },
            "email" : {
                required: "Please insert valid email"
            },
            "phone_no" : {
                required: "Please valid phone number"
            },
            "address1" : {
                required: "Please insert address"
            },
            "country_id" : {
                required: "Please specify a country"
            },
            "state_id" : {
                required: "Please specify a state"
            },
            "city" : {
                required: "Please insert city"
            },
            "header" : {
                required: "Please insert header"
            },
            "footer" : {
                required: "Please insert footer"
            },
            "sub_question_lvls" : {
                required: "Please insert sub question level value"
            },
            "pagination_lvl" : {
                required: "Please insert pagination value"
            },
            "mjb_sub" : {
                required: "Please specify a marijuana subscription type"
            }
        }
    });

    // jquery validation plugin
    $("#masterDataEditForm").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="company_name"){
                error.insertAfter(document.getElementById('err-company_name'));
            } else if(element.attr('name')=="email"){
                error.insertAfter(document.getElementById('err-email'));
            } else if(element.attr('name')=="phone_no"){
                error.insertAfter(document.getElementById('err-phone_no'));
            } else if(element.attr('name')=="address1"){
                error.insertAfter(document.getElementById('err-address1'));
            } else if(element.attr('name')=="country_id"){
                error.insertAfter(document.getElementById('err-country_id'));
            } else if(element.attr('name')=="state_id"){
                error.insertAfter(document.getElementById('err-state_id'));
            } else if(element.attr('name')=="city"){
                error.insertAfter(document.getElementById('err-city'));
            } else if(element.attr('name')=="header"){
                error.insertAfter(document.getElementById('err-header'));
            } else if(element.attr('name')=="footer"){
                error.insertAfter(document.getElementById('err-footer'));
            } else if(element.attr('name')=="sub_question_lvls"){
                error.insertAfter(document.getElementById('err-sub_question_lvls'));
            } else if(element.attr('name')=="pagination_lvl"){
                error.insertAfter(document.getElementById('err-pagination_lvl'));
            } else if(element.attr('name')=="mjb_sub"){
                error.insertAfter(document.getElementById('err-mjb_sub'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "company_name" : {
                required: false
            },
            "email" : {
                required: false,
                email: true
            },
            "phone_no" : {
                required: false,
                number: true
            },
            "address1" : {
                required: false
            },
            "country_id" : {
                required: false
            },
            "state_id" : {
                required: false
            },
            "city" : {
                required: false
            },
            "header" : {
                required: false
            },
            "footer" : {
                required: false
            },
            "sub_question_lvls" : {
                required: true,
                number: true
            },
            "pagination_lvl" : {
                required: false,
                number: true
            },
            "mjb_sub" : {
                required: false
            }
        },
        messages: {
            "company_name" : {
                required: "Please insert a company name"
            },
            "email" : {
                required: "Please insert valid email"
            },
            "phone_no" : {
                required: "Please valid phone number"
            },
            "address1" : {
                required: "Please insert address"
            },
            "country_id" : {
                required: "Please specify a country"
            },
            "state_id" : {
                required: "Please specify a state"
            },
            "city" : {
                required: "Please insert city"
            },
            "header" : {
                required: "Please insert header"
            },
            "footer" : {
                required: "Please insert footer"
            },
            "sub_question_lvls" : {
                required: "Please insert sub question level value"
            },
            "pagination_lvl" : {
                required: "Please insert pagination value"
            },
            "mjb_sub" : {
                required: "Please specify a marijuana subscription type"
            }
        }
    });
});

//Send all submitted form data to the backend methods
$(document).on('click', '#save-master-data-btn', function() {
    //if form validation success
    if($("#masterDataForm").valid()){
        var dataset = {};
        //Serialize submitted form data
        var data = $('#masterDataForm').serializeArray();

        //push all values to one array
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
            url: "/configuration/masterdata/store",
            type: 'POST',
            dataType: 'json',
            data : { dataset:dataset },
            success: function(result){
                if(result.success == 'true') {
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

//Send all updated form data to the backend methods
$(document).on('click', '#update-master-data-btn', function() {
    //if form validation success
    if($("#masterDataEditForm").valid()){
        var dataset = {};
        //Serialize submitted form data
        var data = $('#masterDataEditForm').serializeArray();

        //push all values to one array
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
            url: "/configuration/masterdata/update",
            type: 'POST',
            dataType: 'json',
            data : { dataset:dataset },
            success: function(result){
                if(result.success == 'true') {
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
 * show notification message
 * @param msg
 * @param msg_type
 */
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