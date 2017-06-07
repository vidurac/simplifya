<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 6/6/2016
 * Time: 11:33 AM
 */
?>

@extends('layout.dashbord')

@section('content')
    <style>
        .accordion-toggle, a.accordion-toggle { outline: 0; }
        .accordion-toggle > span {
            width: 100%;
            display: block;
            outline: 0;
        }
        .accordion-toggle span {position: relative;}
        .accordion-toggle > span .heading_title { margin: 0; width: 95%; display: inline-block; white-space: nowrap;text-overflow: ellipsis;overflow: hidden;line-height: 1.5;}
        .accordion-toggle > span .heading_title.heading_title_91 { width: 91%;}
        .panel-open .accordion-toggle > span .heading_title { white-space: normal;text-overflow: initial;overflow: initial;}
        .accordion-toggle > span .heading_status { width: 5%;  display: inline;}
        .accordion-toggle > span .heading_status.comp {
            width: 18px;
            height: 18px;
            display: inline-block;
            float: right;
            border-radius: 50%;
            margin: -2px 0 0px 0;
        }
        .accordion-toggle > span .heading_status.non-comp {
            background-color: #ed0000;
        }
        .accordion-toggle > span .heading_status.comp-comp {
            background-color: green;
        }
        .accordion-toggle > span .heading_status.unknown-comp {
            background-color: #fff018;
        }
        .report_heading_text {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            line-height: 1.5;
        }
    </style>
    <div class="content animated-panel">

        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">

                    <div class="panel-body">
                        <div>
                            @if(isset($finalise))
                                @if($finalise != 3)
                                    <div class="form-group">
                                        <div class="col-sm-3 pull-right m-b">
                                            <button class="btn w-xs btn-info pull-right" type="button"
                                                    id="report_finalised"><strong>Finalize</strong></button>
                                            <input type="hidden" value="{{$entity_type}}" id="report_entity_type">
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="clearfix"></div>
                        <div class="text-center m-b-md" id="wizardControl">
                            {{--<a class="btn btn-primary" href="#step1" data-toggle="tab">Info</a>--}}
                            @if ($allowed)
                                <a class="btn btn-primary" href="#step2" data-toggle="tab" id="question_tab">All
                                    Questions</a>
                            @endif
                            <a class="btn btn-default" href="#step3" data-toggle="tab" id="action_items">Actions
                                Items</a>
                            @if ($allowed)
                                <a class="btn btn-default" href="#step4" data-toggle="tab" id="unknownCompliance_tab">Unknown
                                    Compliance</a>
                            @endif
                        </div>

                        <div id="step1" class="">
                            <div class="p-m">
                                <div class="row">
                                    @if(config('simplifya.AUDIT_REPORT_CLASSIC_VIEW') == 1)
                                        {{-- new template goes here --}}
                                        @if($info['status'] == 'Finalized')
                                            <div class="col-xs-12 m-b">
                                                <div class="col-xs-12 alert-info pdf-info">
                                                    <div class="col-md-3"><label>Download Audit Report</label></div>
                                                    <div class="col-md-7"><input type="text" name="pdf_password" class="form-control" id="pdf_password" placeholder="Create a password"></div>
                                                    <div class="col-md-2"><input type="button" value="Download PDF" id="pdf_download" class="btn btn-success btn-block" ></div>
                                                    <div class="col-xs-12 no-padding">
                                                        <div class="col-md-3"></div>
                                                        <div class="col-md-7"><span class="small">Audit Reports can be downloaded as a password-protected PDF file. To download, you must first create a password. Passwords must be a minimum of 8 characters and contain at least: one lowercase character, one uppercase character, and one number.</div></span> </div>

                                                    <div class="col-md-2"></div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-xs-12 new-layout-test">
                                            <div class="summery-box  clearfix n-l">
                                                <div class="col-xs-12 no-padding">
                                                        <div class="col-xs-12 row-field no-padding">
                                                            <div class="n-l col-xs-3"><strong>Business Name :</strong></div>
                                                            <div class="n-l col-xs-9">{{ $info['mjb'] }}</div>
                                                        </div>
                                                    <div class="col-xs-12 row-field no-padding">
                                                            <div class="n-l col-xs-3"><strong>Location :</strong></div>
                                                            <div class="n-l col-xs-9">{{ $info['location'] }}</div>
                                                        </div>
                                                    <div class="col-xs-12 row-field no-padding">
                                                            <div class="n-l col-xs-3"><strong>Audit Type :</strong></div>
                                                            <div class="n-l col-xs-9">
                                                                @if (isset($info['audit_type']))
                                                                    @if ($info['audit_type'] == 'In-house')
                                                                        {{"Self-Audit"}}
                                                                    @elseif (($info['audit_type'] == '3rd Party'))
                                                                        {{"Third Party Audit"}}
                                                                    @else
                                                                        {{ $info['audit_type'] }}
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>

                                                    <div class="col-xs-12 row-field no-padding">
                                                            <div class="n-l col-xs-3"><strong>Auditor Name :</strong></div>
                                                            <div class="n-l col-xs-9">
                                                                {{ $info['inspector'] }}
                                                                @if (isset($info['cc']))
                                                                , {{ $info['cc'] }}
                                                                @endif
                                                            </div>
                                                        </div>

                                                    <div class="col-xs-12 row-field no-padding">
                                                            <div class="n-l col-xs-3"><strong>Audit Date :</strong></div>
                                                            <div class="n-l col-xs-9">{{ $info['date_time'] }}</div>
                                                        </div>

                                                    <div class="col-xs-12 row-field no-padding">
                                                            <div class="n-l col-xs-3"><strong>Audit Report Status :</strong></div>
                                                            <div class="n-l col-xs-9">{{ $info['status'] }}</div>
                                                        </div>
                                                    <div class="col-xs-12 row-field no-padding">
                                                            <?php $licence_list = $info['licence_names']; ?>
                                                            <div class="n-l col-xs-3"><strong>License(s) Audited :</strong></div>
                                                            <div class="n-l col-xs-9"><ul>@foreach ( $licence_list as $single_licence)<li>
                                                                            {{$single_licence['name']}} - ( {{$single_licence['licence_number']}} )
                                                                        </li>
                                                                    @endforeach </ul></div>
                                                        </div>
                                                </div>
                                            </div>
                                            {{--<div class="form-group col-lg-12">--}}
                                            {{--<label>Audit Type</label>--}}
                                            {{--<input type="text" value="{{ $info['audit_type'] }}" id="audit_type"--}}
                                            {{--class="form-control" name="audit_type" placeholder="Audit Type"--}}
                                            {{--disabled>--}}
                                            {{--</div>--}}

                                        </div>
                                    @else
                                        {{-- old template goes here --}}
                                        <div class="col-xs-12">
                                            <div class="summery-box  clearfix p-m">
                                                <div class="col-xs-12 col-lg-6">
                                                    <div class="row">
                                                        <div class="col-xs-12 table-row">
                                                            <b>Marijuana Business Name</b> <span>: {{ $info['mjb'] }}</span>
                                                        </div>
                                                    </div>{{--/ row--}}
                                                    <div class="row">
                                                        <div class="col-xs-12 table-row">
                                                            <b>Location</b> <span>: {{ $info['location'] }}</span>
                                                        </div>
                                                    </div>{{--/ row--}}
                                                    <div class="row">
                                                        <div class="col-xs-12 table-row">
                                                            <b>Auditing Party</b> <span>: {{ $info['cc'] }}</span>
                                                        </div>
                                                    </div>{{--/ row--}}

                                                </div>
                                                <div class="col-xs-12 col-lg-6">
                                                    <div class="row">
                                                        <div class="col-xs-12 table-row ">
                                                            <b>Auditor Name </b>
                                                            <span>: {{ $info['inspector'] }}</span>
                                                        </div>
                                                    </div>{{--/ row--}}
                                                    <div class="row">
                                                        <div class="col-xs-12 table-row ">
                                                            <b>Date & Time</b>
                                                            <span>: {{ $info['date_time'] }}</span>
                                                        </div>
                                                    </div>{{--/ row--}}
                                                    <div class="row">
                                                        <div class="col-xs-12 table-row ">
                                                            <b>Audit Report Status</b>
                                                            <span>: {{ $info['status'] }}</span>
                                                        </div>
                                                    </div>{{--/ row--}}
                                                    {{--/ row--}}
                                                </div>
                                                <div class="form-group col-lg-12">
                                                    <div class="row">
                                                        <div class="col-xs-12 m-t"><div class="col-xs-12 license-border">
                                                                <?php $licence_list = $info['licence_names']; ?>
                                                                <div class="col-md-3 col-xs-12 license-name"><b>License(s) Audited</b></div>
                                                                <div class="col-md-9 col-xs-12 text-left"><ul class="license">@foreach ( $licence_list as $single_licence)<li>
                                                                            {{$single_licence['name']}} - ( {{$single_licence['licence_number']}} )
                                                                        </li>
                                                                        @endforeach </ul></div>
                                                            </div></div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{--<div class="form-group col-lg-12">--}}
                                            {{--<label>Audit Type</label>--}}
                                            {{--<input type="text" value="{{ $info['audit_type'] }}" id="audit_type"--}}
                                            {{--class="form-control" name="audit_type" placeholder="Audit Type"--}}
                                            {{--disabled>--}}
                                            {{--</div>--}}

                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                        <div class="tab-content">
                            @if ($allowed)
                            <div id="step2" class="p-m tab-pane active" ng-controller="questionsList"
                                 ng-init="appmnt_id='{{ $info['id'] }}'" ng-cloak="">
                                @else
                                <div id="step2" class="p-m tab-pane" ng-controller="questionsList"
                                 ng-init="appmnt_id='{{ $info['id'] }}'" ng-cloak="">
                                @endif
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="alert alert-info">
                                            <div class="col-xs-9">
                                                Compliant : <strong>@{{compliantCount.length}}</strong> | Non-Compliant
                                                : <strong>@{{nonCompliantCount.length}}</strong> | Unknown Compliance :
                                                <strong>@{{unknownCompliantCount.length}}</strong>
                                            </div>
                                            <div class="col-xs-3 text-right">
                                                <strong>Compliance
                                                    Rate: @{{getCategoryWiseComplianceRate(compliantCount.length, compliantCount.length,nonCompliantCount.length,unknownCompliantCount.length) |number:0}}
                                                    %</strong>
                                            </div>
                                            <br clear="all">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="p-lg">
                                            <input type="hidden" name="appointment_id" id="appointment_id"
                                                   value="{{ $info['id'] }}"/>
                                            <div class="list-group" id="category_listing">
                                                <div class="list-group" id="category_listing">
                                                    <a class="list-group-item" ng-click="categoryFilter('')"
                                                       ng-class="{active : activeMenu === ''}">
                                                        All
                                                        <strong class="pull-right">@{{ getAllComplianceRate()|number:0}} %</strong>
                                                    </a>
                                                    <a class="list-group-item col-md-12"
                                                       ng-repeat="category in questionCategories | orderBy: 'name'"
                                                       ng-click="categoryFilter(category.id)"
                                                       ng-class="{active : activeMenu === category.id}">

                                                        <div class="col-md-8 no-padding">@{{ category.name }}</div>
                                                        <div class="col-md-4 no-padding"><strong class="pull-right">@{{getComplianceRate(category.id)|number:0}} %</strong></div>

                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div>
                                            <div id="questions_log" class="col-md-12 m-t">
                                                <div class="checkbox text-right">
                                                    <label>
                                                        <input type="checkbox" ng-model="expandAccordion" ng-change="changeExpand(expandAccordion)"> Expand
                                                    </label>
                                                </div>
                                                {{--<div class="col-md-12 list-group-item m-t"--}}
                                                     {{--ng-repeat="question in questions"--}}
                                                     {{--ng-include="'questionTree'"></div>--}}

                                                <uib-accordion class="" close-others="false">
                                                    <uib-accordion-group is-open="question.open" ng-repeat="question in questions">
                                                        <uib-accordion-heading style="width: 100%;display: block">
                                                            <p class="heading_title" ng-class="{'heading_title_91': (question.comment || question.images.length)}">
                                                                @{{ question.level }}. @{{ question.question }}
                                                                <span style="position: absolute;right: 24px;top:-4px;" ng-show="question.comment || question.images.length">
	                                                                <i class="fa fa fa-camera out-circle circle2" aria-hidden="true" ng-show="question.images.length"></i>
                                                                    <i class="fa fa fa-comment out-circle circle1" aria-hidden="true" ng-show="question.comment"></i>

	                                                            </span>
                                                            </p>
                                                            <a class="heading_status" ng-attr-title="@{{question.answer_value_name}}" compliance-state value="@{{question.answer_value_name}}"></a>
                                                        </uib-accordion-heading>

                                                        {{--<div ng-repeat="citation in question.citations">--}}
                                                            <div class="col-md-12" style="font-family: sans-serif;font-style: italic">@{{question.citation_list}}</div>
                                                        {{--</div>--}}

                                                        <div ng-include="'questionTree'"></div>
                                                    </uib-accordion-group>
                                                </uib-accordion>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                    @if ($allowed)
                            <div id="step3" class="p-m tab-pane">
                                        @else
                                <div id="step3" class="p-m tab-pane active">
                                        @endif
                                <div class="row">
                                    <div class="col-lg-12">

                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="p-lg">
                                                    <input type="hidden" name="appointment_id" id="appointment_id"
                                                           value="{{ $info['id'] }}"/>
                                                    <div class="list-group" id="category_listing">
                                                        <div class="list-group" id="category_listings">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="action-item-content" class="col-lg-9">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="step4" class="p-m tab-pane" ng-controller="unknownComplianceCtrl"
                                 ng-init="appmnt_id='{{ $info['id'] }}'" ng-cloak="">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="p-lg">
                                            <input type="hidden" name="appointment_id" id="appointment_id"
                                                   value="{{ $info['id'] }}"/>
                                            <div class="list-group" id="category_listing">
                                                <div class="list-group" id="category_listing">
                                                    <a class="list-group-item" ng-click="categoryFilter('')"
                                                       ng-class="{active : activeMenu === ''}">
                                                        All
                                                    </a>
                                                    <a class="list-group-item"
                                                       ng-repeat="category in questionCategories | orderBy: 'name'"
                                                       ng-click="categoryFilter(category.id)"
                                                       ng-class="{active : activeMenu === category.id}">
                                                        @{{ category.name }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div id="unknownCompliance_log" class="col-md-12 m-t" ng-if="questions.length > 0">
                                                {{--<div class="col-md-12 list-group-item m-t"--}}
                                                     {{--ng-repeat="question in questions" ng-include="'questionTree'">--}}
                                                {{--</div>--}}
                                                <div class="checkbox text-right">
                                                    <label>
                                                        <input type="checkbox" ng-model="expandAccordion" ng-change="changeExpand(expandAccordion)"> Expand
                                                    </label>
                                                </div>
                                                <uib-accordion class="" close-others="false">
                                                    <uib-accordion-group is-open="question.open" ng-repeat="question in questions">
                                                        <uib-accordion-heading style="width: 100%;display: block">
                                                            <p class="heading_title" ng-class="{'heading_title_91': (question.comment || question.images.length)}">
                                                                @{{ question.level }}. @{{ question.question }}
                                                                <span style="position: absolute;right: 24px;top:0;" ng-show="question.comment || question.images.length">
	                                                                <i class="fa fa-comment-o" aria-hidden="true" ng-show="question.comment"></i>
	                                                                <i class="fa fa-picture-o" aria-hidden="true" ng-show="question.images.length"></i>
	                                                            </span>
                                                            </p>
                                                            <a class="heading_status" ng-attr-title="@{{question.answer_value_name}}" compliance-state value="@{{question.answer_value_name}}"></a>
                                                        </uib-accordion-heading>
                                                        <div ng-include="'questionTree'"></div>
                                                    </uib-accordion-group>
                                                </uib-accordion>
                                            </div>
                                            <div id="unknownCompliance_log"  ng-if="questions.length == 0">
                                                <div class="p-lg">No unknown compliance questions</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--<div class="row" ng-if="questions.length == 0">--}}
                                    {{--<div class="col-lg-12">--}}
                                        {{--<div class="p-lg">No unknown compliance questions</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            </div>

                            <script type="text/ng-template" id="group-template.html">
                                <div class="panel @{{panelClass || 'panel-default'}}">
                                    <div class="panel-heading">
                                        <h4 class="panel-title" style="color:#fa39c3">
                                            <a href tabindex="0" class="accordion-toggle" ng-click="toggleOpen()">
                                                <span ng-class="{'text-muted': isDisabled}" uib-accordion-transclude="heading">@{{heading}}</span>
                                                <span uib-accordion-transclude="state">@{{state}}</span>
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" uib-collapse="!isOpen">
                                        <div class="panel-body" style="text-align: right" ng-transclude></div>
                                    </div>
                                </div>
                            </script>

                            <script type="text/ng-template" id="questionTree">
                                <!--<div class="col-md-1">
                                    <div>
                                    <h4>Q@{{ question.question_id }}.</h4>
                                    </div>
                                </div>-->
                                <div class="col-md-12">
                                    <!--<p>Category: @{{ question.category_name }}</p>-->
                                    <!--<p class="report_heading_text">@{{ (question.option_value == '1') ? question.question : question.explanation }}</p>>-->
                                    <p class="report_heading_text" ng-show="question.treeview == undefined || question.treeview != '0'">@{{ question.level }}. @{{ question.question }}</p>

                                    <span ng-if="question.answer_value_name === 'Non-Compliant'">
                                            <p class="report_answer_text"><strong
                                                        style="color: red;">@{{question.answer_value_name}}</strong></p>
                                        </span>
                                    <span ng-if="question.answer_value_name !== 'Non-Compliant'">
                                            <p class="report_answer_text">
                                                <strong>@{{question.answer_value_name}}</strong></p>
                                        </span>
                                    <div>
                                        <h5 ng-if="question.action_items.length"><strong>Action Items</strong></h5>
                                        <ul class="act-items">
                                            <li ng-repeat="action_item in question.action_items" class="answer">@{{action_item.name}}</li>
                                        </ul>
                                    </div>

                                    {{--<p ng-if="question.report_status == '3' && question.comment != ''">--}}
                                    <p>

                                        <strong ng-if="!(question.comment == '' && question.report_status == '3')">Auditor's Notes:</strong>
                                        <span ng-show="question.report_status == '3'" class="answer-@{{question.question_id}}">@{{question.comment}}</span>
                                        {{--<button type="button" class="btn btn-xs btn-default"--}}
                                                {{--ng-if="question.report_status != '3'"--}}
                                                {{--ng-click="addQuestionComment(question.question_id, appmnt_id)"--}}
                                                {{--title="@{{(question.comment == '') ? 'Add' : 'Edit' }} Comment"><i--}}
                                                    {{--class="fa @{{(question.comment == '') ? 'fa-comment' : 'fa-pencil-square-o' }}"--}}
                                                    {{--aria-hidden="true"></i></button>--}}
                                        <span ng-if="question.report_status != '3'" ng-init="question.tempComment=question.comment"
                                                inline-edit="question.tempComment"
                                                inline-edit-textarea=""
                                                inline-edit-btn-edit="Edit Note"
                                                inline-edit-btn-save="Save"
                                                inline-edit-btn-cancel="Cancel"
                                                {{--inline-edit-validation="myValidator(question.tempComment)"--}}
                                                inline-edit-on-blur="cancel"
                                                inline-edit-on-click=""
                                                inline-edit-placeholder="Add Auditor's Note"
                                                inline-edit-callback="saveNoteListener(question)"
                                        ></span>
                                    </p>

                                    <div class="question_image_list">
                                        <div class="actionItemImage"
                                             ng-repeat="img_url in question.images track by $index"><a
                                                    class="fancybox-button-cstm"
                                                    rel="fancybox-button-@{{question.question_id}}" href="@{{img_url}}"
                                                    data-title="Image"><img src="@{{img_url}}" width="150" height="150"
                                                                            style="margin: 5px; padding: 5px;"></a>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="col-md-12 list-group-item"
                                             ng-repeat="question in question.questions"
                                             ng-include="'questionTree'"></div>
                                    </div>
                                </div>
                            </script>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- include all models --}}
    @include('report.editQuestionCommentModel')
    @include('report.addQuestionCommentModel')
    @include('report.assignUserModel')
    @include('report.editActionItemCommentModel')

            <div class="modal fade" id="commentModel" tabindex="-1" role="dialog" aria-hidden="true">
                <form id="versionCommentForm">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="color-line"></div>
                            <div class="modal-header text-center">
                                <h4 class="modal-title"> Add Comment </h4>
                            </div>
                            <div class="modal-body col-sm-12">
                                <textarea id="comment" name="comment" placeholder="comment" rows="5" style="width: 100%;"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default close_window" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="comment_model">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

    {!! Html::script('js/reports/reports.js') !!}
    {!! Html::script('js/reports/questions.js') !!}
@stop