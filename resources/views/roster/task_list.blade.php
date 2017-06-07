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
    <div class="content" ng-controller="rosterTaskList">
        <div class="row">
            <div class="col-md-12" >
                <div class="hpanel">
                    <h5><strong>Create Task</strong></h5>
                    <div class="panel-body taskList no-padding">
                    <input type="hidden" ng-model="rosterId">
                    <form name="rosterTaskAssignForm" novalidate id="roster-task-form" ng-submit="submitTask(rosterTaskAssignForm.$valid)">

                            <row class="col-md-12 no-padding row-1">
                                <span class="col-md-10 col-sm-10 no-padding">
                                    <input size="20" name="task" class="form-control" autocomplete="off" valid ng-model="taskName" value="" placeholder="Task Name" type="text" required>
                                    <div ng-messages="rosterTaskAssignForm.task.$error" ng-if="rosterTaskAssignForm.$submitted || !rosterTaskAssignForm.task.$pristine">
                                            <p ng-message="required">Checklist Task is required.</p>
                                    </div>
                                </span>
                                <span class="col-md-2 col-sm-2">
                                    <button type="submit" id="addTask" class="btn btn-info pull-left" ng-disabled="savingRosterTask">Add New Task</button>
                                    </span>
                            </row>
                            <br><br>
                            <hr>
                    <h5><strong>Task List</strong></h5>
                            <row class="col-md-12 no-padding row-2" ng-repeat="task in tasks">
                                <span class="col-md-10 col-sm-10 no-padding">
                                    <label size="20" class="form-control valid">@{{ task.name }}</label>
                                </span>
                                <span class="col-md-2 col-sm-2"><a href="#" id="remScnt" ng-click="deleteTask(task.id)" class="btn btn-danger remScnt">Remove</a></span>
                            </row>

                    </form>

                    </div>
                </div>
                <div class="col-md-12 no-padding">
                <div class="hpanel">
                <h5><strong>Assignee List</strong></h5>
                    <div class="panel-body">

                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-condensed question-category-table" id="roster-table">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <a href="#" ng-click="sortTypeAssignee = 'name'; sortReverseAssignee = !sortReverseAssignee">
                                                        <span class="tb-sort-header-name">
                                                            Employee Name
                                                        </span>
                                                        <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeAssignee == 'name' && !sortReverseAssignee" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeAssignee == 'name' && sortReverseAssignee" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeAssignee != 'name'" class="fa fa-sort"></i>
                                                        </span>
                                                    </a>
                                                </th>
                                                <th>
                                                    <a href="#" ng-click="sortTypeAssignee = 'start_date_timestamp'; sortReverseAssignee = !sortReverseAssignee">
                                                        <span class="tb-sort-header-name">
                                                            Start Date
                                                        </span>
                                                        <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeAssignee == 'start_date_timestamp' && !sortReverseAssignee" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeAssignee == 'start_date_timestamp' && sortReverseAssignee" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeAssignee != 'start_date_timestamp'" class="fa fa-sort"></i>
                                                        </span>
                                                    </a>
                                                </th>
                                                <th>
                                                    <a href="#" ng-click="sortTypeAssignee = 'end_date_timestamp'; sortReverseAssignee = !sortReverseAssignee">
                                                        <span class="tb-sort-header-name">
                                                            End Date
                                                        </span>
                                                        <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeAssignee == 'end_date_timestamp' && !sortReverseAssignee" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeAssignee == 'end_date_timestamp' && sortReverseAssignee" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeAssignee != 'end_date_timestamp'" class="fa fa-sort"></i>
                                                        </span>
                                                    </a>
                                                </th>
                                                <th>
                                                    <a href="#" ng-click="sortTypeAssignee = 'due_date_timestamp'; sortReverseAssignee = !sortReverseAssignee">
                                                        <span class="tb-sort-header-name">
                                                            Due Date
                                                        </span>
                                                        <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeAssignee == 'due_date_timestamp' && !sortReverseAssignee" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeAssignee == 'due_date_timestamp' && sortReverseAssignee" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeAssignee != 'due_date_timestamp'" class="fa fa-sort"></i>
                                                        </span>
                                                    </a>
                                                </th>
                                                <th>
                                                    <a href="#" ng-click="sortTypeAssignee = 'due_date_timestamp'; sortReverseAssignee = !sortReverseAssignee">
                                                        <span class="tb-sort-header-name">
                                                            Frequency
                                                        </span>
                                                        <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeAssignee == 'frequency' && !sortReverseAssignee" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeAssignee == 'frequency' && sortReverseAssignee" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeAssignee != 'frequency'" class="fa fa-sort"></i>
                                                        </span>
                                                    </a>
                                                </th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr dir-paginate="rosterAssignee in rosterAssignees|orderBy:sortTypeAssignee:sortReverseAssignee|filter:search|itemsPerPage:10" pagination-id="assignee">
                                                <td style="">@{{rosterAssignee.name}}</td>
                                                <td style="">@{{rosterAssignee.start_date}}</td>
                                                <td style="">@{{rosterAssignee.end_date}}</td>
                                                <td style="">@{{rosterAssignee.due_date}}</td>
                                                <td style="">@{{rosterAssignee.frequency}}</td>
                                                <td style=""><a title="edit" href="#"  ng-click="showAssignee(rosterAssignee.id)" class="btn btn-circle btn-success"><i class="fa fa-paste"></i></a></td>
                                            </tr>
                                            <tr ng-show="!rosterAssignees.length">
                                                <td colspan="7" class="dataTables_empty" valign="top" style="text-align: center;">No Any Assignee</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <dir-pagination-controls
                                                pagination-id="assignee"
                                                max-size="5"
                                                direction-links="true"
                                                boundary-links="true" style="float: right" >
                                        </dir-pagination-controls>
                                    </div>

                        </div>
                    </div>
                </div>
                    </div>
                <div class="col-md-12 no-padding">
                <div class="hpanel">
                    <h5><strong>Checklist job History</strong></h5>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-condensed question-category-table" id="roster-table">
                                    <thead>
                                    <tr>
                                        <th>
                                            <a href="#" ng-click="sortTypeJobs = 'name'; sortReverseJobs = !sortReverseJobs">
                                                <span class="tb-sort-header-name">
                                                          Employee Name
                                                </span>
                                                <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeJobs == 'name' && !sortReverseJobs" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeJobs == 'name' && sortReverseJobs" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeJobs != 'name'" class="fa fa-sort"></i>
                                                </span>
                                            </a>

                                        </th>

                                        <th>
                                            <a href="#" ng-click="sortTypeJobs = 'date_timestamp'; sortReverseJobs = !sortReverseJobs">
                                                <span class="tb-sort-header-name">
                                                          Created On
                                                </span>
                                                <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeJobs == 'date_timestamp' && !sortReverseJobs" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeJobs == 'date_timestamp' && sortReverseJobs" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeJobs != 'date_timestamp'" class="fa fa-sort"></i>
                                                </span>
                                            </a>
                                        </th>
                                        <th>
                                            <a href="#" ng-click="sortTypeJobs = 'status'; sortReverseJobs = !sortReverseJobs">
                                                <span class="tb-sort-header-name">
                                                          Status
                                                </span>
                                                <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeJobs == 'status' && !sortReverseJobs" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeJobs == 'status' && sortReverseJobs" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeJobs != 'status'" class="fa fa-sort"></i>
                                                </span>
                                            </a>
                                        </th>
                                        <th>
                                            <a href="#" ng-click="sortTypeJobs = 'rosterTaskCompletinPercentage'; sortReverseJobs = !sortReverseJobs">
                                                <span class="tb-sort-header-name">
                                                          Progress
                                                </span>
                                                <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeJobs == 'rosterTaskCompletinPercentage' && !sortReverseJobs" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeJobs == 'rosterTaskCompletinPercentage' && sortReverseJobs" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeJobs != 'rosterTaskCompletinPercentage'" class="fa fa-sort"></i>
                                                </span>
                                            </a>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr dir-paginate="rosterJob in rosterJobs|orderBy:sortTypeJobs:sortReverseJobs|filter:search|itemsPerPage:10" pagination-id="jobs">
                                        <td style="">@{{rosterJob.name}}</td>
                                        <td style="">@{{rosterJob.date}}</td>
                                        <td style="">@{{rosterJob.status}}</td>
                                        <td style=""><span ng-if="rosterJob.rosterTaskCompletinPercentage==0">Not Performed</span><uib-progressbar ng-if="rosterJob.rosterTaskCompletinPercentage!=0 && rosterJob.rosterTaskCompletinPercentage!=100" animate="true" style="width: 80%; float: left" class="progress-striped progress-angular active" value="rosterJob.rosterTaskCompletinPercentage" type="warning"><b>@{{rosterJob.rosterTaskCompletinPercentage}}%</b></uib-progressbar><uib-progressbar ng-if="rosterJob.rosterTaskCompletinPercentage==100"  animate="true" style="width: 80%; float: left" class="progress-angular active" value="rosterJob.rosterTaskCompletinPercentage" type="success"><b>@{{rosterJob.rosterTaskCompletinPercentage}}%</b></uib-progressbar><span ng-show="rosterJob.rosterTaskCompletinPercentage!=0" style="width: 20%;float: right;color: #3498db;">&nbsp;&nbsp;@{{rosterJob.rosterTaskCompleteCount}}/@{{rosterJob.rosterTaskCount}}</span></td>
                                        <td style=""><a href="#"title="edit" ng-click="showJobs(rosterJob.id,rosterJob.rosterId,rosterJob.status)" class="btn btn-circle btn-success"><i class="fa fa-paste"></i></a></td>

                                    </tr>
                                    <tr ng-show="!rosterJobs.length">
                                        <td colspan="7" class="dataTables_empty" valign="top" style="text-align: center;">No Any Jobs</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <dir-pagination-controls
                                        pagination-id="jobs"
                                        auto-hide="false"
                                        max-size="5"
                                        direction-links="true"
                                        boundary-links="true" style="float: right" >
                                </dir-pagination-controls>
                            </div>
                        </div>
                    </div>
                </div>
                    </div>
            </div>
        </div>
        <div class="row">

        </div>
        <script type="text/ng-template" id="rosterAssignment.html">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Update Assignment</h4>
            </div>
            <div class="modal-body">
                <form name="assign_rosters" novalidate="novalidate" id="assign_rosters" action="#" method="post" class="ng-pristine ng-valid">
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Employee </label>
                                <div class="col-sm-8">
                                    <input type="text" ng-model="form.userName" autocomplete="off" disabled class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Frequency </label>
                                <div class="col-sm-8">
                                    <select class="form-control" ng-model="form.selectedFrequency" disabled ng-options="frequency.value as frequency.name for frequency in frequecies"></select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Start Date </label>
                                <div class="col-sm-8">
                                    <p class="input-group">
                                        <input type="text" disabled class="form-control" cs-date-to-iso uib-datepicker-popup="@{{format}}" ng-model="form.dtStart" is-open="popup1.opened" datepicker-options="dateOptions"  close-text="Close" alt-input-formats="altInputFormats" />
                                        <span class="input-group-btn">
                                         <button type="button" disabled class="btn btn-default" ng-click="open1()"><i class="glyphicon glyphicon-calendar"></i></button>
                                    </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">End Date </label>
                                <div class="col-sm-8">
                                    <p class="input-group">
                                        <input type="text" class="form-control" min-date="minDate" cs-date-to-iso uib-datepicker-popup="@{{format}}" ng-model="form.dtEnd" is-open="popup2.opened" datepicker-options="dateOptions"  close-text="Close" alt-input-formats="altInputFormats" />
                                        <span class="input-group-btn">
                                         <button type="button" class="btn btn-default" ng-click="open2()"><i class="glyphicon glyphicon-calendar"></i></button>
                                    </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" ng-click="update(form.id)">Update</button>
                <button class="btn btn-warning" ng-click="cancel()">Cancel</button>
            </div>
        </script>

        <script type="text/ng-template" id="modelJobs.html">
            <div class="color-line"></div>

            <div class='modal-content'>
                <div class="modal-header">
                    <h4 class="modal-title">Task List</h4>
                </div>

                <div class="modal-body">
                    <form name="save_tasks" class="form-horizontal" novalidate="novalidate" id="save_tasks" action="#" method="post" class="ng-pristine ng-valid">

                        <row class="" ng-repeat="userTask in userTasks">
                            <div class="form-group">
                                <label class="control-label col-sm-8" size="20" style="text-align: left">@{{ userTask.name }}</label>
                                <div class="col-sm-4">
                                    <input type="checkbox" ng-true-value="'1'" ng-false-value="'0'" class="" ng-model="userTask.status">
                                </div>
                            </div>
                        </row>

                    </form>
                </div>
                <div class="modal-footer">
                    <div style="float: left;" ng-show="userGroup!='2'">
                        <button ng-show="jobStatus=='Pending'"class="btn btn-primary" ng-click="save()">Save</button>
                        <button ng-show="jobStatus=='Pending'" class="btn btn-success" ng-click="complete()">Save & Complete</button>
                    </div>
                    <button class="btn btn-warning" ng-click="cancel()">Cancel</button>
                </div>
            </div>
        </script>
    </div>

@stop
@section('scripts')
    {!! Html::script('/js/angular/rosters.js') !!}
@stop