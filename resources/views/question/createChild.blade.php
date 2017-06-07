@extends('layout.dashbord')



@section('content')
    <div class="content">
    <div class="row" ng-controller="childQuestion" ng-cloak="">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body breadcrumb-panel">
                    <ol class="breadcrumb">

                        @foreach($levelTree as $item)
                            @if ($item['is_root'] == 'yes')
                                <li><a href="{{URL('question/editQuestion/'.$item['id'])}}">{{ str_limit($item['question'], $limit = 125, $end = '...') }}</a></li>
                            @else
                                <li><a href="{{URL('question/edit/child/'.$item['id'].'/1')}}">{{ str_limit($item['question'], $limit = 125, $end = '...') }}</a></li>
                            @endif
                        @endforeach
                        @if (isset($create) && $create)
                            <li>New</li>
                        @endif
                    </ol>
                </div>
                <div class="panel-body">

                    <form role="form" id="create_question_from" name="addNewChildQuestion" class="form-horizontal" novalidate ng-init="isOnEdit=<?php if ($visibility) echo "true"; else echo "false"; ?>; @if(isset($question_id))getChildQuestion(<?php echo $question_id.','.$parent_id?>)@else;init();@endif">

                        @if (isset($question_id) && !$visibility && (isset($is_draft) && $is_draft))
                        <div class="form-group">
                            <div class="col-sm-3 pull-right">
                                <a class="btn w-xs btn-info pull-right"  href="{{URL('question/edit/child/'.$question_id.'/1')}}"><strong>Edit</strong></a>
                            </div>
                        </div>
                        @endif

                        <div class="form-group law-section">
                            @include('includes.question.refactor.LawList')
                        </div>
                        <div class="form-group country-section ">
                            @include('includes.question.refactor.CountryList')
                        </div>
                        <div class="form-group state-section" ng-if="form.law!=1">
                            @include('includes.question.refactor.StateList')
                        </div>

                        <div class="form-group city-section" ng-if="form.law==3">
                            @include('includes.question.refactor.CityList')
                        </div>

                        <div class="form-group license-section" ng-if="form.law!=1">
                            @include('includes.question.refactor.LicenceList')
                        </div>
                        <div class="form-group" hidden>
                            @include('includes.question.refactor.MainCategoryList')
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label required-field">Question* </label>
                            <div class="col-sm-10">
                                <input class="form-control" type="text" placeholder="Question" name="question" id="question" ng-model="form.question" required ng-disabled="!isOnEdit">
                                <input type="hidden" name="law" id="sub_question_law" >
                                <div ng-messages="addNewChildQuestion.question.$error" ng-if="addNewChildQuestion.$submitted || !addNewChildQuestion.question.$pristine" class="error">
                                    <label ng-message="required" class="error">This field is required.</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Question Topic*</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="1" placeholder="Explanation" name="explanation" id="explanation" ng-model="form.explanation" required ng-disabled="!isOnEdit" ng-maxlength="26"></textarea>
                                <div ng-messages="addNewChildQuestion.explanation.$error" ng-if="addNewChildQuestion.$submitted || !addNewChildQuestion.explanation.$pristine" class="error">
                                    <label ng-message="required" class="error">This field is required.</label>
                                    <label ng-message="maxlength" class="error">Please enter no more than 26 characters.</label>
                                </div>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-sm-2 control-label">Action Items*</label>
                            <input type="hidden" name="actionItemValidation">
                            <div class="col-sm-10">
                                <table id="addActionItemTable" class="col-sm-11 pull-left">

                                    <tbody id="addActionItemTableBody">
                                        <tr ng-repeat="actionItem in form.actionItems">
                                            <td class="questionTableTd" style="display: none">@{{$index+1}}</td>
                                            <td class="questionTableTdInput">
                                                <input name="action_item_@{{ $index }}"  class="action_item_data marginButtom1 col-sm-11 form-control" type="text" id="action_name_1_0_0" ng-model="actionItem.name" required ng-disabled="!isOnEdit">
                                                <div ng-messages="addNewChildQuestion['action_item_' + $index].$error" ng-if="addNewChildQuestion.$submitted || !addNewChildQuestion['action_item_' + $index].$pristine">
                                                    <label class="error" ng-message="required">This field is required.</label>
                                                </div>
                                            </td>
                                            <td class="questionTableTd">
                                                <button class="btn btn-danger btn-circle marginLeft5 marginTopMin6 pull-left btn_delete_item" type="button" item-number="1" ng-show="!$first && isOnEdit" ng-click="removeActionItem($index)"><i class="fa fa-times"></i></button>
                                                <button class="btn btn-success btn-circle marginLeft5 marginTopMin6 btnAddNewActionItem" id="btnAddNewActionItem" type="button" ng-click="addNewActionItem()" ng-show="isOnEdit"><i class="fa fa-plus"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            @include('includes.question.refactor.AuditTypesList')
                        </div>


                        <div class="form-group" ng-show="otherClassifications.length">
                            @include('includes.question.refactor.ClassifcationList')
                        </div>

                        <div class="form-group" ng-show="otherClassificationsNotRequired.length">
                            @include('includes.question.refactor.ClassificationListNotReqList')
                        </div>

                        <div class="form-group">

                            @include('includes.question.refactor.CitationList')

                        </div>

                        <div class="form-group">
                            @include('includes.question.refactor.QuestionAnswer')
                        </div>


                        <div class="form-group">
                            <div class="col-sm-6 pull-right">
                                @if ($visibility)
                                    @if(isset($question_id))
                                        <button class="create_parent_question btn w-xs btn-info pull-right" type="submit" save-type="save" style="margin-left: 2%" ng-click="saveChildQuestion(<?php echo $question_id?>)"><strong>Update</strong></button>
                                    @else
                                        <button class="create_parent_question btn w-xs btn-success pull-right" type="submit" save-type="save" style="margin-left: 2%" ng-click="saveChildQuestion()"><strong>Save</strong></button>
                                    @endif
                                @endif
                                <a href="/question" class="btn w-xs btn w-xs btn-default pull-right"><strong>Cancel</strong></a>
                            </div>

                        </div>


                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>

    {!! Html::script('/js/angular/createQuestions.js') !!}

    <style>
        .marginLeft5{
            margin-left: 5%;
        }
        .marginButtom1{
            margin-bottom: 1% !important;
        }

        .breadcrumb-panel {
            margin-bottom: 25px !important;
            padding: 5px 10px !important;
        }
        .breadcrumb-panel .breadcrumb li a,
        .breadcrumb-panel .breadcrumb li a:visited,
        .breadcrumb-panel .breadcrumb li a:focus,
        .breadcrumb-panel .breadcrumb li a:active{
            color: #337ab7;
        }
        .create_question_child{
            height:auto!important;
        }
        .breadcrumb {
            margin-bottom: 0;
            padding-left: 0;
            padding-right: 0;
            background: transparent;
        }
        .breadcrumb li { display:inline; }
        .breadcrumb>li+li:before { font-family: "FontAwesome"; content: "\f105"; color:#000; font-weight: bold; }
        .answer-box-panel {
            margin-bottom: 5px;
        }
        .answer-box-panel .checkbox { min-height: 20px !important;padding-top: 0; }
        .form-control.ng-invalid.ng-dirty {
            /*border-color: #e74c3c !important;*/
        }
        .ui-select-search.input-xs {
            font-size: 14px;
        }
        .ui-select-multiple.ui-select-bootstrap .ui-select-match-item.btn-xs {
            font-size: 14px;
        }
        .ui-select-search.btn-xs {
            font-size: 14px;
        }
    </style>

@stop