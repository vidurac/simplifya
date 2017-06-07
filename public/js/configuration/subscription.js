/**
 * Created by Nishan on 6/6/2016.
 */

$(document).ready(function() {
    var subscription_type = $('#type_of_sub').val();
    var dataTable = {}
    ajax_Datatable_Loader(subscription_type);

    // jquery validation plugin for new subscription
    $("#newSubscriptionForm").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="subscription_name"){
                error.insertAfter(document.getElementById('err-subscription_name'));
            } else if(element.attr('name')=="validity_period"){
                error.insertAfter(document.getElementById('err-validity_period'));
            } else if(element.attr('name')=="company_type"){
                error.insertAfter(document.getElementById('err-company_type'));
            } else if(element.attr('name')=="price"){
                error.insertAfter(document.getElementById('err-price'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "subscription_name" : {
                required: true
            },
            "validity_period" : {
                required: true
            },
            "company_type" : {
                required: true
            },
            "price" : {
                required: true,
                number:true
            }
        },
        messages: {
            "subscription_name" : {
                required: "Please insert a subscription name"
            },
            "validity_period" : {
                required: "Please specify a validity period"
            },
            "company_type" : {
                required: "Please specify a company type"
            },
            "price" : {
                required: "Please insert a price"
            }
        }
    });

    // jquery validation plugin for edit subscription
    $("#subscription-edit-form").validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="subscription_name"){
                error.insertAfter(document.getElementById('err-subscription_name'));
            } else if(element.attr('name')=="validity_period"){
                error.insertAfter(document.getElementById('err-validity_period'));
            } else if(element.attr('name')=="company_type"){
                error.insertAfter(document.getElementById('err-company_type'));
            } else if(element.attr('name')=="price"){
                error.insertAfter(document.getElementById('err-price'));
            } else{
                error.insertAfter(element);
            }
        },
        rules: {
            "subscription_name" : {
                required: true
            },
            "validity_period" : {
                required: true
            },
            "company_type" : {
                required: true
            },
            "price" : {
                required: true,
                number:true
            }
        },
        messages: {
            "subscription_name" : {
                required: "Please insert a subscription name"
            },
            "validity_period" : {
                required: "Please specify a validity period"
            },
            "company_type" : {
                required: "Please specify a company type"
            },
            "price" : {
                required: "Please insert a price"
            }
        }
    });
});

//Bootstrap datable loader
function ajax_Datatable_Loader(subscription_type){
    dataTable =  $('#subscription-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : false,
        "sAjaxSource":"/subscription/filter/"+subscription_type

    } );
    $("#subscription-table_filter").css("display","none");
}

//submit new subscription form
$(document).on('click', '#submit_subscription', function () {
    //if submitted form validation successful
    if($("#newSubscriptionForm").valid()){
        var dataset = {};
        //Serialize submitted form data
        var data = $('#newSubscriptionForm').serializeArray();

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

        var sub_type = dataset.sub_type;

        var subscription_name = dataset.subscription_name;
        var validity_period = dataset.validity_period;
        var company_type = dataset.company_type;
        var price = dataset.price;
        var description = dataset.subscription_description;

        $.ajax({
            url: "/configuration/subscription/"+sub_type+"/store",
            type: 'POST',
            dataType: 'json',
            data : { subscription_name:subscription_name, validity_period:validity_period, company_type:company_type, price:price, description: description },
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#subscription_name').val('');
                    $('#price').val('');
                    $('#validity_period').val('');
                    $('#company_type').val('');
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            }
        });
    }
});


//update new subscription form
$(document).on('click', '#update-subscription-btn', function () {
    var subscription_type = $('#type_of_sub').val();
    //if submitted form validation successful
    if($("#subscription-edit-form").valid()){
        var dataset = {};

        var subscription_id = $('#subscription_id').val();
        //Serialize submitted form data
        var data = $('#subscription-edit-form').serializeArray();

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
            url: "/configuration/subscription/edit/store/item",
            type: 'POST',
            dataType: 'json',
            data : { dataset:dataset },
            success: function(result){
                if(result.success == 'true') {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#subscription-manage-model').modal('hide');
                    $('#subscription-table').dataTable().fnDestroy();
                    ajax_Datatable_Loader(subscription_type);
                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            }
        });
    }
});

/**
 * Individual subscription edit view popup
 * @param subscription_id
 */
function editSubscription(subscription_id) {

    $('#subscription_id').val(subscription_id);

    $('#subscription-manage-model').modal('show');
    //Edit detail retrieve ajax call
    $.ajax({
        url: "/configuration/subscription/edit/item",
        type: 'POST',
        dataType: 'json',
        data : { dataset:subscription_id },
        success: function(result){
            if(result.success == 'true')
            {
                $.each(result.data, function(index, value){
                    //initialise all values to variables
                    var sub_name     = value.name;
                    var valid_period = value.validity_period_id;
                    var company_type = value.entity_type_id;
                    var price        = value.amount;
                    var desciption   = value.description;
                    //assign all values to elements
                    $('#subscription_name').val(sub_name);
                    $('#validity_period_temp').val(valid_period);
                    if (valid_period != undefined) {
                        switch (valid_period){
                            case 1: {
                                $('#validity_period_temp').val('Monthly');
                                break;
                            }
                            case 3: {
                                $('#validity_period_temp').val('3 Months');
                                break;
                            }
                            case 6: {
                                $('#validity_period_temp').val('6 Months');
                                break;
                            }
                            case 12: {
                                $('#validity_period_temp').val('12 Months');
                                break;
                            }

                            default: {

                                $('#validity_period_temp').val(valid_period + ' Months');
                                break;
                            }
                        }
                    }
                    $('#validity_period').val(valid_period);
                    $('#company_type').val(company_type);
                    $('#price').val(price);
                    $('#subscription_description').val(desciption);
                });
                //show edit popup
                $('#subscription-manage-model').modal('show');
            } else {

            }

        }
    });
}

//Close edit model
$(document).on('click', '.close-update-subscription-form', function () {
    $('#subscription_name').val('');
    $('#validity_period').val('');
    $('#company_type').val('');
    $('#price').val('');
    $('#subscription-manage-model').modal('hide');
});

/**
 * change subscription status
 */
$(document).on('click', '#change-subscription-state', function(){
    
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