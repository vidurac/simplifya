<label class="col-sm-2 control-label">Law*</label>
<div class="col-sm-3">
    <select class="form-control" name="law" ng-change="removeSelectedCities()" ng-model="form.law" id="question_law" ng-options="law.value as law.name for law in laws" required ng-disabled="!isOnEdit">

    </select>
    <div ng-messages="addNewChildQuestion.law.$error" ng-if="addNewChildQuestion.$submitted || !addNewChildQuestion.law.$pristine">
        <p ng-message="required">This field is required.</p>
    </div>
</div>