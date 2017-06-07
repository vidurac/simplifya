<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/16/2016
 * Time: 3:23 PM
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
                                <input type="hidden" id="entityType" value="{{$type}}">
                                <div class="col-lg-12">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="dateContainer col-xs-12">
                                                <div class="input-group input-append date" id="startDatePicker">
                                                    <input type="text" class="form-control" name="startDate" id="startDate" placeholder="Start Date" />
                                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="dateContainer col-xs-12">
                                                <div class="input-group input-append date" id="endDatePicker">
                                                    <input type="text" class="form-control" name="endDate" id="endDate" placeholder="End Date" />
                                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                                <span id="date_err"></span>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" id="audit_type">
                                                    <option value="">Audit Type</option>
                                                    <option value="1">Self-audit</option>
                                                    <option value="2">3rd Party</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" id="status">
                                                    <option value="">Status</option>
                                                    <option value="0">Pending</option>
                                                    <option value="1">Synced</option>
                                                    <option value="3">Finalized</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <a name="user-search" id="inspection-search" class="btn btn-default"><i class="fa fa-search"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 pull-right">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <a onclick="downloadInspectionReport('{{url('inspection/report/export')}}')" name="download_csv" id="download_csv" class="btn btn-default pull-right"><i class="fa fa-download" aria-hidden="true"></i> Export CSV </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-hover" id="report-detail-table">
                                        <thead>
                                        <tr>
                                            <th>Audit Date</th>
                                            <th>Audit Time</th>
                                            <th>MJ Business Name</th>
                                            <th>Audit Party</th>
                                            <th>Audit Type</th>
                                            <th>Duration</th>
                                            <th>Status</th>
                                            <th>Apt.Status</th>
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

    {!! Html::script('/js/reports/inspection-report.js') !!}
@stop
