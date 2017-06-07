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
    <div class="content" ng-controller="rosterJobs">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="search-field">
                                    <div class="col-md-4" style="padding-left: 0px"><input placeholder="Search by name" autocomplete="off" ng-model="search.name" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text"></div>
                                    <div class="col-md-4"><input placeholder="Search by Checklist Name" autocomplete="off" ng-model="search.rosterName" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text"></div>
                                    {{--<input placeholder="Search by name" ng-model="search.name" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text">--}}
                                </div>
                            </div>
                            <br>
                            <br>
                            <br>
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
                                                <a href="#" ng-click="sortTypeJobs = 'roster_name'; sortReverseJobs = !sortReverseJobs">
                                                <span class="tb-sort-header-name">
                                                          Checklist Name
                                                </span>
                                                    <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeJobs == 'roster_name' && !sortReverseJobs" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeJobs == 'roster_name' && sortReverseJobs" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeJobs != 'roster_name'" class="fa fa-sort"></i>
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
                                        <tr dir-paginate="rosterJob in rosterJobs|orderBy:sortTypeJobs:sortReverseJobs| filter:search|itemsPerPage:10 as loadSection">
                                            <td style="">@{{rosterJob.name}}</td>
                                            <td style="">@{{rosterJob.rosterName}}</td>
                                            <td style="">@{{rosterJob.date}}</td>
                                            <td style="">@{{rosterJob.status}}</td>
                                            <td style=""><span ng-if="rosterJob.rosterTaskCompletinPercentage==0">Not Performed</span><uib-progressbar ng-if="rosterJob.rosterTaskCompletinPercentage!=0 && rosterJob.rosterTaskCompletinPercentage!=100" animate="true" style="width: 80%; float: left" class="progress-striped progress-angular active" value="rosterJob.rosterTaskCompletinPercentage" type="warning"><b>@{{rosterJob.rosterTaskCompletinPercentage}}%</b></uib-progressbar><uib-progressbar ng-if="rosterJob.rosterTaskCompletinPercentage==100"  animate="true" style="width: 80%; float: left" class="progress-angular active" value="rosterJob.rosterTaskCompletinPercentage" type="success"><b>@{{rosterJob.rosterTaskCompletinPercentage}}%</b></uib-progressbar><span ng-show="rosterJob.rosterTaskCompletinPercentage!=0" style="width: 20%;float: right;color: #3498db;">&nbsp;&nbsp;@{{rosterJob.rosterTaskCompleteCount}}/@{{rosterJob.rosterTaskCount}}</span></td>
                                            <td style=""><a href="#" title="edit" ng-click="showJobs(rosterJob.id,rosterJob.rosterId,rosterJob.status)" class="btn btn-circle btn-success"><i class="fa fa-paste"></i></a></td>
                                        </tr>
                                        <tr ng-show="!loadSection.length">
                                            <td colspan="6" class="dataTables_empty" valign="top" style="text-align: center;">No Any Jobs</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <dir-pagination-controls
                                            max-size="5"
                                            direction-links="true"
                                            auto-hide=false
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
