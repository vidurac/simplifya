/**
 * Created by Nishan on 5/6/2016.
 */

var appointment_table = {};
var dataTable = {};

$(function () {

    jQuery('#fromDateDatePicker').datepicker();
    jQuery('#toDateDatePicker').datepicker();
});

$(document).ready(function() {
    var dataTable = {}
    ajax_Datatable_Loader();
    $('#startDatePicker').datepicker({
            format: 'mm/dd/yyyy'
        }).on('changeDate', function(e) {
            //
        });

    $('#endDatePicker').datepicker({
            format: 'mm/dd/yyyy'
        }).on('changeDate', function(e) {
            //
        });


});

$(document).on('click', '#submitButton', function(){

    $.validator.addMethod("greaterThan", function(value, element, params) {
        var et = new Date(value);
        var st = new Date($('#startDate').val());

        if(value!="") {
            return (et > st);
        }else
        {
            return true;
        }
    }, 'invalid end date');

    var inspection_form = $("#eventForm");
    inspection_form.validate({
        errorPlacement: function(error, element) {
            if(element.attr('name')=="endDate"){
                error.insertAfter($('#date_err'));
            }else{
                error.insertAfter(element);
            }
        },
        errorElement: 'span',
        errorClass: 'help-block',
        highlight: function(element, errorClass, validClass) {
            $(element).closest('.form-group').addClass("has-error");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).closest('.form-group').removeClass("has-error");
        },
        // Specify the validation rules
        rules: {
            endDate: {
                greaterThan : "#endDate"
            }
        },
        // Specify the validation error messages
        messages: {

        }
    });

    if (inspection_form.valid() == true) {

        var cc = $('#compliance_company').val();
        var mj = $('#business').val();
        var b_status = $('#status').val();

        var from_date = $('#startDate').val();
        var to_date = $('#endDate').val();

        var date = [from_date, to_date];

        dataTable.destroy();
        ajax_Datatable_Loader();

        dataTable.columns(1).search(date, true).draw();
        dataTable.columns(2).search(cc, true).draw();
        dataTable.columns(3).search(mj, true).draw();
        dataTable.columns(4).search(b_status, true).draw();
    }

});

function ajax_Datatable_Loader(){
    dataTable =  $('#inspection-request-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : true,
        "sAjaxSource":"/request/filter",
        "aoColumns": [
            {"mData": 0},
            {"mData": 1},
            {"mData": 2},
            {"mData": 3, "bSortable" : false},
            {"mData": 4, "bSortable" : false}
        ]

    } );
    $("#inspection-request-table_filter").css("display","none");
}



$(document).on('click', '#mjb-request', function(){

    $( "#audit_active" ).removeClass("active");
    $( "#req_active" ).addClass( "active" );

    $( "#request-table" ).addClass("req_tab_display");
    $( "#request-table" ).removeClass( "req_tab_display_none" );
    $( "#3rd-party-audit-tab" ).addClass("_3rd_tab_display_none");
    $( "#3rd-party-audit-tab" ).removeClass( "_3rd_tab_display");

    appointment_table.destroy();
    ajax_Datatable_Loader();


})

$(document).on('click', '#3rd-party-audit', function(){

    $( "#req_active" ).removeClass("active");
    $( "#audit_active" ).addClass( "active" );

    $( "#request-table" ).removeClass("req_tab_display");
    $( "#request-table" ).addClass( "req_tab_display_none" );
    $( "#3rd-party-audit-tab" ).removeClass("_3rd_tab_display_none");
    $( "#3rd-party-audit-tab" ).addClass( "_3rd_tab_display");

    dataTable.destroy();
    appointmentTable();
})

// appointment table
function appointmentTable()
{
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var mjBusiness = $("#mjBusiness").val();
    var companyName = $("#companyName").val();
    var status = $("#status").val();
    var entityType = $("#entityType").val();

    appointment_table = $('#appointment-detail-table').DataTable();
    appointment_table.destroy();
    appointment_table = $('#appointment-detail-table').dataTable({
        "ajax": {
            "url": "/appointment/all",
            "type": "GET",
            data : {fromDate:fromDate,toDate:toDate,mjBusiness:mjBusiness,companyName:companyName,status:status, entityType:entityType, thPartyAudit:'true'},
        },
        "searching": false,
        "paging": true,
        "info": true,
        "autoWidth": false,
        "bSort": false,
        "aoColumns": [
            {"sWidth": "20%", "mData": 0},
            {"sWidth": "20%", "mData": 1},
            {"sWidth": "20%", "mData": 2},
            {"sWidth": "10%", "mData": 3},
            {"sWidth": "24%", "mData": 4},
            {"sWidth": "6%", "mData": 5},
            {"sWidth": "6%", "mData": 6},
            // {"sWidth": "2%", "mData": 5}
        ]
    });
}

$("#searchAppointments").click(function(){
    appointment_table.destroy();
    appointmentTable();
});