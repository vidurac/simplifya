/**
 * Created by Nishan on 5/6/2016.
 */
$(function () {

    jQuery('#fromDateDatePicker').datepicker();
    jQuery('#toDateDatePicker').datepicker();


    appointmentTable();
    var table = $('#appointment-detail-table').DataTable();

    $("#searchAppointments").click(function(){
        appointmentTable();
    });


});


// appointment table
function appointmentTable()
{
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();
    var mjBusiness = $("#mjBusiness").val();
    var companyName = $("#companyName").val();
    var status = $("#status").val();
    var entityType = $("#entityType").val();


    var table = $('#appointment-detail-table').DataTable();
    table.destroy();
    $('#appointment-detail-table').dataTable({
        "ajax": {
            "url": "/appointment/all",
            "type": "GET",
            data : {fromDate:fromDate,toDate:toDate,mjBusiness:mjBusiness,companyName:companyName,status:status, entityType:entityType, thPartyAudit:'false'},
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











