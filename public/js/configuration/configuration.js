/**
 * Created by Nishan on 6/6/2016.
 */

$(document).on('change', '#country', function () {
    var countryId = $(this).val();

    jQuery.ajax({
        type: 'POST',
        url: "/configuration/getCities",
        async: false,
        data: { countryId: countryId},
        dataType: "json",
        beforeSend: function () {
            
        },
        success: function (result) {
            $("#city").empty();
            $("#city").append('<option value="0">---</option>');

            if(result.data != null){
                // $.each(result.data.master_states, function(incex, value){
                //     $("#state").append('<option value="'+value.id+'"> '+value.name+'</option>');
                // });
            }
        },
        error: function (result) {

        }
    });
});