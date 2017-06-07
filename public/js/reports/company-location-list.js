/**
 * Created by User on 8/16/2016.
 */

var dataTable = {}
$(document).ready(function() {
    company_location_list();

});

function company_location_list() {
    dataTable =  $('#report-company-location-table').DataTable( {
        "processing": true,
        "serverSide": false,
        "paginate" : true,
        "bSort" : true,
        "sAjaxSource":"/company/locations",
        "aoColumns": [
            {"mData": 0},
            {"mData": 1},
            {"mData": 2},
            {"mData": 3},
            {"mData": 4},
            {"mData": 5, "bSortable" : false},
        ]
    } );

    $("#report-company-location-table_filter").css("display", "none");
}

$(document).on('click','#company_search', function(){
    var name = $('#zip_name_phone_no').val();
    var entity_type = $('#entity_type').val();
    var country = $('#country').val();
    var state = $('#state').val();
    var city = $('#city').val();

    dataTable =  $('#report-company-location-table').DataTable();
    dataTable.destroy();
    $('#report-company-location-table').dataTable({
        "ajax": {
            "url": "/company/locations",
            "type": "GET",
            data : {entityType:entity_type,country:country,state:state,city:city,name:name},
        },
        "searching": false,
        "paging": true,
        "info": true,
        "autoWidth": false,
        "bSort": false
    });
});

$(document).on('click','#country', function(){
    var country_id = $(this).val();
    if(country_id !='') {
        $.ajax({
            url: "/get/state/city/"+country_id,
            type: 'GET',
            dataType: 'json',
            success: function(result){
                var select_state = $('#state').val();
                var select_city = $('#city').val();
                if(select_state == '') {
                    $("#state").empty().append('<option value="">State</option>');
                    $.each(result.state, function(index, value){
                        $("#state").append($('<option>', {value: value.id, text: value.name}));
                    });
                }
                if(select_city == '') {
                    $("#city").empty().append('<option value="">City</option>');
                    $.each(result.city, function(index, value){
                        $("#city").append($('<option>', {value: value.id, text: value.name}));
                    });
                }

            }
        });
    }
});

$(document).on('click','#state', function(){
    var state_id = $(this).val();
    if(state_id !='') {
        $.ajax({
            url: "/get/country/city/"+state_id,
            type: 'GET',
            dataType: 'json',
            success: function(result){
                var select_state = $('#country').val();
                var select_city = $('#city').val();
                if(select_state == '') {
                    $("#country").empty().append('<option value="">country</option>');
                    $.each(result.country, function(index, value){
                        $("#country").append($('<option>', {value: value.id, text: value.name}));
                    });
                }
                if(select_city == '') {
                    $("#city").empty().append('<option value="">City</option>');
                    $.each(result.city, function(index, value){
                        $("#city").append($('<option>', {value: value.id, text: value.name}));
                    });
                }

            }
        });
    }
});
$(document).on('click','#city', function(){
    var city_id = $(this).val();
    if(city_id !='') {
        $.ajax({
            url: "/get/country/state/"+city_id,
            type: 'GET',
            dataType: 'json',
            success: function(result){
                var select_country = $('#country').val();
                var select_state = $('#state').val();
                if(select_country == '') {
                    $("#country").empty().append('<option value="">country</option>');
                    $.each(result.country, function(index, value){
                        $("#country").append($('<option>', {value: value.country_id, text: value.country_name}));
                    });
                }
                if(select_state == '') {
                    $("#state").empty().append('<option value="">State</option>');
                    $.each(result.state, function(index, value){
                        $("#state").append($('<option>', {value: value.id, text: value.name}));
                    });
                }

            }
        });
    }
});

function downloadCompanyLocationReport(url){
    location.href = url+"?company_name="+$('#zip_name_phone_no').val()+"&entity_type="+$('#entity_type').val()+"&country="+$('#country').val()+"&state="+$('#state').val()+"&city="+$('#city').val()
}