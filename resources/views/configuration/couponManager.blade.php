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
    <div class="content" ng-controller="CouponCtrl">

        <div class="row" ng-init="mjbEntityType=<?php echo $MJB_entity_type?>;init()">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div><a href="/configuration/coupons/new" class="btn btn-info">Create New Discount Code</a></div>
                                <div class="col-sm-5 text-right"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="search-field">
                                    <div class="col-md-2" style="padding-left: 0px"><input placeholder="Search by discount code" ng-model="search.code" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text"></div>

                                    <div class="col-md-2 no-padding">

                                        <select class="form-control" ng-model="search.master_subscription_id" ng-change="checkPlanIdSelection()" ng-options="plan.id as plan.name for plan in subscription_plans"> <option value="">Search by plan</option> </select>
                                    </div>

                                    {{--<div class="col-md-3 no-padding"><p class="input-group"><input placeholder="Search by Start Date" type="text" ng-click="open3()" class="form-control input-sm" cs-date-to-iso uib-datepicker-popup="@{{format}}" ng-model="search.start_date" is-open="popup3.opened"   close-text="Close" alt-input-formats="altInputFormats" /><span class="input-group-btn">
                                         <button type="button"  class="input-sm btn btn-default" ng-click="open3()"><i class="glyphicon glyphicon-calendar"></i></button>
                                    </span></p></div>
                                    <div class="col-md-3"><p class="input-group"><input ng-model="search.end_date" placeholder="Search by End Date" type="text" ng-click="open4()" class="form-control input-sm" cs-date-to-iso uib-datepicker-popup="@{{format}}"  is-open="popup4.opened" close-text="Close" alt-input-formats="altInputFormats" /><span class="input-group-btn">
                                         <button type="button"  class="btn btn-default input-sm" ng-click="open4()"><i class="glyphicon glyphicon-calendar"></i></button>
                                    </span></p></div>--}}
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed city-table" id="coupon-table">
                                        <thead>
                                        <tr>
                                            <th>
                                                <a href="#" ng-click="sortType = 'code'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Discount Code
                                                   </span>
                                                    <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'code' && !sortReverse" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'code' && sortReverse" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'code'" class="fa fa-sort"></i>
                                                   </span>
                                                </a>
                                            </th>
                                            <th>
                                                <a href="#" ng-click="sortType = 'master_subscription_name'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Plan
                                                   </span>
                                                    <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'master_subscription_name' && !sortReverse" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'master_subscription_name' && sortReverse" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'master_subscription_name'" class="fa fa-sort"></i>
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
                                                <a href="#" ng-click="sortType = 'used'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Availability
                                                   </span>
                                                    <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'used' && !sortReverse" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'used' && sortReverse" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'used'" class="fa fa-sort"></i>
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
                                        <tr dir-paginate="coupon in coupons | orderBy:sortType:sortReverse |filter:search | itemsPerPage: 10">

                                            <td>@{{coupon.code}}</td>
                                            <td>@{{coupon.master_subscription_name}}</td>
                                            <td>@{{coupon.start_date}}</td>
                                            <td>@{{coupon.end_date}}</td>
                                            <td ng-show="coupon.used == 0"><span class='badge badge-success'>Available</span></td>
                                            <td ng-show="coupon.used == 1"><span class='badge badge-danger'>Not Available</span></td>
                                            <td><a class="btn btn-info btn-circle btn-xm" data-toggle="tooltip" title="Edit"  href="/configuration/coupons/edit/@{{coupon.id}}"><i class="fa fa-paste"></i></a></td>
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

    {!! Html::script('js/configuration/city.js') !!}
    {!! Html::script('js/angular/coupons.js') !!}
@stop
