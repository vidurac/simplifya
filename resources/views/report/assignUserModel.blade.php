<div class="modal fade" id="myModal0" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form name="AssignUserform" id="AssignUserform" method="post">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title text-center">Assign Users</h4>
                </div>
                <div class="modal-body">
                    <ul id="location_based_users"></ul>
                    <input type="hidden" name="action_id" id="action_id" value="" />
                    <input type="hidden" name="appointmentId" id="appointmentId" value="" />
                    <input type="hidden" name="inspection_no" id="inspection_no" value="" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="assign_user_btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>