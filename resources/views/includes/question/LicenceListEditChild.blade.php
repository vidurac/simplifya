@if(isset($supperParentQuestion))
    <?php $question_law=$supperParentQuestion->law; ?>
@else
    <?php $question_law=$question->law; ?>
@endif
<label class="col-sm-2 control-label" name="addLicenceTable">License*</label>

<input type="hidden" name="licenceValidation">
<?php //print_r(json_encode($masterLicenses)); ?>

<div class="col-sm-9">
    <table id="editLicenceTable{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}" class="col-sm-12 " table-id="editLicenceTableChild">

        <tbody id="editLicenceTableBody">
        @if (isset($federalLicenses) && $question_law=='1')
        @if(count($federalLicenses)>0)
        @foreach($federalLicenses as $index => $federalLicense)
        <tr>
            <td class="questionTableTd" style="display: none">{{$index+1}}</td>
            <td class="questionTableStateTdInput">
                <select class="form-control states_fedaral"  id="states_fedaral_{{$index+1}}" custom-attr="state_type">
                    @foreach($states as $state)
                    <?php $isExists = false; ?>
                    @if($state->id==$federalLicense->state_id)
                        <?php $isExists = true; ?>
                    @endif

                    @if($isExists)
                        <option value="{{$state->id}}" selected>{{$state->name}}</option>
                    @else
                        <option value="{{$state->id}}">{{$state->name}}</option>
                    @endif

                    @endforeach
                </select>

            </td>
            <td class="questionTableTdInput">
                <select class="license_data licence{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}} marginButtom1 col-sm-11 form-control padding0 selectDrop_edit" name="license_type_{{$index+1}}" id="license_type_{{$index+1}}" multiple="multiple" custom-attr="licence_type">
                    <?php $options =  explode(',', $federalLicense->licenses->option_value); ?>


                    @foreach($federalLicense->masterLicenses as $masterLicense)
                        <?php $isExists = false; ?>
                        @foreach($options as $indexSub => $option)
                            @if($option == $masterLicense->id)
                                <?php $isExists = true; ?>
                            @endif
                        @endforeach

                        @if($isExists)
                            <option value="{{$masterLicense->id}}" selected>{{$masterLicense->name}}</option>
                        @else
                            <option value="{{$masterLicense->id}}">{{$masterLicense->name}}</option>
                        @endif

                    @endforeach
                </select>
            </td>
            <td class="questionTableTd">
                @if($index == 0)
                    <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1"><i class="fa fa-times"></i></button>
                    <button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem btnEditLicenseChild" id="btnEditLicenseChild" licence_class="licence{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}" type="button"><i class="fa fa-plus"></i></button>
                @else
                    <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button>
                @endif
            </td>
        </tr>
        @endforeach
        @else
            <tr>
                <td class="questionTableTd" style="display: none">1</td>
                <td class="questionTableStateTdInput hidden">
                    <select class="form-control states_fedaral"  id="states_fedaral_1" custom-attr="state_type">
                    </select>

                </td>
                <td class="questionTableTdInput">
                    <select class="license_data marginButtom1 col-sm-11 form-control"  name="license_type_1" id="license_type_1" disabled multiple="multiple" custom-attr="licence_type">
                    </select>

                </td>

                <td class="questionTableTd">
                    <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1"><i class="fa fa-times"></i></button>
                    <button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem" id="btnAddLicense" type="button"><i class="fa fa-plus"></i></button>
                </td>

            </tr>
        @endif

        @else
            @foreach($licences as $index => $licence)
                <tr>
                    <td class="questionTableTd" style="display: none">{{$index+1}}</td>
                    <td class="questionTableTdInput">
                        <select class="license_data licence{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}} marginButtom1 col-sm-11 form-control padding0 selectDrop_edit" name="license_type_{{$index+1}}" id="license_type_{{$index+1}}" multiple="multiple" custom-attr="licence_type">


                            @foreach($masterLicenses as $masterLicense)
                                <?php $options =  explode(',', $licence->option_value); $isExists = false;?>
                                @foreach($options as $indexSub => $option)
                                    @if($option == $masterLicense->id)
                                        <?php $isExists = true; ?>
                                    @endif
                                @endforeach

                                @if($isExists)
                                    <option value="{{$masterLicense->id}}" selected>{{$masterLicense->name}}</option>
                                @else
                                    <option value="{{$masterLicense->id}}">{{$masterLicense->name}}</option>
                                @endif

                            @endforeach
                        </select>
                    </td>
                    <td class="questionTableTd">
                        @if($index == 0)
                            <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1"><i class="fa fa-times"></i></button>
                            <button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem btnEditLicenseChild" id="btnEditLicenseChild" licence_class="licence{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}" type="button"><i class="fa fa-plus"></i></button>
                        @else
                            <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button>
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>