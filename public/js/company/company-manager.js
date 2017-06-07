/**
 * Created by Harsha on 5/31/2016.
 */

var companyTable = {};
var business_location_dataTable = {};
var employee_dataTable = {};

var companyid;
$(function(){
    companyTableManager();
    companyPendingTable();
    companySummaryTable();
    commissionTable();
});

function commissionTable()
{
    companyTable =  $('#company-commission-table').dataTable( {
        "ajax": {
            "url": "get/commissions",
            "type": "GET",
        },
        "language": {
            "emptyTable": "No pending commissions to show"
        },
        "destroy":true,
        "searching": false,
        "paging": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "aoColumns": [
            {"sWidth": "70%", "mData": 0},
            {"sWidth": "30%", "mData": 1},
            // {"sWidth": "20%", "mData": 2}
        ]
    } );
}

function companyTableManager()
{
    companyTable =  $('#company-manager-table').dataTable( {
        "ajax": {
            "url": "/get/all/company",
            "type": "GET",
        },
        "searching": false,
        "paging": true,
        "info": true,
        "autoWidth": false,
        "bSort": false,
        "aoColumns": [
            // {"sWidth": "20%", "mData": 0},
            {"sWidth": "20%", "mData": 1},
            {"sWidth": "20%", "mData": 2},
            {"sWidth": "20%", "mData": 3},
            {"sWidth": "10%", "mData": 4},
            {"sWidth": "20%", "mData": 5}
        ]
    } );
}

function companyPendingTable()
{
    companyTable =  $('#company-pending-table').dataTable( {
        "ajax": {
            "url": "/get/all/pending",
            "type": "GET",
        },
        "language": {
            "emptyTable": "No approval pending companies"
        },
        "destroy":true,
        "searching": false,
        "paging": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "aoColumns": [
            {"sWidth": "20%", "mData": 0},
            {"sWidth": "30%", "mData": 1},
            {"sWidth": "30%", "mData": 2},
            {"sWidth": "10%", "mData": 3},
            {"sWidth": "10%", "mData": 4},
            // {"sWidth": "10%", "mData": 5}
        ]
    } );
}
function companySummaryTable()
{
    companyTable =  $('#company-summary-table').dataTable( {
        "ajax": {
            "url": "get/company/summary",
            "type": "GET",
        },
        "language": {
            "emptyTable": "No summary to show"
        },
        "destroy":true,
        "searching": false,
        "paging": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "aoColumns": [
            {"sWidth": "70%", "mData": 0},
            {"sWidth": "30%", "mData": 1}
        ]
    } );
}
$(document).on('click', '#company_search',function(){
    var business_name = $('#business_name').val();
    var entity_type = $('#entity_type').val();
    var status      = $('#status').val();

    if(business_name == '' && entity_type == '' && status == '') {
        $('#company-manager-table').dataTable().fnDestroy();
        companyTableManager();
    } else {
        $('#company-manager-table').dataTable().fnDestroy();
        companyTable =  $('#company-manager-table').dataTable( {
            "ajax": {
                url: "/company/filtering",
                type: "POST",
                data : {business_name:business_name,entity_type:entity_type,status:status}
            },
            "searching": false,
            "paging": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "aoColumns": [
                // {"sWidth": "10%", "mData": 0},
                {"sWidth": "20%", "mData": 1},
                {"sWidth": "20%", "mData": 2},
                {"sWidth": "20%", "mData": 3},
                {"sWidth": "10%", "mData": 4},
                {"sWidth": "20%", "mData": 5}
            ]
        } );
    }
});

/**
 * get company details and load company detail on company wizard
 * @param company_id
 */
function viewCompanyDetails(company_id, type)
{
    companyid = company_id;
    $.ajax({
        url: "/get/company/details/"+company_id,
        type: 'GET',
        dataType: 'json',
        success: function(result){
            if(result.success == 'true') {

                var active_tab = $('#wizardControl a.btn-primary').attr('id');
                var active_step = $('.tab-content div.active').attr('id');
                $('#'+active_step).removeClass('active');
                $('#step1').addClass('active');
                $('#'+active_tab).removeClass('btn-primary');
                $('#'+active_tab).addClass('btn-default');
                $('#basic_info_tab').removeClass('btn-default');
                $('#basic_info_tab').addClass('btn-primary');

                if(result.data.company_detail.entity_type_id == 3 || result.data.company_detail.entity_type_id == 4) {
                    $('#license_tab').addClass('license-show');
                } else {
                    $('#license_tab').removeClass('license-show');
                }
                if(result.data.company_detail.company_status == '1' && result.data.entity_type != '2') {
                    $('#btn-display').css('display','block');
                } else {
                    $('#btn-display').css('display','none');
                }

                if(result.data.entity_type == '2') {
                    $('.foc-update-section').css('display','block');
                    if (result.data.company_detail.foc == '1') {
                        $("#foc_update_enable").prop("checked", true);
                        $("#foc_update_enable").attr('checked', 'checked');
                    }else {
                        $("#foc_update_disable").prop("checked", true);
                        $("#foc_update_disable").attr('checked', 'checked');
                    }

                }else {
                    $('.foc-update-section').css('display','none');
                }

                $('#company_registration_no').val(result.data.company_detail.company_reg);
                $('#company_entity_type').val(result.data.company_detail.company_type);
                $('#name_of_business').val(result.data.company_detail.company_name);

                if(type == 0) {
                    if(result.data.company_detail.company_status==1){
                        $("#suspend-company").remove();
                        $("#approve-company").remove();
                        $("#reject-company").remove();
                        $("#un-suspend-company").remove();
                        $("#approve-pending-company").remove();
                        $('.btn-footer').append('<button type="button" class="btn btn-danger pull-right" id="reject-company" style="display: block; margin: 3px;">Reject</button>');
                        $('.btn-footer').append('<button type="button" class="btn btn-success pull-right" data-dismiss="modal" id="approve-company" style="display: block; margin: 3px;">Approve</button>');
                        $('#approve-pending-company').css("display","block")
                    }else if(result.data.company_detail.company_status==2){
                        $("#approve-company").remove();
                        $("#suspend-company").remove();
                        $("#reject-company").remove();
                        $("#un-suspend-company").remove();
                        $("#approve-pending-company").remove();
                        $('.btn-footer').append('<button type="button" class="btn btn-primary pull-right" data-dismiss="modal" id="suspend-company" style="display: block; margin: 3px;">Suspend</button>');
                    } else if(result.data.company_detail.company_status==6){
                        $("#approve-company").remove();
                        $("#suspend-company").remove();
                        $("#reject-company").remove();
                        $("#un-suspend-company").remove();
                        $("#approve-pending-company").remove();
                        $('.btn-footer').append('<button type="button" class="btn btn-primary pull-right" data-dismiss="modal" id="un-suspend-company" style="display: block; margin: 3px;">Un-Suspend</button>');
                    } else {
                        //$('#suspend-company').css("display","none")
                        //$('#approve-company').css("display","none")
                        //$('#approve-pending-company').css("display","none")
                        //$('#reject-company').css("display","none")

                        $("#suspend-company").remove();
                        $("#approve-company").remove();
                        $("#reject-company").remove();
                        $("#un-suspend-company").remove();
                        $("#approve-pending-company").remove();
                    }
                } else {
                    $("#suspend-company").remove();
                    $("#approve-company").remove();
                    $("#reject-company").remove();
                    $("#un-suspend-company").remove();
                    $("#approve-pending-company").remove();
                }

                $('#company-details-modal').modal('show');
            }
        }
    });
}

// $('.foc-update').click(function(){
$(document).on('click', '.foc-update',function(){
    var previousSelected =  $("input[name=foc_active]:checked");
    var cnfrm = confirm('Sure you want to change the FOC status of the company?');
    if(cnfrm != true)
    {
        if(previousSelected.attr("id") == 'foc_update_disable') {
            $( "#foc_update_enable" ).prop( "checked", true );
        } else {    
            $( "#foc_update_disable" ).prop( "checked", true );
        }
        //previousSelected.checked = false;
        $(this).preventDefault();
        return false;
    }else {
        var radioSelectedId = $('input[name=foc_active]:checked').val();
        var companyId = companyid;
        var focValue = radioSelectedId;
        $.ajax({
            url: "/change/company/foc",
            type: 'POST',
            data:{company_id:companyId, foc:focValue},
            dataType: 'json',
            beforeSend: function() {
                $(".splash").show();
            },
            success: function(result) {
                $(".splash").hide();
                if (result.success == 'true') {
                    msgAlert(result.message, 'success');
                }
            },
            error: function (result) {
                $(".splash").hide();
                msgAlert(result.message, 'error');
            }
        });
    }

    // swal({
    //         title: "Are you sure?",
    //         text: "Sure you want to change foc property?",
    //         type: "warning",
    //         showCancelButton: true,
    //         confirmButtonColor: "#DD6B55",
    //         confirmButtonText: "Delete",
    //         cancelButtonText: "Cancel",
    //         closeOnConfirm: true,
    //         closeOnCancel: true
    //     },
    //     function (isConfirm) {
    //         if (isConfirm != true) {
    //             return false;
    //         }
    //     });
});

$(document).on('click', '#basic_info_tab', function(){
    var active_tab = $('#wizardControl a.btn-primary').attr('id');
    $('#'+active_tab).removeClass('btn-primary');
    $('#'+active_tab).addClass('btn-default');
    $('#basic_info_tab').removeClass('btn-default');
    $('#basic_info_tab').addClass('btn-primary');
});

$(document).on('click', '#business_location_tab', function(){
    var active_tab = $('#wizardControl a.btn-primary').attr('id');
    $('#'+active_tab).removeClass('btn-primary');
    $('#'+active_tab).addClass('btn-default');
    $('#business_location_tab').removeClass('btn-default');
    $('#business_location_tab').addClass('btn-primary');

    $("#business_location_tbl").dataTable().fnDestroy();
    business_location_dataTable = $('#business_location_tbl').dataTable( {
        "ajax": {
            "url": "/company/locations/"+companyid,
        },
        "searching": false,
        "paging": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "aoColumns": [
            {"sWidth": "20%", "mData": 0},
            {"sWidth": "20%", "mData": 1},
            {"sWidth": "20%", "mData": 2},
            {"sWidth": "20%", "mData": 3},
            {"sWidth": "15%", "mData": 4},
            // {"sWidth": "10%", "mData": 4}
        ]
    });
});

$(document).on('click', '#license_tab', function() {

    var active_tab = $('#wizardControl a.btn-primary').attr('id');
    $('#'+active_tab).removeClass('btn-primary');
    $('#'+active_tab).addClass('btn-default');
    $('#license_tab').removeClass('btn-default');
    $('#license_tab').addClass('btn-primary');

    $("#licenses_table").dataTable().fnDestroy();
    $('#licenses_table').dataTable({
        "ajax": {
            "url": "/company/license/"+companyid,
        },
        "searching": false,
        "paging": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "aoColumns": [
            {"sWidth": "20%", "mData": 0},
            {"sWidth": "20%", "mData": 1},
            {"sWidth": "20%", "mData": 2}
        ]
    });
});

$(document).on('click', '#employee_tab', function() {

    var active_tab = $('#wizardControl a.btn-primary').attr('id');
    $('#'+active_tab).removeClass('btn-primary');
    $('#'+active_tab).addClass('btn-default');
    $('#employee_tab').removeClass('btn-default');
    $('#employee_tab').addClass('btn-primary');

    $("#employe-table").dataTable().fnDestroy();
    employee_dataTable = $('#employe-table').dataTable({
        "ajax": {
            "url": "/get/employees/"+companyid,
        },
        "searching": false,
        "paging": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        "aoColumns": [
            {"mData": 0},
            {"mData": 1},
            {"mData": 2},
            {"mData": 3},
            // {"sWidth": "10%", "mData": 4}
        ]
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

$('#approve-pending-company').click(function(){
    var status = '2';
    approveCompanyByAdmin(status)
});

function changeCompanyStatusByAdmin(status)
{
    var url_path_name      = window.location.pathname;     // Returns full URL
    var split_url = url_path_name.split("/");

    $.ajax({
        url: "/change/company/status",
        type: 'POST',
        dataType: 'json',
        data:{company_id:companyid, status:status},
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(result){
            if(result.success == 'true') {
                if((status == '2') || (status == '3')) {
                    $("#reject-company").css("display", "none");
                    $("#approve-company").css("display", "none");
                    if(split_url[1] == 'dashboard') {
                        location.reload();
                    } else {
                        $('#company-manager-table').dataTable().fnDestroy();
                        $(".splash").hide();
                        companyTableManager();
                        var msg = result.message;
                        var msg_type = 'success';
                        msgAlert(msg, msg_type);
                        $('#company-details-modal').modal('hide');
                    }
                } else {
                    if(split_url[1] == 'dashboard') {
                        location.reload();
                    } else {
                        $('#company-manager-table').dataTable().fnDestroy();
                        $(".splash").hide();
                        companyTableManager();
                        var msg = result.message;
                        var msg_type = 'success';
                        msgAlert(msg, msg_type);
                        $('#company-details-modal').modal('hide');
                    }
                }
            } else {
                if(split_url[1] == 'dashboard') {
                    location.reload();
                } else {
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);
                    $('#company-details-modal').modal('hide');
                }
            }
        },
        error: function(result) {
            $(".splash").hide();
        }
    });
}

function approveCompanyByAdmin(status)
{
    $.ajax({
        url: "/change/company/status",
        type: 'POST',
        dataType: 'json',
        data:{company_id:companyid, status:status},
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(result){
            if(result.success == 'true') {
                $('#company-manager-table').dataTable().fnDestroy();
                $(".splash").hide();
                companyTableManager();
                var msg = result.message;
                var msg_type = 'success';
                msgAlert(msg, msg_type);
                companyPendingTable();
                companySummaryTable();
                $('#company-details-modal').modal('hide');
            } else {
                var msg = result.message;
                var msg_type = 'success';
                msgAlert(msg, msg_type);
                $('#company-details-modal').modal('hide');
            }
        },
        error: function(result) {
            $(".splash").hide();
        }
    });
}

$(document).on('click', '#suspend-company', function(){
    var status = '6';
    changeCompanyStatusByAdmin(status)
});
$(document).on('click', '#un-suspend-company', function(){
    var status = '0';
    changeCompanyStatusByAdmin(status)
});
$(document).on('click', '#reject-company', function(){
    var status = '3';
    changeCompanyStatusByAdmin(status)
});
$(document).on('click', '#approve-company', function(){
    var status = '2';
    changeCompanyStatusByAdmin(status)
});


$(document).on('click', '#welcom_module', function(){
    companyModuleSetup();
});

function companyModuleSetup()
{
    $.ajax({
        url: "dashboard/moduleSetup",
        type: 'POST',
        dataType: 'json',
        data:{module_id:"WELCOME"},
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(result){
            if(result.success == 'true') {
                location.reload();
                $(".splash").hide();
            }
        }
    });
}
