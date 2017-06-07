/**
 * Created by User on 8/16/2016.
 */

$(function () {
    mailChimpTable();
    $("#mailChimpSearch").click(function(){
        mailChimpTable();
    });
});

function mailChimpTable()
{
    var entityType = $("#mailChimpEntityType").val();
    var companyList = $("#mailChimpCompanyList").val();
    var user_name = $("#user_name").val();

    var table = $('#mailChimpTable').DataTable();
    table.destroy();
    $('#mailChimpTable').dataTable({
        "ajax": {
            "url": "/company/users",
            "type": "GET",
            data : {entityType:entityType,companyList:companyList,user_name:user_name},
        },
        "searching": false,
        "paging": true,
        "info": true,
        "autoWidth": false,
        "bSort": false
    });
}

function downloadCompanyUsers(url){
    location.href = url+"?user_name="+$('#user_name').val()+"&entity_type="+$('#mailChimpEntityType').val()+"&company_list="+$('#mailChimpCompanyList').val()
}