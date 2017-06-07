    <label class="col-sm-2 control-label" >Other Classifications</label>
    <div class="col-sm-10">
        <table id="nonReqClassifictionTable" class="col-sm-10 pull-left">
            {{--<thead>--}}
            {{--<tr>--}}
                {{--<th>Classification Type</th>--}}
                {{--<th>Options</th>--}}
            {{--</tr>--}}
            {{--</thead>--}}

            <tbody>



            <tr ng-repeat="otherClassificationNotRequired in otherClassificationsNotRequired">
                <td class="col-sm-2" style="padding: 5px;"> @{{otherClassificationNotRequired.name}}</td>
                <td class="col-sm-8" style="padding: 5px;">
                    <ui-select multiple ng-model="otherClassificationNotRequired.selected" theme="bootstrap" close-on-select="true"  ng-disabled="!isOnEdit">
                        <ui-select-match placeholder="Select other classification...">@{{$item.name}}</ui-select-match>
                        <ui-select-choices repeat="option in otherClassificationNotRequired.option_value | filter:$select.search">
                            @{{option.name}}
                        </ui-select-choices>
                    </ui-select>
                </td>
            </tr>



            </tbody>
        </table>
    </div>
