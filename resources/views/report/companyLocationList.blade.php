<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/16/2016
 * Time: 5:30 PM
 */?>
@extends('layout.dashbord')

@section('content')
    <div class="content fadeIn">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <a class="small-header-action" href="">
                            <div class="clip-header">
                                <i class="fa fa-arrow-up"></i>
                            </div>
                        </a>

                        <div class="row">
                            <form id="reportForm" class="form-horizontal">
                                <div class="col-lg-12">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <input type="text" name="zip_name_phone_no" id="zip_name_phone_no" class="form-control" placeholder="business name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" id="entity_type" placeholder="Select Entity Type" >
                                                    <option value="">Select Entity Type</option>
                                                    @foreach($entities as $option)
                                                        <option value="{{$option->id}}"> {{$option->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" id="country" placeholder="Select Country">
                                                    <option value="">Country</option>
                                                    @foreach($countries as $option)
                                                        <option value="{{$option->id}}"> {{$option->name}}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" id="state" placeholder="Select State" >
                                                    <option value="">State</option>
                                                    @foreach($states as $option)
                                                        <option value="{{$option->id}}"> {{$option->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" id="city">
                                                    <option value="">City</option>
                                                    @foreach($cities as $option)
                                                        <option value="{{$option->id}}"> {{$option->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <a name="company_search" id="company_search" class="btn btn-default"><i class="fa fa-search"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-12">
                                    <div class="col-md-3 pull-right">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <a onclick="downloadCompanyLocationReport('{{url('company/location/export')}}')" name="download_csv" id="download_csv" class="btn btn-default pull-right"><i class="fa fa-download" aria-hidden="true"></i> Export CSV </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-hover" id="report-company-location-table">
                                        <thead>
                                        <tr>
                                            <th>BUSINESS/ENTITY NAME</th>
                                            <th>LOCATION NAME</th>
                                            <th>CITY</th>
                                            <th>STATE</th>
                                            <th>COUNTRY</th>
                                            <th>PHONE NUMBER</th>
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

    {!! Html::script('/js/reports/company-location-list.js') !!}
    @stop
