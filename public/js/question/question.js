
$(function() {
    $("#questionKeywords").select2();



});
$(window).bind("load", function () {
    // Remove splash screen after load
    $('.splash').css('display', '');
});

// Question table
function questionTable()
{
    // var questionName = $("#questionName").val();
    // var keywords = $("#questionKeywords").val();
    // var status = $("#status").val();
    // var display = $("#display").val();
    //
    //
    // var table = $('#question-detail-table').DataTable();
    // table.destroy();
    // $('#question-detail-table').dataTable({
    //     "ajax": {
    //         "url": "/questions/all",
    //         "type": "GET",
    //         data : {questionName:questionName,keywords:keywords,status:status,display:display},
    //     },
    //     "searching": false,
    //     "paging": true,
    //     "info": true,
    //     "autoWidth": false,
    //     "bSort": false,
    //     "aoColumns": [
    //         {"sWidth": "26%", "mData": 0},
    //         {"sWidth": "7%", "mData": 1},
    //         {"sWidth": "7%", "mData": 2},
    //         {"sWidth": "14%", "mData": 3},
    //         {"sWidth": "12%", "mData": 4},
    //         {"sWidth": "1%", "mData": 5},
    //         {"sWidth": "1%", "mData": 6},
    //     ]
    // });
}

// change question status
function changeQuestionStatus(questionId, status, isDraft){
    if(isDraft == 0){
        swal({
            title: "Error!",
            text: "You can't change status on published questions."
        });
    }
    else{
        $.ajax({
            url: "/questions/updateQuestionStatus",
            type: 'POST',
            dataType: 'json',
            data:{questionId:questionId, status:status},
            success: function(result){
                if(result.success == 'true') {
                    questionTable();
                    var msg = result.message;
                    var msg_type = 'success';
                    msgAlert(msg, msg_type);

                } else {
                    var msg = result.message;
                    var msg_type = 'error';
                    msgAlert(msg, msg_type);
                }
            }
        });
    }

}

// delete question
function deleteQuestion(questionId){
    swal({
            title: "Are you sure?",
            text: "You will not be able to recover this question. \n If you're unsure, cancel and deactivate the question to prevent it from appearing on checklists.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Delete",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function (isConfirm) {
            if (isConfirm) {

                $.ajax({
                    url: "/questions/deleteQuestion",
                    type: 'POST',
                    dataType: 'json',
                    data:{questionId:questionId},
                    success: function(result){
                        if(result.success == 'true') {
                            location.reload(true);
                            swal("Deleted!", "Your question has been deleted.", "success");
                        }
                    }
                });
            } else {
                swal("Cancelled", "Your question is safe :)", "error");
            }
        }
    );
}

// alert message
function msgAlert(msg, msg_type) {
    toastr.options = {
        "debug": false,
        "newestOnTop": false,
        "positionClass": "toast-top-center",
        "closeButton": true,
        "toastClass": "animated fadeInDown",
    };
    if(msg_type == 'success') {
        toastr.success(msg);
    } else if(msg_type == 'error') {
        toastr.error(msg);
    }
}

// update question redirect
function updateQuestion(questionId){

    var currentPage = $('#currentPage').attr("data-currentPage");
    var entries = $('#entries').val().split(':')[1];
    var questionName = $('#questionName').val();

    var questionKeywords = $('#questionKeywords').val();
    var status = $('#status').val();
    var display = $('#display').val();
    console.log(status+','+display)
    $.ajax({
        url: "/questions/saveUserQuestionSession",
        type: 'POST',
        dataType: 'json',
        data: {
            questionId:questionId,
            currentPage: currentPage,
            entries: entries,
            questionName: questionName,
            questionKeywords: questionKeywords,
            status: status,
            display: display
        },
        success: function (result) {
            if (result.success == 'true') {
                window.location.assign("/question/editQuestion/" + questionId);
            }
        }
    });
}

// view question redirect
function previewQuestion(questionId) {
    var currentPage = $('#currentPage').attr("data-currentPage");
    var entries = $('#entries').val().split(':')[1];
    var questionName = $('#questionName').val();
    var sort = $('#sort').attr('data-sort');
    var sortType = $('#sort').attr('data-sortType');

        var questionKeywords = $('#questionKeywords').val();
        var status = $('#status').val();
        var display = $('#display').val();
        console.log(status+','+display)
        $.ajax({
            url: "/questions/saveUserQuestionSession",
            type: 'POST',
            dataType: 'json',
            data: {
                questionId:questionId,
                currentPage: currentPage,
                entries: entries,
                questionName: questionName,
                questionKeywords: questionKeywords,
                status: status,
                display: display,
                sort:sort,
                sortType:sortType,
            },
            success: function (result) {
                if (result.success == 'true') {
                    window.location.assign("/question/viewQuestion/" + questionId);
                }
            }
        });
}
//question log view
function questionLogView(questionId){
    $.ajax({
        url: "/questions/findQuestionVersions",
        type: 'GET',
        dataType: 'json',
        data:{questionId:questionId},
        success: function(result){
            $(".version-modal-title").empty();
            $("#questionVersionList").empty();
            $(".version-modal-title").append("Versions for Question Number " + questionId);
            $(".noVersons").remove();

            if(result.data.length > 0){
                $.each(result.data, function(key, value){
                    var append = '<li><a style="color: #0000ff; text-decoration: underline;" href="/question/editQuestion/'+value.id+'" target="_blank"> Version No '+value.version_no +' - Created User : '+value.created_by +' - Created Date : '+value.created_at+ ' - Modified User : '+value.updated_by +' - Modified Date : '+value.updated_at+ ' </a></li>';
                    $("#questionVersionList").append(append);
                });
            }
            else{
                $("#questionVersionBody").append('<p class="noVersons">No Versions Found for this Question</p>')
            }
            $('#questionLogModel').modal('show');
        }
    });
}

function scrollToLastQuestion(id) {

    setTimeout(function(){
        $('html, body').animate({
            'scrollTop' : $('*[data-question_id="'+id+'"]').parent().position().top
        });
        $('*[data-question_id="'+id+'"]').parent().parent().parent().effect("highlight", {color:'#ababab'}, 3000);
    }, 1000);
}
