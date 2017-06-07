<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/27/2016
 * Time: 3:19 PM
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
                            <div class="col-md-12">
                                <div class="form-group text-left">
                                    <button class="btn btn-info" id="syncMailChimip">Sync With MailChimp</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <form id="eventForm" class="form-horizontal">
                                <div class="col-lg-12">
                                    <div class="col-md-5">
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

                                    <div class="col-md-5">
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

                                </div>
                            </form>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-hover" id="mailChimpTable">
                                        <thead>
                                        <tr>
                                            <th></th>
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

    {!! Html::script('/js/mailChimp/mailChimp.js') !!}


    <div class="modal fade" id="mailChimpCynicModel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header text-center">
                    <h4 class="modal-title">Sync With MailChimp</h4>
                    <small class="font-bold"></small>
                </div>
                <div class="modal-body">
                    <label class="col-sm-4 control-label" style="margin-top: 1%">Select MailChimp List</label>
                    <div class="col-sm-5">
                        <select class="form-control" id="mailChimpList">
                            <option value="">Select List</option>
                        </select>
                    </div>
                    <br/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="create_new_version_model">Save</button>
                </div>
            </div>
        </div>
    </div>

@stop