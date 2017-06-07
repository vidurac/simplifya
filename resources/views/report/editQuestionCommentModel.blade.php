<div class="modal fade" id="editQuestionCommentModel" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form name="editQuestionCommentForm" id="editQuestionCommentForm" method="post">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Edit Comment</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <label class="form-group col-md-12">Note:
                            <textarea class="form-control" name="question_comment" id="question_comment"></textarea>
                            <span id="err-question_comment"></span>
                        </label>
                        <input type="hidden" id="question_id" value="" />
                        <input type="hidden" id="appointment_id" value="" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="remove_qcomment_btn" style="display: none;">Remove</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="update_qcomment_btn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>