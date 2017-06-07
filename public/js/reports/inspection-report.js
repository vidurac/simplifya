/**
 * Created by User on 8/16/2016.
 */
var dataTable = {}
$(document).ready(function() {
    //start date picker initialize
    $('#startDatePicker').datepicker({
        format: 'mm/dd/yyyy'
    }).on('changeDate', function(e) {});

    //start date picker initialize
    $('#endDatePicker').datepicker({
        format: 'mm/dd/yyyy'
    }).on('changeDate', function(e) {});


    ajax_Datatable_Loader();
    });

function ajax_Datatable_Loader() {
    dataTable =  $('#report-detail-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : true,
        "sAjaxSource":"/inspection/report",
        "aoColumns": [
            {"mData": 0},
            {"mData": 1},
            {"mData": 2},
            {"mData": 3},
            {"mData": 4},
            {"mData": 5},
            {"mData": 6, "bSortable" : false},
            {"mData": 7, "bSortable" : false},
        ]
    } );

    $("#report-detail-table_filter").css("display", "none");
}

/**
 * Search filter to retrieve filtered data to the
 * reports bootstrap datatable
 */
$(document).on('click', '#inspection-search', function(){

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

    var report_filter = $("#reportForm");
    report_filter.validate({
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

    if (report_filter.valid() == true) {

        //Get all searchable filter values and assign them to variables
        var fromDate    = $("#startDate").val();
        var toDate      = $("#endDate").val();

        var status      = $("#status").val();
        var audit_type  = $("#audit_type").val();

        var date = [fromDate, toDate];

        dataTable.destroy();
        ajax_Datatable_Loader();

        dataTable.columns(0).search(date, true).draw();
        dataTable.columns(4).search(audit_type, true).draw();
        dataTable.columns(6).search(status, true).draw();
    }

});

function downloadInspectionReport(url)
{
    location.href = url+"?startDate="+$('#startDate').val()+"&endDate="+$('#endDate').val()+"&audit_type="+$('#audit_type').val()+"&status="+$('#status').val()
}
