/**
 * Created by User on 8/16/2016.
 */
var companyTable = {};
var business_location_dataTable = {};
var employee_dataTable = {};

var companyid;
$(function(){
    companyTableManager();
    companyPendingTable();
    companySummaryTable();
});

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
            {"sWidth": "10%", "mData": 4}
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
                {"sWidth": "10%", "mData": 4}
            ]
        } );
    }
});

