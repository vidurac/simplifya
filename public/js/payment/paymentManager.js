
$(function () {
    paymentTable();

    $("#searchPayments").click(function(){
        paymentTable();
    });

    $("#paymentFromDateDatePicker").datepicker();
    $("#paymentToDateDatePicker").datepicker();



});

function paymentTable()
{
    var fromDate = $("#paymentFromDate").val();
    var toDate = $("#paymentToDate").val();
    var txId = '';
    var responseId = $("#responseId").val();
    var companyType = $("#companyType").val();
    var companyName = $("#companyName").val();
    var txStatus = $("#txStatus").val();
    var txType = $("#txType").val();


    var table = $('#payment-detail-table').DataTable();
    table.destroy();
    $('#payment-detail-table').dataTable({
        "ajax": {
            "url": "/payment/all",
            "type": "GET",
            data : {fromDate:fromDate,toDate:toDate,txId:txId,responseId:responseId,companyType:companyType, companyName:companyName,txStatus:txStatus,txType:txType},
        },
        "searching": false,
        "paging": true,
        "info": true,
        "autoWidth": false,
        "bSort": false
    });
}