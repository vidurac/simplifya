<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/31/2016
 * Time: 10:27 PM
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
                                    <!--div class="col-md-2"></div-->
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
                                            <th></th>
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

    <div class="modal fade in" id="company-details-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Company Details</h4>
                    <small class="font-bold"></small>
                </div>
                <div class="panel-body">
                    <div class="text-center m-b-md" id="wizardControl">

                        <a class="btn btn-primary" href="#step1" data-toggle="tab" id="basic_info_tab">Basic Info</a>
                        <a class="btn btn-default" href="#step2" data-toggle="tab" id="business_location_tab">Business Locations</a>
                        <a class="btn btn-default license-show" href="#step3" data-toggle="tab" id="license_tab" >Licenses</a>
                        <a class="btn btn-default" href="#step4" data-toggle="tab" id="employee_tab">Employees</a>

                    </div>

                    <div class="tab-content">
                        <div id="step1" class="p-m tab-pane active">
                            <div class="row">
                                <div class="form-group col-lg-10 col-sm-offset-2">
                                    <label class="col-sm-4 control-label">Entity Type</label>
                                    <div class="col-sm-6">
                                        <input name="company_entity_type" id="company_entity_type" class="form-control" type="text" placeholder="Entity Type" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-10 col-sm-offset-2">
                                    <label class="col-sm-4 control-label">Name of Business / Entity</label>
                                    <div class="col-sm-6">
                                        <input class="form-control" type="text" placeholder="Name of Business / Entity " id="name_of_business" value="" name="name_of_business" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-10 col-sm-offset-2">
                                    <label class="col-sm-4 control-label">FEIN</label>
                                    <div class="col-sm-6">
                                        <input class="form-control" type="text" placeholder="FEIN " id="company_registration_no"  value="" name="company_registration_no" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row foc-update-section">
                                <div class="form-group col-lg-10 col-sm-offset-2">
                                    <label class="col-sm-4 control-label">Free Of Charge</label>
                                    <div class="col-sm-6">
                                            <label><input type="radio" id="foc_update_enable" name="foc_active" class="foc-update" value="1"> Yes </label>
                                            <label><input type="radio" id="foc_update_disable" name="foc_active" class="foc-update" value="0" > No </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="step2" class="p-m tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="hpanel">
                                        <div class="panel-heading hbuilt">
                                            <div class="panel-tools">
                                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                            </div>
                                            Business Locations
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table cellpadding="1" cellspacing="1" class="table table-bordered table-striped" id="business_location_tbl">
                                                    <thead>
                                                    <tr>
                                                        <th>Location Name</th>
                                                        <th>Address</th>
                                                        <th>Phone Number</th>
                                                        {{--<th>Country</th>--}}
                                                        <th>State</th>
                                                        <th>City</th>
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
                        <div id="step3" class="tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="hpanel">
                                        <div class="panel-heading hbuilt">
                                            <div class="panel-tools">
                                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                            </div>
                                            Licenses
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table cellpadding="1" cellspacing="1" class="table table-bordered table-striped" id="licenses_table">
                                                    <thead>
                                                    <tr>
                                                        <th>Location Name</th>
                                                        <th>License Type</th>
                                                        <th>License No</th>
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
                        <div id="step4" class="p-m tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="hpanel">
                                        <div class="panel-heading hbuilt">
                                            <div class="panel-tools">
                                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                            </div>
                                            Employees
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table cellpadding="1" cellspacing="1" class="table table-bordered table-striped" id="employe-table">
                                                    <thead>
                                                    <tr>
                                                        {{--<th>Title</th>--}}
                                                        <th>Name</th>
                                                        <th>Email Address</th>
                                                        <th>Location</th>
                                                        <th>Permission</th>
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
                <div class="modal-footer btn-footer">
                    <button type="button" class="btn btn-primary pull-right" data-dismiss="modal" id="suspend-company" style="display: none; margin: 3px;">Suspend</button>
                    <button type="button" class="btn btn-success pull-right" data-dismiss="modal" id="approve-company" style="display: none; margin: 3px;">Approve</button>
                    <button type="button" class="btn btn-danger pull-right" id="reject-company" style="display: none; margin: 3px;">Reject</button>
                </div>
            </div>
        </div>
    </div>

    {!! Html::script('/js/company/company-manager.js') !!}
    {!! Html::script('/js/company/export-company-manager.js') !!}
@stop
