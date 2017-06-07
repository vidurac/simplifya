<label class="col-sm-2 control-label">Citi(es)*</label>
<div class="col-sm-10 city-wrap">
    <ui-select multiple ng-model="form.city"  theme="bootstrap" close-on-select="false" name="city"  ui-select-required ng-disabled="!isOnEdit">
        <ui-select-match placeholder="Select city...">@{{$item.name}}</ui-select-match>
        <ui-select-choices repeat="city in cities | filter:$select.search">
            @{{city.name}}
        </ui-select-choices>
    </ui-select>
    <div ng-messages="addNewChildQuestion.city.$error" ng-if="addNewChildQuestion.$submitted || !addNewChildQuestion.city.$pristine">
        <label class="error" ng-message="uiSelectRequired">This field is required.</label>
    </div>
</div>





