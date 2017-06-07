<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 6/6/2016
 * Time: 2:05 PM
 */
?>

@extends('layout.dashbord')
@section('content')
    <div class="content" ng-controller="rosterList">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group text-left">
                            <button class="btn btn-info" id="new-license-btn" ng-click="createRoster()">Create New Checklist</button>
                        </div>
                    </div>
                </div>
                <div class="hpanel">
                    <div class="panel-body">

                        <div class="row">



                        </div>
                        <div id="business_location_tbl_filter" class="dataTables_filter" style="float: right"><label style="color: #333">Search:<input class="roster_search" style="margin-left: 5px;" ng-model="search.name" placeholder="" aria-controls="business_location_tbl" ng-model-options="{ debounce: 500 }" type="search"></label></div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed question-category-table" id="roster-table">
                                        <thead>
                                        <tr>
                                            <th>Checklist Name</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <tr ng-repeat="roster in rosters|filter:search as loadSection">
                                            <td style="width:60%">@{{roster.name}}</td>
                                            <td style="width:40%"><a class="btn btn-info btn-circle" title="View" href="/roster/list/task/@{{roster.id}}"><i class="fa fa-eye"></i></a>&nbsp;<a class='btn btn-success btn-circle' title='Assign' ng-hide="roster.task_count=='0'" ng-click="assignRoster(roster.id,roster.name)"><i class="fa fa-user-plus"></i></a> <a href="#"  ng-click="deleteRoster(roster.id)" title='Delete' class="btn btn-danger btn-circle remScnt"><i class="fa fa-trash-o"></i></a></td>
                                        </tr>
                                        <tr ng-show="!loadSection.length">
                                            <td colspan="2"  style="text-align: center;">No Any Checklist</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">

        </div>

    </div>
    <script type="text/ng-template" id="myModalContent.html">
        <div class="color-line"></div>
        <form name="assignRosters" novalidate="novalidate" id="assign_rosters">
        <div class="modal-header">
            <h4 class="modal-title">Assign an Employee to @{{name}}</h4>
        </div>
        <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Employee </label>
                            <div class="col-sm-8">
                                <select class="form-control" ng-model="form.userId"  ng-options="user.value as user.name for user in users">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Frequency </label>
                            <div class="col-sm-8">
                                <select class="form-control" ng-model="form.selectedFrequency" ng-options="frequency.value as frequency.name for frequency in frequecies"></select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Start Date </label>
                            <div class="col-sm-8">
                                <p class="input-group">
                                    <input type="text" name="startDate" class="form-control" valid min-date="minDate" cs-date-to-iso uib-datepicker-popup="@{{format}}" ng-model="form.dtStart" is-open="popup1.opened" datepicker-options="dateOptions"  close-text="Close" alt-input-formats="altInputFormats" required />

                                     <span class="input-group-btn">
                                         <button type="button" class="btn btn-default" ng-click="open1()"><i class="glyphicon glyphicon-calendar"></i></button>
                                    </span>
                                    <div ng-messages="assignRosters.startDate.$error" ng-if="assignRosters.$submitted || !assignRosters.startDate.$pristine">
                                        <p ng-message="required">Start Date is required.</p>
                                    </div>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">End Date </label>
                            <div class="col-sm-8">
                                <p class="input-group">
                                    <input type="text" class="form-control" valid min-date="minDatePop2" cs-date-to-iso uib-datepicker-popup="@{{format}}" name="endDate" ng-model="form.dtEnd" is-open="popup2.opened" datepicker-options="dateOptions"  close-text="Close" alt-input-formats="altInputFormats" required />

                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" ng-click="open2()"><i class="glyphicon glyphicon-calendar"></i></button>
                                    </span>
                                    <div ng-messages="assignRosters.endDate.$error" ng-if="assignRosters.$submitted || !assignRosters.endDate.$pristine">
                                        <p ng-message="required">End Date is required.</p>
                                    </div>
                                </p>
                            </div>
                        </div>
                    </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="submit" ng-click="ok(assignRosters.$valid)" ng-disabled="savingRosterAssign">Assign</button>
            <button class="btn btn-warning" ng-click="cancel()">Cancel</button>
        </div>
        </form>
    </script>
    <script type="text/ng-template" id="rosterModalContent.html">
        <div class="color-line"></div>
        <form name="rosterTaskForm" novalidate="novalidate" id="roster-task-form">
        <div class="modal-header">
            <h4 class="modal-title">Create a Checklist</h4>
        </div>
        <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Checklist Name </label>
                            <div class="col-sm-8">
                                    {{--<input type="text" name="checkListName" class="form-control" valid  ng-model="form.checkListName"  required />--}}
                            <input size="20" name="roster" autocomplete="off" class="form-control" valid ng-model="form.rosterName" ng-pattern="/^[a-zA-Z ]*$/" value="" placeholder="Checklist Name" type="text" ng-disabled="savingRoster" required>
                            <div ng-messages="rosterTaskForm.roster.$error" ng-if="rosterTaskForm.$submitted || !rosterTaskForm.roster.$pristine">
                                <p ng-message="required">Checklist name is required.</p>
                                <p ng-message="pattern">Checklist name cannot contain numbers or special characters.</p>
                            </div>
                            </div>
                        </div>
                    </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="submit" ng-click="submitRoster(rosterTaskForm.$valid)" ng-disabled="form.savingRoster">Add</button>
            <button class="btn btn-warning" ng-click="cancel()">Cancel</button>
        </div>
        </form>
    </script>

@stop
@section('scripts')
    {!! Html::script('/js/angular/rosters.js') !!}
@stop
