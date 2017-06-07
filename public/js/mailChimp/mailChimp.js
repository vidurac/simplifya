
$(function () {
    mailChimpTable();

    $("#mailChimpSearch").click(function(){
        mailChimpTable();
    });

    $("#syncMailChimip").click(function(){
        findMailChimpList();
    });

    $("#create_new_version_model").click(function(){
        var emails = Array();

        $('.chkMailChimpUser').each(function () {
            if(this.checked){
                emails.push($(this).val());
            }
        });

        var list = $("#mailChimpList").val();
        if(emails.length > 0 && list != ""){
            syncMailChimp(list, emails)
        }
        else if(emails.length <= 0){
            swal({
                title: "Error!",
                text: "You should select at least one email address to sync."
            });
        }
        else if(list == ""){
            swal({
                title: "Error!",
                text: "Please select MailChimp List"
            });
        }
    });


    $("#mailChimpEntityType").change(function(){
        var entityType =  $(this).val();
        if(entityType != ""){
            findCompany(entityType);
        }
        else {
            findCompany(0);
        }

    })
});

// find company based on the entity type
function findCompany(entityType){
    $.ajax({
        url: "/mailchimp/companyList",
        type: 'GET',
        data: {entityType: entityType},
        dataType: 'json',
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(result){
            $("#mailChimpCompanyList").empty();
            $("#mailChimpCompanyList").append($("<option></option>")
                .attr("value", "")
                .text("Company List"));

            $.each(result.data, function(key, value){
                $("#mailChimpCompanyList").append($("<option></option>")
                    .attr("value",value.id)
                    .text(value.name));
            });
            $(".splash").hide();
        },
        error: function(xhr){
            $(".splash").hide();
        }
    });
}


function mailChimpTable()
{
    var entityType = $("#mailChimpEntityType").val();
    var companyList = $("#mailChimpCompanyList").val();

    var table = $('#mailChimpTable').DataTable();
    table.destroy();
    $('#mailChimpTable').dataTable({
        "ajax": {
            "url": "/mailchimp/all",
            "type": "GET",
            data : {entityType:entityType,companyList:companyList},
        },
        "searching": false,
        "paging": true,
        "info": true,
        "autoWidth": false,
        "bSort": false
    });
}


function findMailChimpList(){
    $.ajax({
        url: "/mailchimp/list",
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(result){
            $("#mailChimpList").empty();
            $("#mailChimpList").append($("<option></option>")
                .attr("value", "")
                .text("Select List"));

            $.each(result.data.data, function(key, value){
                $("#mailChimpList").append($("<option></option>")
                    .attr("value",value.id)
                    .text(value.name));
            });

            $('#mailChimpCynicModel').modal('show');
            $(".splash").hide();

        },
        error: function(xhr){
            $(".splash").hide();
        }
    });
}

function syncMailChimp(list, emails){
    $.ajax({
        url: "/mailchimp/sync",
        type: 'POST',
        dataType: 'json',
        data: {list: list, emails: emails},
        beforeSend: function() {
            $(".splash").show();
        },
        success: function(result){
            $('#mailChimpCynicModel').modal('hide');
            $(".splash").hide();
            toastr.success('Success - Sync successfully completed.');
        },
        error: function(xhr){
            $(".splash").hide();
            toastr.error('Error - Sync faild.');
        }
    });
}