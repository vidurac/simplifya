<label class="col-sm-2 control-label" name="addLicenceTable">License*</label>

    <input type="hidden" name="licenceValidation">


<div class="col-sm-10">
    <table id="addLicenceTable" class="col-sm-12">

        <tbody id="addLicenceTableBody">
        <tr ng-repeat="licenseChoise in form.licenseChoises">
            <td class="questionTableTd" style="display: none">@{{$index+1}}</td>
            <td class="questionTableTdInput">
                    <ui-select name="license_multi_@{{ $index }}" multiple ng-model="licenseChoise.multi"  theme="bootstrap" close-on-select="false" ui-select-required ng-disabled="!isOnEdit">
                        <ui-select-match placeholder="Select license...">@{{$item.name}}</ui-select-match>
                        <ui-select-choices repeat="license in licenseChoise.licenses | filter:$select.search">
                            @{{license.name}}
                        </ui-select-choices>
                    </ui-select>
                <div ng-messages="addNewChildQuestion['license_multi_' + $index].$error" ng-if="addNewChildQuestion.$submitted || !addNewChildQuestion['license_multi_' + $index].$pristine">
                    <label class="error" ng-message="uiSelectRequired">This field is required.</label>
                </div>
            </td>

            <td class="questionTableTd">
                    <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1" ng-show="!$first && isOnEdit" ng-click="removeChoice($index)" ng-show="isOnEdit"><i class="fa fa-times"></i></button>
                    <button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem" id="btnAddLicense" type="button" ng-click="addNewChoice()" ng-show="isOnEdit"><i class="fa fa-plus"></i></button>
            </td>

        </tr>
        </tbody>
    </table>
</div>