/**
 * Created by Nishan on 6/6/2016.
 */

$(document).ready(function(){
    var pathname = window.location.pathname;
    var res = pathname.split("/");
    var dataTable = {}
    ajax_Datatable_Loader();
    editQuestionCategory(res[4]);

    /**
     * store all updated data
     */
    $('#update-qcategory-btn').on('click', function () {

        var is_required = $('input[name="is_required"]:checked').val();
        var is_multiselect = $('input[name="is_multiselect"]:checked').val();
        //var is_multiselect = $('#is_multiselect').val();
        var category_name  =   $('#category_name').val();
        var id = $('#id').val();
        var is_main_cat = $('#is_main_cat').val();
        var values = [];
        var visible_to = [];

        //get all visible entities
        $("input[name='visible_to[]']:checked").each( function () {
            console.log('1'+$(this).data('parentid'));
            var data = $(this).attr('id');
            var dataset = {
                'id' : $(this).attr('id'),
                'value' : this.value,
                'parent_id':($(this).data('parentid')==undefined)?0:$(this).data('parentid')
            };
            visible_to.push(dataset);
        });

        //get all option list
        $("input[name='option']").each(function() {
            console.log('2'+$(this).data('parentid'));
            var data = $(this).attr('id');
            var arr = data.split('option_');
            var dataset = {
                'id' : arr[1],
                'value' : $(this).val(),
                'parent_id':($(this).data('parentid')==undefined ||$(this).data('parentid')==0)?0:$(this).data('parentid')
            };
            values.push(dataset);
        });

        //option list validation
        var status = checkOptionListEmpty();

        if(status){

            $.ajax({
                url: "/configuration/qcategory/insert",
                type: 'POST',
                dataType: 'json',
                data : { is_required:is_required, is_multiselect:is_multiselect, visible_to:visible_to, category_name:category_name, classification_id:id, is_main_cat:is_main_cat, options:values },
                success: function(result){
                    if(result.success == 'true') {
                        var msg = result.message;
                        var msg_type = 'success';
                        editQuestionCategory(res[4]);

                        $('.is_req_yes').parent().removeClass('checked');
                        $('.is_req_no').parent().removeClass('checked');
                        $('.is_req_no').removeAttr('checked');
                        $('.is_req_yes').removeAttr('checked');

                        $('.is_multi_yes').parent().removeClass('checked');
                        $('.is_multi_no').parent().removeClass('checked');
                        $('.is_multi_no').removeAttr('checked');
                        $('.is_multi_yes').removeAttr('checked');

                        $('#mjb').prop('checked', false);
                        $('#cc').prop('checked', false);
                        $('#ge').prop('checked', false);
                        $('.addSubcnt').hasClass('hidden')?$('.addSubcnt').removeClass('hidden'):

                        $("#question-category-table").dataTable().fnDestroy();
                        ajax_Datatable_Loader();
                        msgAlert(msg, msg_type);
                    } else {
                        var msg = result.message;
                        var msg_type = 'error';
                        msgAlert(msg, msg_type);
                    }
                }
            });
        }


    });
    var scntDiv = $('#option_list');
    var i = $('#option_list p').size() + 1;

    $(document).on('click','#addScnt', function() {
        $('<p><label for="option_list" class="col-md-12">' +
            '<row class="col-md-12">' +
            '<span class="col-md-10"><input type="text" size="20" name="option" id="option_" class="form-control valid " value="" placeholder="Option Name" /></span>' +
            '<span class="col-md-2">' +
            '<a href="#" id="addSubcnt" class="btn btn-success hidden addSubcnt">+</a>' +
            '<a href="#" id="remScnt" class="btn btn-danger remScnt">Remove</a>' +
            '</span>' +
            '</row>' +
            '</label>' +

            '</p>').appendTo(scntDiv);
        i++;
        $('html, body').animate({scrollTop:$(document).height()}, 'slow');
        return false;

    });
    $(document).on('click','.addSubcnt', function() {
        $('<div><label for="option_list" class="col-md-12" style="margin-top: 1px;">' +
            '<row class="col-md-12">' +
            '<span class="col-md-6"><input type="text" size="20"  data-parentid="'+$(this).data("parent")+'"name="option" id="option_" class="form-control valid " value="" placeholder="Option Name" /></span>' +
            '<span class="col-md-2">' +
            '<a href="#" id="remScnt" class="btn btn-danger remScntChild">Remove</a>' +
            '</span>' +
            '</row>' +
            '</label>' +

            '</div>').appendTo($(this).closest('label'));
        i++;
        return false;
    });


    $(document).on('click', '.remScnt', function(e) {
        $(this).parents('label').remove();
        e.preventDefault();
        e.stopPropagation();


    });
    $(document).on('click', '.remScntChild', function(e) {
        $(this).closest('label').remove();
        e.preventDefault();
        e.stopPropagation();

    });


})

/**
 *
 * Edit Question categories
 * @param question_cat_id
 */
function editQuestionCategory(question_cat_id) {

    $.ajax({
        url: '/configuration/qcategory/edit/',
        type: 'GET',
        data: { categoryId : question_cat_id },
        beforeSend : function(){
            $('#option_list').html('');
            $('.is_multi_yes').parent().removeClass('checked');
            $('.is_multi_no').parent().removeClass('checked');
            $('.required_options_btns').css('display', 'block');
            $('#option_list').html('<p>'+
                '<label for="option_list" class="col-md-12">'+
                '<span class="col-md-10"></span>'+
                '<span class="col-md-2"><a href="#" id="addScnt" class="btn btn-info pull-right">Add New Option</a></span>'+
                '</label>'+
                '</p>');
        },
        success: function(response) {
            var is_required = response.data.is_required;
            var is_multiselect = response.data.is_multiselect;
            var main_category = response.data.is_main;
            var category_name = response.data.name;
            var category_id = response.data.id;

            var visibility_arr = response.data.master_classification_allocations;

            var master_classification_options = response.category;

            //append all option names and values to the input fields
            var count = 1;
            $.each(master_classification_options, function( index, value ) {
                var childCategory=value.children[1];

                if(count==1){
                    var items='<div>'+
                        '<label for="option_list" class="col-md-12">' +
                        '<row class="col-md-12">' +
                        '<span class="col-md-10">' +
                        '<input type="text" size="20" name="option" data-parentid="'+value.parent_id+'" id="option_'+ value.id +'" class="form-control valid " value="'+ value.name +'" placeholder="Option Name">' +
                        '</span>' +
                        '<span class="col-md-2">' +
                        '<a href="#" id="addSubcnt" class="btn btn-success addSubcnt" data-parent="'+value.id+'">+</a>' +
                        '</span>' +
                        '</row>';


                        $.each(childCategory,function (index,res) {
                        items+='<p><label for="option_list" class="col-md-12">' +
                        '<row class="col-md-12">' +
                        '<span class="col-md-6"><input type="text" size="20"  data-parentid="'+res.parent_id+'" name="option" id="option_'+res.id+'" class="form-control valid " value="'+ res.name +'" placeholder="Option Name" /></span>' +
                        '<span class="col-md-2">' +
                        '<a href="#" id="remScnt" class="btn btn-danger remScntChild">Remove</a>' +
                        '</span>' +
                        '</row>' +
                        '</label>' +
                        '</p>';
                    })
                        items+='</label>' +
                            '</div>'+
                            '<span id="err-option"></span>';
                    $(items).appendTo('#option_list');
                }else{
                    var items='<div>' +
                        '<label for="option_list" class="col-md-12">' +
                        '<row class="col-md-12">' +
                        '<span class="col-md-10">' +
                        '<input type="text" size="20" name="option" data-parentid="'+value.parent_id+'" id="option_'+ value.id +'" class="form-control valid " value="'+ value.name +'" placeholder="Option Name">' +
                        '</span>' +
                        '<span class="col-md-2">' +
                        '<a href="#" id="addSubcnt" data-parent="'+value.id+'" class="btn btn-success addSubcnt">+</a>' +
                        '<a href="#" id="remScnt" class="btn btn-danger remScnt">Remove</a>' +
                        '</span>' +
                        '</row>' ;

                    $.each(childCategory,function (index,res) {
                        items+='<p><label for="option_list" class="col-md-12" style="margin-top: 6px;">' +
                            '<row class="col-md-12">' +
                            '<span class="col-md-6"><input type="text" size="20"  data-parentid="'+res.parent_id+'" name="option" id="option_'+res.id+'" class="form-control valid " value="'+ res.name +'" placeholder="Option Name" /></span>' +
                            '<span class="col-md-2">' +
                            '<a href="#" id="remScnt" class="btn btn-danger remScntChild">Remove</a>' +
                            '</span>' +
                            '</row>' +
                            '</label>' +
                            '</p>';
                    })
                    items+='</label>' +
                        '</div>';
                    '<span id="err-option"></span>';
                    $(items).appendTo('#option_list');
                }
                count++;
            });

            //check if any visible entities
            $.each(visibility_arr, function( index, value ) {
                console.log(value.entity_type_id);
                if(value.entity_type_id==2){ $('#mjb').prop('checked', true); }
                if(value.entity_type_id==3){ $('#cc').prop('checked', true); }
                if(value.entity_type_id==4){  $('#ge').prop('checked', true); }

            });

            //select is required option
            if(is_multiselect==1)
            {
                $('.is_multi_yes').parent().addClass('checked');
                $('.is_multi_yes').attr('checked', 'checked');
                $('.is_multi_yes').prop('checked', true);
            }else{
                $('.is_multi_no').parent().addClass('checked');
                $('.is_multi_no').attr('checked', 'checked');
                $('.is_multi_no').prop('checked', true);
            }

            //select is required option
            if(is_required==1)
            {
                $('.is_req_yes').parent().addClass('checked');
                $('.is_req_yes').attr('checked', 'checked');
                $('.is_req_yes').prop('checked', true);
            }else{
                $('.is_req_no').parent().addClass('checked');
                $('.is_req_no').attr('checked', 'checked');
                $('.is_req_no').prop('checked', true);
            }

            //select is main option
            if(main_category==1)
            {
                $('.required_options_btns').css('display', 'none');
                $('#is_main_cat').val(1);
                $('.is_main_yes').parent().addClass('checked');
            }else{
                $('#is_main_cat').val(0);
                $('.is_main_no').parent().addClass('checked');
            }
            $('#category_name').val(category_name);
            $('#id').val(category_id);

            $('#question-category-edit-model').modal('show');
        }
    });
}
//quesiton category edit mode option list validation
function checkOptionListEmpty() {
    var isValid = true;
    $("#option_list").find('input').each (function(index, value) {
        var value = $(this).val();

        if(value == ""){
            $(this).css( "border", "solid 1px #ff0000" );

            if($("#err-"+index).length == 0) {
                $(this).parent().append('<span id="err-'+ index +'" style="color: #ff0000;">This field cannot be empty</span>');
            }

            isValid = false;
        }
        else {
            $(this).css( "border", "solid 1px #e4e5e7" );
        }

    });
    return isValid;
}
/**
 * Show notification message function
 * @param msg
 * @param msg_type
 */
function msgAlert(msg, msg_type) {
    toastr.options = {
        "debug": false,
        "newestOnTop": false,
        "positionClass": "toast-top-center",
        "closeButton": true,
        "toastClass": "animated fadeInDown"
    };
    if(msg_type == 'success') {
        toastr.success(msg);
    } else if(msg_type == 'error') {
        toastr.error(msg);
    }

}
function ajax_Datatable_Loader(){
    dataTable =  $('#question-category-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "paginate" : true,
        "bSort" : false,
        "sAjaxSource":"/qcategories/filter/"

    } );
    $("#question-category-table_filter").css("display","none");
}