<div class="modal fade" id="addQuestionCommentModel" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form name="addQuestionCommentForm" id="addQuestionCommentForm" method="post">
                <div class="color-line"></div>
                <div class="modal-header">
                   <h4 class="modal-title"><span id="add-edit-comment">Add</span> Comment</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <label class="form-group col-md-12">Note:</label>
                        <textarea class="form-control" name="qComment" id="qComment"></textarea>
                        <span id="err-qComment"></span>
                        <input type="hidden" id="question_id" value="" />
                        <input type="hidden" id="appointment_id" value="" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="remove_qcomment_btn">Remove</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="add_qcomment_btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>