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
    <div class="content" ng-controller="ReferralCtrl">

        <div class="row" ng-init="mjbEntityType=<?php echo $MJB_entity_type?>;init()">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div><a href="/configuration/referrals/new" class="btn btn-info" title="Add New referrer">Add New Referrer</a></div>
                                <div class="col-sm-5 text-right"></div>
                            </div>
                        </div>
                        <div class="row" ng-show="referrals.length == 0">
                            <div class="col-md-12">
                                No referrers found.
                            </div>
                        </div>
                        <div class="row" ng-show="referrals.length > 0" ng-cloak="">
                            <div class="col-md-12">
                                <div class="search-field">
                                    <div class="col-md-2" style="padding-left: 0px">
                                        <input placeholder="Search by name" ng-model="search.name" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text">
                                    </div>
                                    <div class="col-md-2 no-padding">
                                        <input placeholder="Search by email" ng-model="search.email" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text">
                                    </div>
                                    <div class="col-md-3 " >
                                        <input placeholder="Search by type" ng-model="search.type" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed city-table" id="referral-table">
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
                                                <a href="#" ng-click="sortType = 'email'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Email
                                                   </span>
                                                    <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'email' && !sortReverse" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'email' && sortReverse" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'email'" class="fa fa-sort"></i>
                                                   </span>
                                                </a>
                                            </th>
                                            {{--<th>
                                                <a href="#" ng-click="sortType = 'commission_rates'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Commission Rates
                                                   </span>
                                                                                                    <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'commission_rates' && !sortReverse" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'commission_rates' && sortReverse" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'commission_rates'" class="fa fa-sort"></i>
                                                   </span>
                                                </a>
                                            </th>--}}
                                            <th>
                                                <a href="#" ng-click="sortType = 'used'; sortReverse = !sortReverse">
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
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr dir-paginate="referral in referrals | orderBy:sortType:sortReverse |filter:search | itemsPerPage: 10">

                                            <td>@{{referral.name}}</td>
                                            <td>@{{referral.email}}</td>
                                            {{--<td>@{{referral.commission_rates}}</td>--}}
                                            <td>@{{referral.type}}</td>
                                            <td><a class="btn btn-info btn-circle btn-xm" data-toggle="tooltip" title="View"  href="/configuration/referrals/view/@{{referral.id}}"><i class="fa fa-eye"></i></a>
                                                <a class="btn btn-info btn-circle btn-xm" data-toggle="tooltip" title="Edit"  href="/configuration/referrals/edit/@{{referral.id}}"><i class="fa fa-paste"></i></a>
                                                <a class="btn btn-danger btn-circle btn-xm" ng-click="deleteReferral(referral.id)" title="Remove" data-toggle="tooltip">
                                                    <i class="fa fa-trash"></i>
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

    {!! Html::script('js/configuration/city.js') !!}
    {!! Html::script('js/angular/coupons.js') !!}
@stop
