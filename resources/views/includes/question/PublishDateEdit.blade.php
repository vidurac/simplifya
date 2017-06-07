<label class="col-sm-2 control-label">Publish Date</label>
<div class="col-sm-3">
    <div class="input-group input-append date" id="publishDateEditPicker">
        <input type="text" class="form-control" name="publishDateEdit" id="publishDateEdit" placeholder="Publish Date" {{isset($viewOnly)? 'disabled' : ''}}>
        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
    </div>
	<label id="publishDateError"></label>	
</div>
