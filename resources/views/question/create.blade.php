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

                    <form role="form" id="create_question_from" class="form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Visibility</label>
                            <div class="col-sm-4">
                                <div class="radio col-sm-6"><label> <input type="radio" name="visibility" class="i-checks" value="1" checked> Active </label></div>
                                <div class="radio col-sm-6"><label> <input type="radio" name="visibility" class="i-checks" value="0"> Inactive </label></div>


                            </div>

                            <div class="col-sm-5 hidden">
                                <label class="col-sm-2 control-label">Mandatory</label>
                                <div class="radio col-sm-4"><label> <input type="radio" name="mandatory" value="1" class="i-checks" checked> Yes </label></div>
                                <div class="radio col-sm-4"><label> <input type="radio" name="mandatory" value="0" class="i-checks" > No </label></div>

                            </div>
                            
                            <div class="col-sm-1">
                            </div>
                        </div>

                        <div class="form-group">
                            @include('includes.question.PublishDate')
                        </div>
                        
                        <div class="form-group law-section">
                            @include('includes.question.LawList')
                        </div>
                        <div class="form-group country-section ">
                            @include('includes.question.CountryList')
                        </div>
                        <div class="form-group apply-all-section">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-4">
                                <input type="checkbox" id="select-license"> Applies to specific license type in each state
                            </div>
                        </div>

                        <div class="form-group state-section hidden">
                            @include('includes.question.StateList')
                        </div>

                        <div class="form-group city-section hidden">
                            @include('includes.question.CityList')

                        </div>

                        {{--<div class="form-group">--}}
                        {{--<label class="col-sm-2 control-label"></label>--}}
                        {{--<div class="col-sm-3">--}}
                        {{--<label id="chkCreateQuestionSelectAllCityError"></label>--}}
                        {{--</div>--}}
                        {{--</div>--}}


                        <div class="form-group license-section hidden">
                            @include('includes.question.LicenceList')
                        </div>
                        <div class="form-group">
                            @include('includes.question.MainCategoryList')
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Question Topic*</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="1"  placeholder="Question Topic" name="explanation" id="explanation" required></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label required-field">Question* </label>
                            <div class="col-sm-10">
                                <input class="form-control" type="text" placeholder="Question " name="question" id="question" required>
                            </div>
                        </div>

                        <div class="form-group">
                            @include('includes.question.KeywordList')
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Action Items*</label>
                            <input type="hidden" name="actionItemValidation">
                            <div class="col-sm-10">
                                <table id="addActionItemTable" class="col-sm-11 pull-left">

                                    <tbody id="addActionItemTableBody">
                                        <tr>
                                            <td class="questionTableTd" style="display: none">1</td>
                                            <td class="questionTableTdInput"><input class="action_item_data marginButtom1 col-sm-11 form-control" type="text" id="action_name_1_0_0"></td>
                                            <td class="questionTableTd">
                                                <button class="btn btn-danger btn-circle marginLeft5 marginTopMin6 pull-left btn_delete_item" type="button" item-number="1"><i class="fa fa-times"></i></button>
                                                <button class="btn btn-success btn-circle marginLeft5 marginTopMin6 btnAddNewActionItem" id="btnAddNewActionItem" type="button"><i class="fa fa-plus"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<div class="col-sm-6 pull-right">--}}
                                {{--<input type="button" class="btn w-xs btn-warning2 btnAddNewActionItem" id="btnAddNewActionItem" value="Add New Action Item">--}}
                            {{--</div>--}}
                        {{--</div>--}}





                        <div class="form-group">
                            @include('includes.question.AuditTypesList')
                        </div>



                        {{--<div class="form-group">--}}
                            {{--<div class="col-sm-6 pull-right">--}}
                                {{--<input type="button" class="btn w-xs btn-primary2" id="btnAddLicense" value="Add New License">--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group">
                            @include('includes.question.ClassifcationList')
                        </div>

                        <div class="form-group">
                            @include('includes.question.ClassificationListNotReqList')
                        </div>

                        <div class="form-group">

                            @include('includes.question.CitationList')

                        </div>
                        <div class="form-group">
                            @include('includes.question.ParentAnswer')
                        </div>



                        <div class="form-group">
                            <div class="col-sm-7 pull-right">

                                <button class="create_parent_question btn w-xs btn-success pull-right" type="button" save-type="publish" style="margin-left: 2%"><strong>Save & Publish</strong></button>
                                <button class="create_parent_question btn w-xs btn-success pull-right" type="button" save-type="save" style="margin-left: 2%"><strong>Save</strong></button>
                                <a href="/question" class="btn w-xs btn w-xs btn-default pull-right"><strong>Cancel</strong></a>
                                <div class="checkbox add-new-question-check">
                                    <label>
                                        <input type="checkbox" id="create_new" checked="checked">Add New Question
                                    </label>
                                </div>
                            </div>

                        </div>


                    </form>

                </div>
            </div>
        </div>
    </div>
    </div> {{-- close content div--}}

    {!! Html::script('/js/question/createQuestion.js') !!}

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
        /* parent question question create add new question checkbox wrapper */
        .add-new-question-check {
            display: inline-block;float: right;padding-right: 10px;
        }
    </style>

@stop