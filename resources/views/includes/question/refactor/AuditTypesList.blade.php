<label class="col-sm-2 control-label">Audit Type(s)*</label>
<div class="col-sm-3">
    <ui-select name="audit_type" multiple ng-model="form.auditTypes"  theme="bootstrap" close-on-select="false" ui-select-required ng-disabled="!isOnEdit">
        <ui-select-match placeholder="Select audit type...">@{{$item.name}}</ui-select-match>
        <ui-select-choices repeat="auditType in auditTypes | filter:$select.search">
            @{{auditType.name}}
        </ui-select-choices>
    </ui-select>
    <div ng-messages="addNewChildQuestion.audit_type.$error" ng-if="addNewChildQuestion.$submitted || !addNewChildQuestion.audit_type.$pristine">
        <label class="error" ng-message="uiSelectRequired">This field is required.</label>
    </div>
</div>