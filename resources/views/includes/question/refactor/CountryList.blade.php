<label class="col-sm-2 control-label">Country*</label>
<div class="col-sm-3">
    <select class="form-control" name="country" ng-model="form.country" id="question_law" ng-options="country.id as country.name for country in countries" required ng-disabled="!isOnEdit">

    </select>
    <div ng-messages="addNewChildQuestion.country.$error" ng-if="addNewChildQuestion.$submitted || !addNewChildQuestion.country.$pristine">
        <p ng-message="required">This field is required.</p>
    </div>
</div>