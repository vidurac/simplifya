<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/16/2016
 * Time: 2:40 PM
 */
?>
@extends('layout.dashbord')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">

                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <form id="eventForm" class="form-horizontal">
                                <div class="col-lg-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <input type="text"class="form-control" placeholder="Search By Business Name" id="business_name" name="business_name">
                                                <input type="hidden" value="{{Auth::User()->company_id}}" id="company_id">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" name="entity_type" id="entity_type">
                                                    <option value="">Select Entity Type</option>
                                                    @foreach ($entities as $entity)
                                                        <option value={{$entity->id}}>{{$entity->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" name="status" id="status">
                                                    <option value="">Status</option>
                                                    <option value="0">In-progress</option>
                                                    <option value="1">Pending</option>
                                                    <option value="2">Active</option>
                                                    <option value="3">Reject</option>
                                                    <option value="4">Inactive</option>
                                                    <option value="5">Expire</option>
                                                    <option value="6">Suspend</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <a name="company_search" id="company_search" class="btn btn-default"><i class="fa fa-search"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1 pull-right">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <a onclick="downloadCompanyReport('{{url('company/export')}}')" name="download_csv" id="download_csv" class="btn btn-default pull-right"><i class="fa fa-download" aria-hidden="true"></i> Export CSV </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-hover" id="company-manager-table">
                                        <thead>
                                        <tr>
                                            <th>Business Name</th>
                                            <th>Entity Type</th>
                                            <th>Date</th>
                                            <th>Status</th>
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

    {!! Html::script('/js/reports/report-type-manager.js') !!}
    {!! Html::script('/js/company/export-company-manager.js') !!}
@stop