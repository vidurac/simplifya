<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/23/2016
 * Time: 2:25 PM
 */
?>

@extends('layout.dashbord')

@section('content')
    <div class="content animate-panel">
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">


                            <input type="hidden" id="company_id" value="{{$company_id}}">
                            <input type="hidden" id="entity_type" value="{{$entity_type}}">
                            <input type="hidden" id="master_user_group_id" value="{{$master_user_group_id}}">
                            <input type="hidden" id="cc_ge_subscription" value="{{$cc_ge_subscription}}">

                        <div id="step2" class="">
                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <div class="col-sm-4">
                                        <button type="button" class="btn btn-info" id="new-business-location" data-toggle="modal" data-target="#add-new-business-location">New Business Location</button>
                                    </div>
                                    <div class="col-sm-6">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="hpanel">

                                        <div class="panel-body">
                                            <div class="table-responsive business_location">
                                                <table cellpadding="1" cellspacing="1" class="table table-striped" id="business_location_tbl">
                                                    <thead>
                                                    <tr>
                                                        <th>Location Name</th>
                                                        <th>Address </th>
                                                        <th>City</th>
                                                        <th>State</th>
                                                        <th>Zip Code</th>
                                                        <th>Country</th>
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

        </div>

    </div>

    <div class="modal fade in" id="edit-business-location" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Edit Business Location</h4>
                    <small class="font-bold"></small>
                </div>
                <form name="business_location_form_edit"  id="business_location_form_edit" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Location Name*</label>
                                <div class="col-sm-8">
                                    <input type="hidden" id="edit_location_id" value="" name="edit_location_id">
                                    <input class="form-control" type="text" placeholder="Name " id="edit_name_of_location" name="edit_name_of_location" maxlength="50">
                                    @if ($errors->has('name_of_location'))<label class="error">{!!$errors->first('name_of_location')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Address*</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Address Line 1 " id="edit_add_line_1" name="edit_add_line_1">
                                    @if ($errors->has('add_line_1'))<label class="error">{!!$errors->first('add_line_1')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Address Line 2 " id="edit_add_line_2" name="edit_add_line_2">
                                    @if ($errors->has('add_line_2'))<label class="error">{!!$errors->first('add_line_2')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Country*</label>
                                <div class="col-sm-8">
                                    <select class="form-control country" name="edit_country" id="edit_country">
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->id}}">{{$country->name}}</option>
                                        @endforeach

                                    </select>
                                </div>
                                @if ($errors->has('edit_country'))<label class="error">{!!$errors->first('edit_country')!!}</label>@endif

                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">State*</label>
                                <div class="col-sm-8">
                                    <select class="form-control state" name="edit_state" id="edit_state">
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                                @if ($errors->has('edit_state'))<label class="error">{!!$errors->first('edit_state')!!}</label>@endif

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">City*</label>
                                <div class="col-sm-8">
                                    <select class="form-control cities" name="edit_cities" id="edit_cities" >
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                                @if ($errors->has('edit_cty'))<label class="error">{!!$errors->first('edit_city')!!}</label>@endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Zip Code*</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Zip Code" name="edit_zip_code" id="edit_zip_code">
                                    @if ($errors->has('edit_zip_code'))<label class="error">{!!$errors->first('edit_zip_code')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Phone Number*</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Phone Number" name="edit_phone_no" id="edit_phone_no">
                                    @if ($errors->has('edit_'))<label class="error">{!!$errors->first('edit_')!!}</label>@endif
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default cls-edit-busin-locat-form" data-dismiss="modal" >Close</button>
                        <button type="button" class="btn btn-primary save-business-changes" >Save changes</button>
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
                                    <input class="form-control" type="text" placeholder="Name" name="edit_name" id="edit_name">
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
                                    <p id="editPermissionDescription_invite" style="margin-left: 1%; font-size: 12px; color: #0000FF;"></p>
                                </div>
                                @if ($errors->has('edit_permission_level'))<label class="error">{!!$errors->first('edit_permission_level')!!}</label>@endif

                            </div>
                        </div>
                        <div class="row edit_location_enable">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Location(s)*</label>
                                <div class="col-sm-8">
                                    <select name="edit_locations" class="form-control padding0" id="edit_locations" multiple="multiple">

                                    </select>
                                </div>
                                @if ($errors->has('edit_'))<label class="error">{!!$errors->first('edit_')!!}</label>@endif

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default cls-edit-user-form" data-dismiss="modal" >Close</button>
                        <button type="button" class="btn btn-primary save-user-changes" >Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="add-new-business-location" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Add Business Location</h4>
                    <small class="font-bold"></small>
                </div>
                <form name="business_location_form"  id="business_location_form" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Location Name*</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Name " id="name_of_location" name="name_of_location">
                                    @if ($errors->has('name_of_location'))<label class="error">{!!$errors->first('name_of_location')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Address*</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Address Line 1 " id="add_line_1" name="add_line_1">
                                    @if ($errors->has('add_line_1'))<label class="error">{!!$errors->first('add_line_1')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Address Line 2 " id="add_line_2" name="add_line_2">
                                    @if ($errors->has('add_line_2'))<label class="error">{!!$errors->first('add_line_2')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Country*</label>
                                <div class="col-sm-8">
                                    <select class="form-control country" name="country" id="country">
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                            @if($country->id == 1)
                                                <option value="{{$country->id}}" selected>{{$country->name}}</option>
                                            @else
                                                <option value="{{$country->id}}">{{$country->name}}</option>
                                            @endif
                                        @endforeach

                                    </select>
                                </div>
                                @if ($errors->has('country'))<label class="error">{!!$errors->first('country')!!}</label>@endif

                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">State*</label>
                                <div class="col-sm-8">
                                    <select class="form-control state" name="state" id="state">
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                                @if ($errors->has('state'))<label class="error">{!!$errors->first('state')!!}</label>@endif

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">City*</label>
                                <div class="col-sm-8">
                                    <select class="form-control cities" name="cities" id="cities" >
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                                @if ($errors->has('cty'))<label class="error">{!!$errors->first('city')!!}</label>@endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Zip Code*</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Zip Code " name="zip_code" id="zip_code">
                                </div>
                                @if ($errors->has('zip_code'))<label class="error">{!!$errors->first('zip_code')!!}</label>@endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Phone Number*</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Phone Number" name="phone_no" id="phone_no">
                                    @if ($errors->has('email'))<label class="error">{!!$errors->first('password')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <a type="button" class="btn w-xs btn-default" id="cls-location-form">Cancel</a>
                                    <?php if($entity_type != 1){?>
                                    <a type="button" class="btn w-xs btn-primary" id="new-add-location">Save</a>
                                    <?php }else{?>
                                    <a type="button" class="btn w-xs btn-primary" id="new-add-location">Add</a>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="invite-employees" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Invite Employees</h4>
                    <small class="font-bold"></small>
                </div>
                <form name="invite_employ_form" novalidate id="invite_employ_form" action="#" method="post">
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Name* </label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Name" name="name" id="name">
                                    @if ($errors->has('name'))<label class="error">{!!$errors->first('name')!!}</label>@endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Email Address* </label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Email Address" name="email_address" id="email_address">
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
                                    <div id="permissionDescription_invite" style="font-size: 12px; color: #0000FF;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="locations-enable">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Location*</label>
                                <div class="col-sm-8">
                                    <select name="location" class="form-control padding0" id="location" multiple="multiple">

                                    </select>
                                </div>
                            </div>
                        </div>

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
    {!! Html::script('js/company/company-registration.js') !!}
    {!! Html::script('js/company/edit-company-prof.js') !!}
@stop
