@extends('layout.dashbord')



@section('content')
    <div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                {{--<div class="panel-heading">--}}
                    {{--<div class="panel-tools">--}}
                        {{--<a class="showhide"><i class="fa fa-chevron-up"></i></a>--}}
                        {{--<a class="closebox"><i class="fa fa-times"></i></a>--}}
                    {{--</div>--}}
                    {{--Add New Question--}}
                {{--</div>--}}
                <div class="panel-body">
                    <?php $questionId = 0; $questionAnswerId = 0; $parentQuestionId = 0; $isDraft= 1; $isArchive = 0;?>
                    @if(isset($question))
                        <?php $questionId = $question->id; $date= new DateTime($question->published_at);?>
                        <?php $isDraft = $question->is_draft?>
                        <?php $isArchive = $question->is_archive?>
                    @endif

                    @if(isset($answer))
                        <?php $questionAnswerId = $answer->id?>
                        <?php $parentQuestionId = $answer->question_id?>
                    @endif
                        <input type="hidden" value="1" id="viewOrEdit">
                    <form role="form" id="edit_question_from" class="form-horizontal">

                        <input type="hidden" value="{{$question->id}}" id="supperQuestionId">

                        {{--@if($isDraft == 0 && $isArchive == 0)--}}
                            {{--<div class="form-group">--}}
                                {{--<div class="col-sm-3 pull-right">--}}
                                    {{--<button class="btn w-xs btn-info pull-right" type="button" id="create_new_version"><strong>Create New Version</strong></button>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--@endif--}}



                        @if($isDraft == 0 && $isArchive == 0)
                            <div class="form-group">
                                <div class="col-sm-3 pull-right">
                                    <button class="btn w-xs btn-info pull-right" type="button" id="create_new_version"><strong>Create New Version</strong></button>
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                                <div class="col-sm-3 pull-right">
                                    <button class="btn w-xs btn-info pull-right" type="button" onclick="updateQuestion({{$question->id}},true)"><strong>Edit</strong></button>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    <label class="col-sm-4 control-label">Visibility</label>
                                    <div class="col-sm-8">
                                        @if($question->status)
                                            <div class="radio col-sm-6 p-l-none p-r-none"><label> <input type="radio" name="visibility" class="i-checks" value="1" disabled checked> Active </label></div>
                                            <div class="radio col-sm-6 p-l-none p-r-none"><label> <input type="radio" name="visibility" class="i-checks" value="0" disabled> Inactive </label></div>
                                        @else
                                            <div class="radio col-sm-6 p-l-none p-r-none"><label> <input type="radio" name="visibility" class="i-checks" value="1" disabled> Active </label></div>
                                            <div class="radio col-sm-6 p-l-none p-r-none"><label> <input type="radio" name="visibility" class="i-checks" value="0" disabled checked> Inactive </label></div>
                                        @endif
                                    </div>
                                </div>
                                    <div class="col-sm-12 col-md-6 hidden">
                                    <label class="col-sm-4 control-label">Mandatory</label>
                                    <div class="col-sm-8">
                                        @if($question->is_mandatory)
                                            <div class="radio col-sm-6 p-l-none p-r-none"><label> <input type="radio" name="mandatory" value="1" class="i-checks" disabled checked> Yes </label></div>
                                            <div class="radio col-sm-6 p-l-none p-r-none"><label> <input type="radio" name="mandatory" value="0" class="i-checks" disabled> No </label></div>
                                        @else
                                            <div class="radio col-sm-6 p-l-none p-r-none"><label> <input type="radio" name="mandatory" value="1" class="i-checks" disabled> Yes </label></div>
                                            <div class="radio col-sm-6 p-l-none p-r-none"><label> <input type="radio" name="mandatory" value="0" class="i-checks" disabled checked> No </label></div>
                                        @endif
                                    </div>


                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            @include('includes.question.PublishDateEdit', ['viewOnly' => true])
                        </div>

                        <div class="form-group law-section-edit">
                            @include('includes.question.LawListEdit',['viewOnly' => true])
                        </div>

                        <div class="form-group country-section-edit">
                            @include('includes.question.CountryListEdit', ['viewOnly' => true])
                        </div>

                        <div class="form-group apply-all-section-edit">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-4">

                                @if (isset($federalLicenses) && count($federalLicenses))
                                    <input type="checkbox" id="select-license-edit" checked disabled>
                                @else
                                    <input type="checkbox" id="select-license-edit" disabled>
                                @endif
                                Applies to specific license type in each state
                            </div>
                        </div>

                        <div class="form-group state-section-edit hidden">
                            @include('includes.question.StateListEdit', ['viewOnly' => true])
                        </div>

                        <div class="form-group city-section-edit hidden">
                            @include('includes.question.CityListEdit', ['viewOnly' => true])
                        </div>

                        {{--<div class="form-group">--}}
                        {{--<label class="col-sm-2 control-label"></label>--}}
                        {{--<div class="col-sm-3">--}}
                        {{--<label id="chkCreateQuestionSelectAllCityError"></label>--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group license-section-edit hidden">
                            @include('includes.question.LicenceListEdit', ['viewOnly' => true])
                        </div>

                        <div class="form-group">
                            @include('includes.question.MainCategoryListEdit', ['viewOnly' => true])
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Question Topic*</label>
                            <div class="col-sm-10">
                                <textarea readonly="true" class="form-control" rows="2"  placeholder="Question Topic" name="explanation" id="explanation" required>{{$question->explanation}}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label required-field">Question* </label>
                            <div class="col-sm-10">
                                <textarea readonly="true" class="form-control" rows="2"  placeholder="Question" name="question" id="question" required>{{$question->question}}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            @include('includes.question.KeywordListEdit', ['viewOnly' => true])
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Action Items*</label>
                            <input type="hidden" name="actionItemValidation">
                            <div class="col-sm-10">
                                <table id="actionItemTable_edit" class="col-sm-11 pull-left">
                                    <tbody>
                                    @foreach($actionItems as $index => $item)
                                        <tr>
                                            <td class="questionTableTd" style="display: none">{{$index +1}}</td>
                                            <td class="questionTableTdInput"><input class="action_item_data marginButtom1 col-sm-11 form-control" type="text" value="{{$item->name}}" readonly></td>
                                        </tr>
                                    @endforeach

                                    @if(count($actionItems) == 0)
                                        <tr>
                                            <td class="questionTableTd" style="display: none">1</td>
                                            <td class="questionTableTdInput"><input class="action_item_data marginButtom1 col-sm-11 form-control" type="text" readonly></td>
                                        </tr>
                                    @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<div class="col-sm-6 pull-right">--}}
                                {{--<input type="button" class="btn w-xs btn-warning2 btnEditActionItem" id="btnEditActionItem" value="Add New Action Item">--}}
                            {{--</div>--}}
                        {{--</div>--}}





                        <div class="form-group">
                            @include('includes.question.AuditTypesListEdit', ['viewOnly' => true])
                        </div>


                        {{--<div class="form-group">--}}
                            {{--<div class="col-sm-6 pull-right">--}}
                                {{--<input type="button" class="btn w-xs btn-primary2" id="btnEditLicense" value="Add New License">--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group">
                            @include('includes.question.ClassificationListEdit', ['viewOnly' => true])
                        </div>

                        <div class="form-group">
                            @include('includes.question.ClassificationListNotReqListEdit', ['viewOnly' => true])
                        </div>

                        <div class="form-group">
                            @include('includes.question.CitationList')
                        </div>

                        <div class="form-group">
                            @include('includes.question.ChildAnswerView')
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6 pull-right">
                                <a href="/question" class="btn w-xs btn-default pull-right"><strong>Cancel</strong></a>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="questionVersionModel" tabindex="-1" role="dialog" aria-hidden="true">
        <form id="versionCommentForm">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header text-center">
                        <h4 class="modal-title"> Add Version Comment </h4>
                    </div>
                    <div class="modal-body col-sm-12">
                        <textarea id="versionComment" name="versionComment" placeholder="comment" rows="5" style="width: 100%;"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="create_new_version_model">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>{{-- close content div--}}

    {!! Html::script('/js/question/editQuestion.js') !!}

    <style>
        .marginLeft5{
            margin-left: 5%;
        }
        .marginButtom1{
            margin-bottom: 1% !important;
        }
        .create_question_child{
            height:auto!important;
        }
    </style>

    <script>

        if('<?php echo $date->format('Y')?>' > -1) {
            $('#publishDateEdit').datepicker('option', 'dateFormat', 'mm/dd/yyyy');
            $('#publishDateEdit').datepicker('setDate', new Date('<?php echo $date->format('Y')?>','<?php echo $date->format('m')-1?>','<?php echo $date->format('d')?>'));
        } else {
            $('#publishDateEdit').datepicker({
                format: 'mm/dd/yyyy',
                startDate: '+1d'
            }).on('changeDate', function(e) {});
        }


    </script>
@stop