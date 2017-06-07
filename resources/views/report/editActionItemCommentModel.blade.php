<div class="modal fade" id="editActionItemCommentModel" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form name="editActionItemCommentForm" id="editActionItemCommentForm" method="post">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Edit Comment</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <label class="form-group col-md-12">Note:
                            <textarea class="form-control" name="action_comment" id="action_comment"></textarea>
                            <span id="err-action_comment"></span>
                        </label>
                        <input type="hidden" id="action_item_id" value="" />
                        <input type="hidden" id="comment_id" value="" />
                        <input type="hidden" id="appointment_id" value="" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="edit_action_comment_btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>