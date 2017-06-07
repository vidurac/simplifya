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
    <div class="content" ng-controller="rosterAssignees">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="search-field">
                                    <div class="col-md-2" style="padding-left: 0px"><input placeholder="Search by name" autocomplete="off" ng-model="search.name" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text"></div>
                                    <div class="col-md-2 no-padding"><input placeholder="Search by Checklist Name" autocomplete="off" ng-model="search.roster_name" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text"></div>
                                    <div class="col-md-2"><input placeholder="Search by Frequency" autocomplete="off" ng-model="search.frequency" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text"></div>
                                    <div class="col-md-3 no-padding"><p class="input-group"><input placeholder="Search by Start Date" autocomplete="off" type="text" ng-click="open3()" class="form-control input-sm" cs-date-to-iso uib-datepicker-popup="@{{format}}" ng-model="search.start_date" is-open="popup3.opened"   close-text="Close" alt-input-formats="altInputFormats" /><span class="input-group-btn">
                                         <button type="button"  class="input-sm btn btn-default" ng-click="open3()"><i class="glyphicon glyphicon-calendar"></i></button>
                                    </span></p></div>
                                    <div class="col-md-3"><p class="input-group"><input ng-model="search.end_date" placeholder="Search by End Date" type="text" ng-click="open4()" class="form-control input-sm" cs-date-to-iso uib-datepicker-popup="@{{format}}"  is-open="popup4.opened" close-text="Close" alt-input-formats="altInputFormats" /><span class="input-group-btn">
                                         <button type="button"  class="btn btn-default input-sm" ng-click="open4()"><i class="glyphicon glyphicon-calendar"></i></button>
                                    </span></p></div>
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
                                                <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                                    <span class="tb-sort-header-name">Employee Name</span>
                                                    <span class="tb-sort-icons">
                                                        <i ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortType != 'name'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="#" ng-click="sortType = 'roster_name'; sortReverse = !sortReverse">
                                                    <span class="tb-sort-header-name">Checklist Name</span>
                                                    <span class="tb-sort-icons">
                                                        <i ng-show="sortType == 'roster_name' && !sortReverse" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortType == 'roster_name' && sortReverse" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortType != 'roster_name'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="#" ng-click="sortType = 'start_date_timestamp'; sortReverse = !sortReverse">
                                                    <span class="tb-sort-header-name">
                                                        Start Date
                                                    </span>
                                                    <span class="tb-sort-icons">
                                                        <i ng-show="sortType == 'start_date_timestamp' && !sortReverse" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortType == 'start_date_timestamp' && sortReverse" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortType != 'start_date_timestamp'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="#" ng-click="sortType = 'end_date_timestamp'; sortReverse = !sortReverse">
                                                    <span class="tb-sort-header-name">
                                                        End Date
                                                    </span>
                                                    <span class="tb-sort-icons">
                                                        <i ng-show="sortType == 'end_date_timestamp' && !sortReverse" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortType == 'end_date_timestamp' && sortReverse" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortType != 'end_date_timestamp'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="#" ng-click="sortType = 'due_date_timestamp'; sortReverse = !sortReverse">
                                                    <span class="tb-sort-header-name">
                                                        Due Date
                                                    </span>
                                                    <span class="tb-sort-icons">
                                                        <i ng-show="sortType == 'due_date_timestamp' && !sortReverse" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortType == 'due_date_timestamp' && sortReverse" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortType != 'due_date_timestamp'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="#" ng-click="sortType = 'frequency'; sortReverse = !sortReverse">
                                                    <span class="tb-sort-header-name">
                                                        Frequency
                                                    </span>
                                                    <span class="tb-sort-icons">
                                                        <i ng-show="sortType == 'frequency' && !sortReverse" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortType == 'frequency' && sortReverse" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortType != 'frequency'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr dir-paginate="rosterAssignee in rosterAssignees|orderBy:sortType:sortReverse|filter:search|itemsPerPage:10 as loadSection">
                                            <td style="">@{{rosterAssignee.name}}</td>
                                            <td style="">@{{rosterAssignee.roster_name}}</td>
                                            <td style="">@{{rosterAssignee.start_date}}</td>
                                            <td style="">@{{rosterAssignee.end_date}}</td>
                                            <td style="">@{{rosterAssignee.due_date}}</td>
                                            <td style="">@{{rosterAssignee.frequency}}</td>
                                            <td style=""><a title="edit" href="#"  ng-click="showAssignee(rosterAssignee.id)" class="btn btn-circle btn-success"><i class="fa fa-paste"></i></a></td>
                                        </tr>
                                        <tr ng-show="!loadSection.length">
                                            <td colspan="7" class="dataTables_empty" valign="top" style="text-align: center;">No Any Assignee</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <dir-pagination-controls
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
                                    <input type="text" autocomplete="off" ng-model="form.userName" disabled class="form-control" />
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
    </div>

@stop
@section('scripts')
    {!! Html::script('/js/angular/rosters.js') !!}
@stop
