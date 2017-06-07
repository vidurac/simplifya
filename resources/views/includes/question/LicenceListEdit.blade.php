@if(isset($supperParentQuestion))
    <?php $question_law=$supperParentQuestion->law; ?>
    @else
    <?php $question_law=$question->law; ?>
    @endif

<label class="col-sm-2 control-label" name="addLicenceTable">License*</label>

<input type="hidden" name="licenceValidation">
<div class="col-sm-9">
    <table id="editLicenceTable" class="col-sm-12 " table-id="editLicenceTable">

        <tbody id="editLicenceTableBody">

        @if (isset($federalLicenses) && $question_law=='1')
            @if (!count($federalLicenses))
                <tr>
                    <td class="questionTableTd" style="display: none">1</td>
                    <td class="questionTableStateTdInput state-list-position-top">
                        <div class="row">
                            <div class="col-xs-12">
                                <label>State:</label>
                                <select {{isset($viewOnly)? 'disabled' : ''}} class="form-control states_fedaral"  id="states_fedaral_1" custom-attr="state_type">
                                </select>
                            </div>
                        </div>

                    </td>
                    <td class="questionTableTdInput">
                        <div class="col-xs-12">
                            <label>License Type:</label>
                            <select {{isset($viewOnly)? 'disabled' : ''}} class="license_data marginButtom1 col-sm-11 form-control"  name="license_type_1" id="license_type_1" disabled multiple="multiple" custom-attr="licence_type">
                            </select>
                        </div>

                    </td>
                    <td class="questionTableTd" valign="bottom">
                        <div class="col-xs-12 m-b-xs">
                            <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1"><i class="fa fa-times"></i></button>
                            <button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem" id="btnEditLicense" type="button"><i class="fa fa-plus"></i></button>
                        </div>
                    </td>

                </tr>
            @endif
                @foreach($federalLicenses as $index => $federalLicense)
                    <tr>
                        <td class="questionTableTd" style="display: none">{{$index+1}}</td>
                        <td class="questionTableStateTdInput state-list-position-top">
                            <div class="row">
                                <div class="col-xs-12">
                                    <label>State:</label>
                                    <select {{isset($viewOnly)? 'disabled' : ''}} class="form-control states_fedaral"  id="states_fedaral_{{$index+1}}" custom-attr="state_type">
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
                                </div>
                            </div>

                        </td>
                        <td class="questionTableTdInput">
                            <div class="col-xs-12">
                                <label>License Type:</label>
                                <select {{isset($viewOnly)? 'disabled' : ''}} class="license_data license_data_main marginButtom1 col-sm-11 form-control padding0 selectDrop_edit" name="license_type_{{$index+1}}" id="license_type_{{$index+1}}" multiple="multiple" custom-attr="licence_type">

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
                            </div>
                        </td>
                        <td class="questionTableTd" valign="bottom">
                            <div class="col-xs-12 m-b-xs">
                                @if(!isset($viewOnly))
                                    @if($index == 0)
                                        <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1"><i class="fa fa-times"></i></button>
                                        <button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem" id="btnEditLicense" type="button"><i class="fa fa-plus"></i></button>
                                    @else
                                        <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                @if (!count($licences))
                    <tr>
                        <td class="questionTableTd" style="display: none">1</td>
                        <td class="questionTableTdInput">
                            <select class="license_data marginButtom1 col-sm-11 form-control"  name="license_type_1" id="license_type_1" disabled multiple="multiple" custom-attr="licence_type">
                            </select>

                        </td>
                        <td class="questionTableTd">
                            <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1"><i class="fa fa-times"></i></button>
                            <button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem" id="btnEditLicense" type="button"><i class="fa fa-plus"></i></button>
                        </td>

                    </tr>
                @endif
                @foreach($licences as $index => $licence)
                    <tr>
                        <td class="questionTableTd" style="display: none">{{$index+1}}</td>
                        <td class="questionTableTdInput">
                            <select {{isset($viewOnly)? 'disabled' : ''}} class="license_data license_data_main marginButtom1 col-sm-11 form-control padding0 selectDrop_edit" disabled name="license_type_{{$index+1}}" id="license_type_{{$index+1}}" multiple="multiple" custom-attr="licence_type">

                                <?php $options =  explode(',', $licence->option_value); ?>

                                @if($licence->option_value == "GENERAL")
                                    <option value="GENERAL" selected>GENERAL</option>

                                    {{--@else
                                            <option value="GENERAL">GENERAL</option>--}}
                                @endif

                                @foreach($masterLicenses as $masterLicense)
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
                            @if(!isset($viewOnly))
                                @if($index == 0)
                                    <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1"><i class="fa fa-times"></i></button>
                                    <button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem" id="btnEditLicense" type="button"><i class="fa fa-plus"></i></button>
                                @else
                                    <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button"><i class="fa fa-times"></i></button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif


                @if(isset($licencesH))
                    <input type="hidden" id="licen_idsH" value="{{json_encode($licencesH)}}" />
                @else
                    <input type="hidden" id="licen_idsH" value="" />
                @endif
        </tbody>
    </table>
</div>