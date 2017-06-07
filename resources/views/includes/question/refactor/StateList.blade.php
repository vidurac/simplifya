<label class="col-sm-2 control-label">State*</label>
<div class="col-sm-3">
    <select class="form-control" name="state" ng-model="form.state"  ng-options="state.id as state.name for state in states" required ng-disabled="!isOnEdit">

    </select>
    <div ng-messages="addNewChildQuestion.state.$error" ng-if="addNewChildQuestion.$submitted || !addNewChildQuestion.state.$pristine">
        <p ng-message="required">This field is required.</p>
    </div>
</div>