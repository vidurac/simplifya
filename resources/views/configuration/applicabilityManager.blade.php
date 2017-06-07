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
    <div class="content" ng-controller="ApplicabilityCtrl">

        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div><a href="/configuration/applicability/new" class="btn btn-info" title="Add New referrer">Add New Applicability</a></div>
                                <div class="col-sm-5 text-right"></div>
                            </div>
                        </div>
                        <div class="row" ng-show="applicabilities.length == 0">
                            <div class="col-md-12">
                                No applicabilites found.
                            </div>
                        </div>
                        <div class="row" ng-show="applicabilities.length > 0" ng-cloak="">
                            <div class="col-md-12">
                                <div class="search-field">
                                    <div class="col-md-2" style="padding-left: 0px">
                                        <input placeholder="Search by name" ng-model="search.name" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text">
                                    </div>
                                    <div class="col-md-2 no-padding">
                                        <input placeholder="Search by country" ng-model="search.country" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text">
                                    </div>
                                    <div class="col-md-3 " >
                                        <input placeholder="Search by type" ng-model="search.types" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed city-table" id="applicability-table">
                                        <thead>
                                        <tr>
                                            <th>
                                                <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Name
                                                   </span>
                                                    <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'name'" class="fa fa-sort"></i>
                                                   </span>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="#" ng-click="sortType = 'country'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Country
                                                   </span>
                                                    <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'country' && !sortReverse" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'country' && sortReverse" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'country'" class="fa fa-sort"></i>
                                                   </span>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="#" ng-click="sortType = 'type'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Type
                                                   </span>
                                                    <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'type' && !sortReverse" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'type' && sortReverse" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'type'" class="fa fa-sort"></i>
                                                   </span>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="#" ng-click="sortType = 'group'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Group
                                                   </span>
                                                    <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'group' && !sortReverse" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'group' && sortReverse" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'group'" class="fa fa-sort"></i>
                                                   </span>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="#" ng-click="sortType = 'used'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Actions
                                                   </span>
                                                </a>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr dir-paginate="applicability in applicabilities | orderBy:sortType:sortReverse |filter:search | itemsPerPage: 10">

                                            <td>@{{applicability.name}}</td>
                                            <td>@{{applicability.country}}</td>
                                            <td>@{{applicability.types}}</td>
                                            <td>@{{applicability.groups}}</td>
                                            <td><a class="btn btn-info btn-circle btn-xm" data-toggle="tooltip" title="Edit"  href="/configuration/applicability/edit/@{{applicability.id}}"><i class="fa fa-paste"></i></a>
                                                <a class="btn btn-danger btn-circle btn-xm" ng-click="deleteApplicability(applicability.id)" title="Remove" data-toggle="tooltip">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                <a class="btn btn-success btn-circle btn-xm" ng-if="applicability.status==0" ng-click="changeStatus(applicability.id,1)" title="Activate" data-toggle="tooltip">
                                                    <i class="fa fa-thumbs-o-up"></i>
                                                </a>
                                                <a class="btn btn-warning btn-circle btn-xm" ng-if="applicability.status==1" ng-click="changeStatus(applicability.id,0)" title="Deactivate" data-toggle="tooltip">
                                                    <i class="fa fa-thumbs-o-down"></i>
                                                </a>
                                            </td>
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

    </div>

    {!! Html::script('js/angular/applicabilities.js') !!}
@stop
