<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/16/2016
 * Time: 3:57 PM
 */
        ?>
@extends('layout.dashbord')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                    {{--<h3>Inspection Request Manager</h3>--}}
                </div>
                <div class="hpanel">
                    <div class="panel-body">

                        <div class="row">
                            <form id="eventForm" class="form-horizontal">
                                <div class="col-lg-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <input type="text" placeholder="Search by user name" id="user_name" name="user_name" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" name="mailChimpEntityType" id="mailChimpEntityType">
                                                    <option value="">Entity Type</option>
                                                    @foreach($entityTypes as $entityType)
                                                        <option value="{{$entityType->id}}">{{$entityType->name}}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" name="mailChimpCompanyList" id="mailChimpCompanyList">
                                                    <option value="">Company List</option>
                                                    @foreach($companies as $company)
                                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <a name="user-search" id="mailChimpSearch" class="btn btn-default"><i class="fa fa-search"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 pull-right">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <a onclick="downloadCompanyUsers('{{url('company/users/export')}}')" name="download_csv" id="download_csv" class="btn btn-default pull-right"><i class="fa fa-download" aria-hidden="true"></i> Export CSV </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-hover" id="mailChimpTable">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>User Group</th>
                                            <th>Company</th>
                                            <th>Entity Type</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Html::script('/js/reports/userList.js') !!}

@stop
