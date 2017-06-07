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
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Add your colleagues and employees to your account. You can assign different permission levels for each person.
                </div>
                <div class="hpanel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group text-left">
                                    <button class="btn btn-info" id="new-user-model" data-toggle="modal" data-target="#add-new-employees">Add New User</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <form id="eventForm" class="form-horizontal">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <select class="form-control" name="permission_levels" id="permission_levels">
                                                <option value="">Permission Level</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <input type="text"class="form-control" placeholder="Search By Name" id="user_name" name="user_name">
                                            <input type="hidden" value="{{Auth::User()->company_id}}" id="company_id">
                                            <input type="hidden" value="{{Auth::User()->id}}" id="user_id">
                                            <input type="hidden" value="{{Auth::User()->master_user_group_id}}" id="master_user_group_id">
                                            <input type="hidden" value="{{Session('entity_type')}}" id="entity_type">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <select class="form-control" name="status" id="status">
                                                <option value="">Status</option>
                                                <option value="1" selected>Active</option>
                                                <option value="2">Inactive</option>
                                                <option value="0">Trash</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-1">
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <a name="user-search" id="user-search" class="btn btn-default"><i class="fa fa-search"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-hover" id="users-detail-table">
                                        <thead>
                                        <tr>
                                            {{--<th>#</th>--}}
                                            <th>Name</th>
                                            <th>Email</th>
                                            {{--@if(Session('entity_type') == 2)--}}
                                                <th>Location</th>
                                            {{--@endif--}}
                                            <th>User Role</th>
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
    {!! Html::script('/js/Users/users.js') !!}


    <div class="modal fade in" id="add-new-employees" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Add New Users</h4>
                    <small class="font-bold"></small>
                </div>
                <form name="invite_employ_form" novalidate id="invite_employ_form" action="#" method="post">
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Name* </label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Name" name="name" id="name" autocomplete="off">
                                    @if ($errors->has('name'))<label class="error">{!!$errors->first('name')!!}</label>@endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Email Address* </label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Email Address" name="email_address" id="email_address" >
                                    @if ($errors->has('email_address'))<label class="error">{!!$errors->first('email_address')!!}</label>@endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Permission Level*</label>
                                <div class="col-sm-8">
                                    <select name="permission_level" class="form-control" id="permission_level">
                                        <option value="">Select Permission level</option>
                                    </select>
                                    <div id="permissionDescription" style="font-size: 12px; color: #0000FF;"></div>
                                </div>
                            </div>
                        </div>
                        @if(Session('entity_type') != 1)
                        <div class="row" id="locations-enable">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Location(s)*</label>
                                <div class="col-sm-8">
                                    <select name="location" class="form-control padding0" id="location" multiple="multiple">

                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <button type="button" class="btn w-xs btn-default" id="cls-emp-form">Cancel</button>
                                    <button type="button" class="btn w-xs btn-primary" id="invite_employee_modl">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="edit-user-details" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Edit User Details</h4>
                    <small class="font-bold"></small>
                </div>
                <form name="user_details_form_edit"  id="user_details_form_edit">
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Name*</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Name" name="edit_name" id="edit_name" autocomplete="off">
                                    @if ($errors->has('name'))<label class="error">{!!$errors->first('name')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Email Address</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Email Address" name="edit_email_address" id="edit_email_address" readonly>
                                    @if ($errors->has('email_address'))<label class="error">{!!$errors->first('email_address')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Permission Level*</label>
                                <div class="col-sm-8">
                                    <select name="edit_permission_level" class="form-control" id="edit_permission_level">
                                        <option value="">Select Permission level</option>
                                    </select>
                                    <p id="editPermissionDescription" style="margin-left: 1%; font-size: 12px; color: #0000FF;"></p>
                                </div>
                                @if ($errors->has('edit_permission_level'))<label class="error">{!!$errors->first('edit_permission_level')!!}</label>@endif

                            </div>
                        </div>
                        @if(Session('entity_type') != 1)
                        <div class="row edit_location_enable">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Location(s)*</label>
                                <div class="col-sm-8">
                                    <select name="edit_locations" class="form-control location" id="edit_locations" multiple="multiple">

                                    </select>
                                </div>
                                @if ($errors->has('edit_'))<label class="error">{!!$errors->first('edit_')!!}</label>@endif

                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default cls-edit-user-form" data-dismiss="modal" >Cancel</button>
                        <button type="button" class="btn btn-primary save-user-changes" >Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop