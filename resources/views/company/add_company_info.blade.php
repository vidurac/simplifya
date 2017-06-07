<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/13/2016
 * Time: 3:03 PM
 */
?>
@extends('layout.dashbord')

@section('content')
    <style>
        .accordion-full-width-lbl {
            display: block;
        }
    </style>
    @if($entity_type == 2)
        @if(Session('company_status') == 0)
              <div id="get_start">
                  <div class="content">
                      <div class="row">
                          <div class="col-lg-12">
                              <div class="hpanel">
                             <div class="panel-body text-center">
                                 <div class="inner-container">
                        <h2>Welcome to Simplifya!</h2>
                                     <span class="col-md-10 col-md-offset-1">
                        <span class="intro"><p>Setting up your cannabis business on Simplifya is easy. Before you can start checking your compliance, you’ll need to fill out some basic information about your business(es).</p>
                        <span class="center-block"><p>Once you have completed the set-up process, all features of Simplifya will be available to you.</p></span>
                            </span>
                        <button id="get_start_view" class="btn btn-orange">Get Started</button>
                                 </span>
                                     </div>
                      </div>

                              </div>
                              </div>
                          </div>
                      </div>
            </div>
        @endif
    @endif

<div class="content {{ Session('company_status') == 0 ? 'info_hidden' : '' }}" id="company_info">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                        <div class="text-center m-b-md" id="wizardControl">
                            @if((Session('company_status') != 0 && $entity_type == 2) || ($cc_ge_subscription == 0 && $entity_type != 2 && Session('company_status') == 2) || ( $cc_ge_subscription == 1 && $entity_type != 2 && Session('company_status') != 4 ) || ($cc_ge_subscription == 1 && $entity_type != 2 && Session('company_status') != 5 ))
                                <a class="btn btn-primary"  data-toggle="tab" id="tab_1">Step 1 - Basic Info</a>
                            @endif
                            <input type="hidden" id="entity_status" value="<?php echo $company_detail['company_status'];?>">
                            <input type="hidden" value="{{$cc_ge_subscription}}" name="cc_ge_subscription" id="cc_ge_subscription">
                            @if($entity_type == 2)
                                @if(Session('company_status') == 0)
                                <a class="btn btn-default"  data-toggle="tab" id="tab_2">Step 1 - Business Locations</a>
                                <a class="btn btn-default"  data-toggle="tab" id="tab_3">Step 2 - Licenses</a>
                                <a class="btn btn-default"  data-toggle="tab" id="tab_4">Step 3 - Invite Employees</a>
                                <a class="btn btn-default"  data-toggle="tab" id="tab_5">Step 4 - Make Payment</a>
                                @elseif(Session('company_status') == 5)
                                    <a class="btn btn-default"  data-toggle="tab" id="tab_5">Step 2 - Make Payment</a>
                                @elseif(Session('company_status') == 4)
                                    <a class="btn btn-default"  data-toggle="tab" id="tab_5">Step 2 - Make Payment</a>
                                @endif
                            @elseif($entity_type == 3 || $entity_type == 4)
                                @if($company_detail['company_status'] == 2)
                                    <a class="btn btn-default"  data-toggle="tab" id="tab_2">Step 2 - Business Locations</a>
                                    <a class="btn btn-default"  data-toggle="tab" id="tab_3">Step 3 - Invite Employees</a>
                                    @if($cc_ge_subscription == 0)
                                        <a class="btn btn-default"  data-toggle="tab" id="tab_4">Step 4 - Payment Details</a>@endif
                                @elseif(Session('company_status') == 0)
                                    {{--@if($cc_ge_subscription == 0)
                                        <a class="btn btn-default"  data-toggle="tab" id="tab_5">Step 2 - Payment Details</a>
                                    @endif--}}
                                    @if($cc_ge_subscription == 1)
                                        <a class="btn btn-default"  data-toggle="tab" id="tab_5">Step 2 - Make Payment</a>
                                    @endif
                                        @if($cc_ge_subscription == 0)
                                            <a class="btn btn-default"  data-toggle="tab" id="tab_5">Step 2 - Make Payment</a>
                                        @endif
                                @elseif(Session('company_status') == 5)
                                   {{-- @if($cc_ge_subscription == 0)
                                        <a class="btn btn-default"  data-toggle="tab" id="tab_5">Step 2 - Payment Details</a>
                                    @endif--}}
                                    @if($cc_ge_subscription == 1)
                                        <a class="btn btn-default"  data-toggle="tab" id="tab_5">Step 2 - Make Payment</a>
                                    @endif
                                @elseif(Session('company_status') == 4)
                                    {{--@if($cc_ge_subscription == 0)
                                        <a class="btn btn-default"  data-toggle="tab" id="tab_5">Step 2 - Payment Details</a>
                                    @endif--}}
                                    @if($cc_ge_subscription == 1)
                                        <a class="btn btn-default"  data-toggle="tab" id="tab_5">Step 2 - Make Payment</a>
                                    @endif
                                @endif
                            @endif
                            <input type="hidden" id="company_id" value="{{$company_id}}">
                            <input type="hidden" id="entity_type" value="{{$entity_type}}">
                            <input type="hidden" id="master_user_group_id" value="{{$master_user_group_id}}">
                        </div>

                    @if(Session('company_status') != 0 )
                        <div class="tab-content">
                            <div id="step1" class="p-m tab-pane {{ Session('company_status') != 0 ? 'active' : '' }}">
                                <form name="simpleForm" novalidate id="simpleForm" action="#" method="post">
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Entity Type</label>
                                            <div class="col-sm-6">
                                                <input name="entity_type" id="entity_type" class="form-control" type="text" placeholder="Entity Type" value="{{$company_detail['company_type']}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Name of Business / Entity</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="Name of Business / Entity " id="name_of_business" value="{{$company_detail['company_name']}}" name="name_of_business" readonly>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">FEIN</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="FEIN " id="company_registration_no"  value="********************{{$company_detail['fein_last_digits']}}" name="company_registration_no" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Your Name</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="Your Name" name="your_name" id="your_name" value="{{$company_detail['user_name']}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Email Address</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="email" placeholder="Email Address" name="email" id="email" value="{{$company_detail['user_email']}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right m-t-xs">
                                        @if($cc_ge_subscription == 1 || ($cc_ge_subscription == 1 && Session('company_status') != 4 && $entity_type != 2) || ($cc_ge_subscription == 1 && Session('company_status') != 5 && $entity_type != 2) || $entity_type == 2 || (Session('company_status') == 2  && $entity_type != 2))
                                            <a class="btn btn-primary next" id='next' href="#">Next</a>
                                        @endif
                                        @if((Session('company_status') == 4 && $cc_ge_subscription == 0 && $entity_type != 2) || ( Session('company_status') == 5 && $cc_ge_subscription == 0 && $entity_type != 2))
                                            <button type="button" class="btn btn-primary pull-right" id="active_account" >Activate</button>
                                        @endif
                                    </div>
                            </form>

                            </div>
                            @endif
                            <div id="step2" class="tab-pane {{ Session('company_status') == 0 ? 'active' : '' }}">
                                @if($entity_type == 2)
                                <div class="intro-content col-md-10 col-md-offset-1"><h3>First, you'll need to add the locations of your businesses.</h3></div>
                                @endif
                                    @if($entity_type != 2)
                                        <div class="intro-content col-md-10 col-md-offset-1"><h3>First, you'll need to add the location of your business.</h3></div>
                                    @endif
                                    <form name="business_location_form"  id="business_location_form" >
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Location Name*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="Name " id="name_of_location" name="name_of_location" maxlength="50">
                                                @if ($errors->has('name_of_location'))<label class="error">{!!$errors->first('name_of_location')!!}</label>@endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Address*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="Address Line 1 " id="add_line_1" name="add_line_1">
                                                @if ($errors->has('add_line_1'))<label class="error">{!!$errors->first('add_line_1')!!}</label>@endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label"></label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="Address Line 2 " id="add_line_2" name="add_line_2">
                                                @if ($errors->has('add_line_2'))<label class="error">{!!$errors->first('add_line_2')!!}</label>@endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Country*</label>
                                            <div class="col-sm-6">
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
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">State*</label>
                                            <div class="col-sm-6">
                                                <select class="form-control state" name="state" id="state">
                                                    <option value="">Select State</option>
                                                </select>
                                            </div>
                                            @if ($errors->has('state'))<label class="error">{!!$errors->first('state')!!}</label>@endif

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Citi(es)*</label>
                                            <div class="col-sm-6">
                                                <select class="form-control cities" name="cities" id="cities" >
                                                    <option value="">Select Cities</option>
                                                </select>
                                            </div>
                                            @if ($errors->has('cty'))<label class="error">{!!$errors->first('city')!!}</label>@endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Zip Code*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="Zip Code" name="zip_code" id="zip_code">
                                                @if ($errors->has('zip_code'))<label class="error">{!!$errors->first('zip_code')!!}</label>@endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Phone Number*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="Phone Number" name="phone_no" id="phone_no">
                                                @if ($errors->has('email'))<label class="error">{!!$errors->first('password')!!}</label>@endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-6">
                                                <a type="button" class="btn w-xs btn-default" id="clear-location-form">Clear</a>
                                                <?php if($entity_type != 1){?>
                                                    <a type="button" class="btn w-xs btn-primary" id="add-location">Save</a>
                                                <?php }else{?>
                                                    <a type="button" class="btn w-xs btn-primary" id="add-location">Add</a>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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

                                    <div class="text-right m-t-xs">
                                        @if($entity_type != 2)
                                        <a class="btn btn-default prev" data-target="#myModal5" href="#">Previous</a>
                                        @endif
                                        <a class="btn btn-primary next" href="#">Next</a>
                                    </div>

                            </div>
                            @if($entity_type == 2)
                            <div id="step3" class="tab-pane">
                                <div class="intro-content col-md-10 col-md-offset-1"><h3>Next, add the licenses you hold at each location.</h3>
                                    <p>The licenses you enter here will determine which audit checklists you have access to at each business location. As your business grows or changes, you can add or remove licenses under the License Manager.
                                    </p>
                                </div>
                                <form name="license_location_form"  id="license_location_form" >
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Location* </label>
                                            <div class="col-sm-6">
                                                <select name="company_location" class="form-control" id="company_location" >
                                                    <option value=""> Select Location</option>
                                                </select>
                                                @if ($errors->has('company_location'))<label class="error">{!!$errors->first('company_location')!!}</label>@endif
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">License Type*</label>
                                            <div class="col-sm-6">
                                                <select name="licen_type" id="licen_type" class="form-control" >
                                                    <option value=""> Select Licenses Type</option>
                                                </select>
                                                @if ($errors->has('licen_type'))<label class="error">{!!$errors->first('licen_type')!!}</label>@endif
                                            </div>
                                        </div>
                                    </div>
                                    <div id="display-only-cc-ge" >
                                        <div class="row">
                                            <div class="form-group col-lg-10 col-sm-offset-2">
                                                <label class="col-sm-4 control-label">License Number*</label>
                                                <div class="col-sm-6">
                                                    <input type="text" value="" id="licen_no" name="licen_no" class="form-control" autocomplete="off"  placeholder="License Number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-6">
                                                <button type="button" class="btn w-xs btn-default" id="clear-license">Clear</button>
                                                <button type="button" class="btn w-xs btn-primary" id="add-license">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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

                                <div class="text-right m-t-xs">
                                    <a class="btn btn-default prev" href="#">Previous</a>
                                    <a class="btn btn-primary next" href="#">Next</a>
                                </div>
                            </div>
                            @endif
                            <div id="<?php if($entity_type == 3 || $entity_type == 4){ echo 'step3';}else{echo 'step4';}?>" class="tab-pane">
                                @if($entity_type == 2)
                                <div class="intro-content col-md-10 col-md-offset-1"><h3>Add your staff for each location.</h3>
                                    <p>You are currently the master admin for your account, so you can see all the activity at all of your business locations. You can add other master admins, managers, or employees. Each user type has different permission levels.
                                        If you don’t have time for this now, you can always add and remove staff under the Admin Manager.</p>
                                </div>
                                @endif
                                    @if($entity_type != 2)
                                <div class="intro-content col-md-10 col-md-offset-1"><h3>Add your staff for the location.</h3>
                                    <p>You are currently the master admin for your account, so you can see all the activity at all of your business location. You can add other master admins, or inspectors. Each user type has different permission levels.
                                        If you don’t have time for this now, you can always add and remove staff under the Admin Manager.</p>
                                </div>
                                    @endif
                                <form name="invite_employ_form" novalidate id="invite_employ_form" action="#" method="post">
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Name* </label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="Name" name="name" id="name" autocomplete="off">
                                                @if ($errors->has('name'))<label class="error">{!!$errors->first('name')!!}</label>@endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Email Address* </label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="Email Address" name="email_address" id="email_address">
                                                @if ($errors->has('email_address'))<label class="error">{!!$errors->first('email_address')!!}</label>@endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Permission Level*</label>
                                            <div class="col-sm-6">
                                                <select name="permission_level" class="form-control" id="permission_level">
                                                    <option value="">Select Permission level</option>
                                                </select>
                                                <div id="permissionDescription_invite" style="font-size: 12px; color: #0000FF;"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="locations-enable">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Location(s)*</label>
                                            <div class="col-sm-6">
                                                @if($entity_type == 3 || $entity_type == 4)
                                                    <select name="location" class="form-control padding0" id="location">
                                                        <option value="">Select Business Location</option>
                                                    </select>
                                                @else
                                                    <select name="location" class="form-control padding0" id="location" multiple="multiple">

                                                    </select>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-6">
                                                <button type="button" class="btn w-xs btn-default" id="clear-emp-form">Clear</button>
                                                <button type="button" class="btn w-xs btn-primary" id="invite_to_emp">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="hpanel">
                                                <div class="panel-heading hbuilt">
                                                    <div class="panel-tools">
                                                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                                    </div>
                                                    Invited Employees
                                                </div>
                                                <div class="panel-body">
                                                    <div class="table-responsive">
                                                        <table cellpadding="1" cellspacing="1" class="table table-bordered table-striped" id="employe-table">
                                                            <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Email Address</th>
                                                                <th>Location</th>
                                                                <th>Permission</th>
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

                                <div class="text-right m-t-xs">
                                    @if($entity_type == 2)
                                        <a class="btn btn-default prev" href="#">Previous</a>
                                        <a class="btn btn-primary next" href="#">Next</a>
                                    @else
                                        <a class="btn btn-default prev" href="#">Previous</a>
                                        @if($cc_ge_subscription == 0)
                                            <a class="btn btn-primary next" href="#">Next</a>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div id="<?php if($entity_type == 3 && $cc_ge_subscription == 0 || $entity_type == 4 && $cc_ge_subscription == 0){ echo 'step4';}else{echo 'step5';}?>" class="tab-pane">

                                @if($entity_type == 2)
                                    {{--<div id="subscription_plan_accordion"></div>--}}
                                    {{--<ul id="subs_plans">--}}
                                    {{--</ul>--}}
                                    <h3>Select Subscription Plan</h3>
                                    <div id="subscription_plan_accordion">
                                    </div>

                                    <div class="intro-content col-md-10 col-md-offset-1">

                                    @if($company_detail['foc'] == 0)
                                    <h3>Make your first payment</h3>
                                    <p>The monthly fee to use Simplifya is <span class="fee"></span> per license, charged for each month. This fee allows you to conduct unlimited self-audits of all of your facilities, so you have the peace of mind that your businesses are consistently operating within compliance. The audit reports will provide Action Items, which are solutions to restore compliance in areas of non-compliance.</p>
                                    @endif

                                    </div>

                                    {{--<div id="subs_plans">--}}
                                    {{--</div>--}}
                                    @if($company_detail['foc'] == 0)
                                <div class="payment-summary" id="payment_summary">

                                    <div class="col-md-10 col-md-offset-1">
                                        <div class="row item1">
                                            <div class="col-md-8 col-xs-8">Monthly fee per license:</div> <div class="col-md-4 col-xs-4 text-right value" id="month_fee"></div>
                                        </div>
                                        <div class="row item2">
                                            <div class="col-md-8 col-xs-8">Licenses held by <span id="payment_company_name"></span> Company:</div> <div class="col-md-4 col-xs-4 text-right value" id="no_of_license"></div>
                                        </div>
                                        <div class="row item3">
                                            <div class="col-md-8 col-xs-8">Monthly subscription fee:</div> <div class="col-md-4 col-xs-4 text-right value" id="total_monthly_fee"></div>
                                        </div>

                                        @if($coupon_referral_id == 0)
                                        <div class="row item3 m-t">
                                            <div class="col-md-8 col-xs-8">Referral Code:</div>
                                            <div class="col-md-4 col-xs-4 text-right value" >
                                                <input  type="text" class="form-control coupon" placeholder="Referral Code" name="coupon_code" id="coupon_code">

                                                <input type="hidden" id="coupon_check_status">
                                                <input type="hidden" id="is_referral" value="0">
                                            </div>
                                            <div class="col-md-12 text-right "><span class="coupon_val" id="coupon_check_msg"></span></div>
                                        </div>
                                        @endif

                                        @if($coupon_referral_id > 0)
                                            <div class="row item3 m-t">
                                                <div class="col-md-8 col-xs-8">Referral Code:</div>
                                                <div class="col-md-4 col-xs-4 text-right value" >
                                                    <input  type="text" class="form-control coupon" placeholder="Referral Code" readonly name="referral_code" id="referral_code" value="{{$coupon_code}}">
                                                    <span class="referral_validation" id="referral_check_msg"></span>
                                                    <input type="hidden" id="coupon_check_status" value="valid">
                                                    <input type="hidden" id="coupon_details_id" value="{{$coupon_details_id}}">
                                                    <input type="hidden" id="coupon_amount" value="{{$coupon_amount}}">
                                                    <input type="hidden" id="amount_type" value="{{$amount_type}}">

                                                    <input type="hidden" id="is_referral" value="1">
                                                </div>
                                            </div>
                                        @endif

                                        <div class="row item3" id="discount_div">
                                            <div class="col-md-8 col-xs-8">Discount:</div> <div class="col-md-4 col-xs-4 text-right value" id="discount"></div>
                                        </div>

                                        <div class="row item4">
                                            <div class="col-md-8 col-xs-8">First month’s charge:</div> <div class="col-md-4 col-xs-4 text-right value" id="payment_sub_fee_for_usage"></div>
                                            <input type="hidden" id="payment_sub_fee_for_usageH" value="0">
                                            @if($coupon_referral_id > 0)
                                                <input type="hidden" id="coupon_id" value="{{$coupon_id}}">
                                            @endif
                                            @if($coupon_referral_id == 0)
                                                <input type="hidden" id="coupon_id" value="">
                                            @endif
                                        </div>

                                    </div>

                                </div>
                                    @endif
                                @endif



                                <form name="payment_form" novalidate id="payment_form" action="#" method="post">
                                    @if( (($entity_type != 2 && $cc_ge_subscription == 1 && $company_detail['cc_data_added']  == 1) || ($entity_type == 2 && $company_detail['company_status'] != 0 && ($company_detail['foc'] == 0))))
                                        <div class="row">

                                            <div class="form-group col-lg-10 col-sm-offset-1">
                                                <label class="col-sm-4 control-label"> Card Number</label>
                                                <div class="col-sm-8">
                                                    @if($company_detail['cc_data_added'] == 1)
                                                        <input type="text" class="form-control" id="card_number" name="card_number" readonly>
                                                        <input type="hidden" class="form-control" id="have_card_detail" >
                                                    @else
                                                        <input type="text" class="form-control" id="card_number" name="card_number" readonly>
                                                        <input type="hidden" class="form-control" id="have_card_detail" >
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-lg-10 col-sm-offset-1">
                                                <label class="col-sm-4 control-label">Expiration Month</label>
                                                <div class="col-sm-8">
                                                    @if($company_detail['cc_data_added'] == 1)
                                                        <input type="text" class="form-control" id="exp_month" name="exp_month" readonly>
                                                    @else
                                                        <select name="exp_month" class="form-control" id="exp_month">
                                                            <option value="01">January</option>
                                                            <option value="02">February</option>
                                                            <option value="03">March</option>
                                                            <option value="04">April</option>
                                                            <option value="05">May</option>
                                                            <option value="06">June</option>
                                                            <option value="07">July</option>
                                                            <option value="08">August</option>
                                                            <option value="09">September</option>
                                                            <option value="10">October</option>
                                                            <option value="11">November</option>
                                                            <option value="12">December</option>
                                                        </select>
                                                        @if ($errors->has('exp_month'))<label class="error">{!!$errors->first('exp_month')!!}</label>@endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-lg-10 col-sm-offset-1">
                                                <label class="col-sm-4 control-label">Expiration Year</label>
                                                <div class="col-sm-8">
                                                    @if($company_detail['cc_data_added'] == 1)
                                                        <input type="text"class="form-control" id="exp_year" name="exp_year" readonly>
                                                    @else
                                                        <select name="exp_year" id="exp_year" class="form-control">
                                                            <?php
                                                            $this_year = date('Y', time());
                                                            for ($i = 0; $i < 10; $i++) {
                                                                echo '<option value="' . $this_year . '">' . $this_year . '</option>';
                                                                $this_year++;
                                                            }
                                                            ?>
                                                        </select>
                                                        @if ($errors->has('exp_month'))<label class="error">{!!$errors->first('exp_year')!!}</label>@endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                            @if($entity_type == 2)
                                                <input type="hidden" class="form-control" id="mjb_company_status" value="{{$company_detail['company_status']}}" readonly>
                                                <input type="hidden" class="form-control" name="payment_subscription_fee" id="payment_subscription_fee"readonly>
                                            @endif
                                    @endif

                                    @if($entity_type != 2 && $cc_ge_subscription == 1)
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-1">
                                            <label class="col-sm-4 control-label">Entity Type </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="payment_entity_type" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-1">
                                            <label class="col-sm-4 control-label">Name of Business / Entity</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="payment_business_name" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="no_of_license">
                                        <div class="form-group col-lg-10 col-sm-offset-1">
                                            <label class="col-sm-4 control-label">No. of Licens(es)</label>
                                            <div class="col-sm-8">
                                                <input type="text"class="form-control" id="payment_no_of_license" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="subscription_row" style="display: block">
                                        <div class="form-group col-lg-10 col-sm-offset-1">
                                            <label class="col-sm-4 control-label">Subscription Fee</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="payment_subscription_fee" id="payment_subscription_fee"readonly>
                                                <input type="hidden" id="payment_type" name="payment_type" value="subscription">
                                            </div>
                                        </div>
                                    </div>

                                    @endif
                                    @if((($entity_type == 2 && $company_detail['company_status'] == 0 && $company_detail['foc'] == 0) || ($entity_type != 2 && $company_detail['cc_data_added'] == 0)) )
                                            <div class="form-group col-lg-10 col-sm-offset-1">
                                                    <div class="row">
                                                        <div class="form-group col-lg-12">
                                                            <div class="col-md-12 col-xs-12">
                                                                <ul class="cc_icons pull-right" style="margin-bottom: 0px">
                                                                    <li><img id="cc-amex" src="/images/cards/amex.png" /></li>
                                                                    <li><img id="cc-visa" src="/images/cards/visa.png" /></li>
                                                                    <li><img id="cc-discover" src="/images/cards/discover.png" /></li>
                                                                    <li><img id="cc-mastercard" src="/images/cards/master.png" /></li>
                                                                </ul>
                                                            </div>


                                                            <label class="col-sm-4 control-label">Card Number*</label>
                                                            <div class="col-sm-8">
                                                                <input class="form-control" type="text" placeholder="Card Number" name="card_number" id="card_number" >
                                                                @if ($errors->has('card_number'))<label class="error" id="err-card_number">{!!$errors->first('card_number')!!}</label>@endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-lg-12">
                                                            <label class="col-sm-4 control-label">CCV Number*</label>
                                                            <div class="col-sm-8">
                                                                <input class="form-control" type="text" placeholder="CCV Number" name="ccv_number" id="ccv_number">
                                                                @if ($errors->has('ccv_number'))<label class="error">{!!$errors->first('ccv_number')!!}</label>@endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-lg-12">
                                                            <label class="col-sm-4 control-label">Expiration Month*</label>
                                                            <div class="col-sm-8">
                                                                <select name="exp_month" class="form-control" id="exp_month">
                                                                    <option value="01">January</option>
                                                                    <option value="02">February</option>
                                                                    <option value="03">March</option>
                                                                    <option value="04">April</option>
                                                                    <option value="05">May</option>
                                                                    <option value="06">June</option>
                                                                    <option value="07">July</option>
                                                                    <option value="08">August</option>
                                                                    <option value="09">September</option>
                                                                    <option value="10">October</option>
                                                                    <option value="11">November</option>
                                                                    <option value="12">December</option>
                                                                </select>
                                                                @if ($errors->has('exp_month'))<label class="error">{!!$errors->first('exp_month')!!}</label>@endif
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-lg-12">
                                                            <label class="col-sm-4 control-label">Expiration Year*</label>
                                                            <div class="col-sm-8">
                                                                <select name="exp_year" id="exp_year" class="form-control">
                                                                    <?php
                                                                    $this_year = date('Y', time());
                                                                    for ($i = 0; $i < 10; $i++) {
                                                                        echo '<option value="' . $this_year . '">' . $this_year . '</option>';
                                                                        $this_year++;
                                                                    }
                                                                    ?>
                                                                </select>
                                                                @if ($errors->has('exp_month'))<label class="error">{!!$errors->first('exp_year')!!}</label>@endif
                                                            </div>
                                                        </div>

                                                    </div>
                                            </div>

                                            <div class="row" id="term_condition" style="display: block">
                                                <div class="form-group col-lg-10 col-sm-offset-1 border-top terms">
                                                    <div class="checkbox checkbox-single checkbox-success col-md-12">
                                                        <label><input type="checkbox" name="terms" value="1"> I have read & agree to the <a target="_blank" href="http://simplifya.com/terms-of-service/"><u>Terms of Service</u></a></label>
                                                        <br><span id="err-terms"></span>
                                                    </div>
                                                </div>
                                            </div>

                                    @endif

                                    <input type="hidden" class="form-control" id="mjb_company_status" value="{{$company_detail['company_status']}}" readonly>
                                    <input type="hidden" class="form-control" id="payment_entity_type" readonly>
                                    <input type="hidden" class="form-control" id="payment_business_name" readonly>
                                    <input type="hidden"class="form-control" id="payment_no_of_license" readonly>
                                    <input type="hidden" class="form-control" name="payment_subscription_fee" id="payment_subscription_fee" readonly>
                                    <input type="hidden" id="payment_type" name="payment_type" value="subscription">
                                    <input type="hidden" id="entity_type" name="entity_type" value="{{$entity_type}}">


                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-1">
                                            <div class="col-sm-6">
                                                    <a class="btn btn-default prev" href="#">Previous</a>
                                                </div>
                                            <div class="col-sm-6 text-right">
                                                <button type="button" class="btn btn-primary pull-right" @if($entity_type != 2) id="payment_subscription" @endif @if($entity_type == 2) id="mjb_subscription" @endif >@if($entity_type == 2 && $company_detail['foc'] == 0 || ($cc_ge_subscription == 1 && $entity_type != 2 )) PAY NOW & SIGN UP @endif @if($entity_type == 2 && $company_detail['foc'] == 1 || ($cc_ge_subscription == 1 && $entity_type != 2 )) REGISTER @endif @if($entity_type != 2 && $cc_ge_subscription == 0 ) SAVE CARD DETAILS @endif</button>
                                                @if($entity_type != 2 && $cc_ge_subscription == 0 && $company_detail['company_status'] == 4 && $company_detail['company_status'] == 5 )
                                                    <button type="button" class="btn btn-primary pull-right" id="active_account" style="display: none">Activate</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if($company_detail['foc'] == 0)
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-1">
                                            <div class="col-md-12 card-container">
                                                <div class="card-images"><img src="../images/cards.png"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </form>

                                    <input type="hidden" id="foc" value="{{$company_detail['foc']}}">

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
                            <label class="col-sm-4 control-label">Address Line 1*</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" placeholder="Address Line 1 " id="edit_add_line_1" name="edit_add_line_1">
                                @if ($errors->has('add_line_1'))<label class="error">{!!$errors->first('add_line_1')!!}</label>@endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Address Line 2</label>
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
                            <label class="col-sm-4 control-label">Citi(es)*</label>
                            <div class="col-sm-8">
                                <select class="form-control cities" name="edit_cities" id="edit_cities" >
                                    <option value="">Select Cities</option>
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

<div class="modal fade in" id="edit-license-location" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Edit License</h4>
                <small class="font-bold"></small>
            </div>
            <form name="edit_license_location_form"  id="edit_license_location_form" >
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Location </label>
                            <div class="col-sm-8">
                                <select name="edit_company_location" class="form-control" id="edit_company_location">
                                    <option value=""> Select Location</option>
                                </select>
                                <input type="hidden" name="edit_company_location" id="hide_edit_company_location">
                                @if ($errors->has('edit_company_location'))<label class="error">{!!$errors->first('edit_company_location')!!}</label>@endif
                            </div>
                        </div>
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">License Type</label>
                            <div class="col-sm-8">
                                <select name="edit_licen_type" id="edit_licen_type" class="form-control">
                                    <option value=""> Select Licenses Type</option>
                                </select>
                                <input type="hidden" name="edit_licen_type" id="hide_edit_licen_type">
                                @if ($errors->has('edit_licen_type'))<label class="error">{!!$errors->first('edit_licen_type')!!}</label>@endif
                            </div>
                        </div>
                    </div>
                    <div id="display-only-cc-ge" >
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">License Number</label>
                                <div class="col-sm-8">
                                    <input type="text" value="" id="edit_licen_no" name="edit_licen_no" class="form-control"  placeholder="License Number">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <div class="col-sm-4"></div>
                            <div class="col-sm-8">
                                <button type="button" class="btn w-xs btn-default" id="cls-license">Close</button>
                                <button type="button" class="btn w-xs btn-primary" id="change-license">Save</button>
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
                                <input class="form-control" type="text" placeholder="Name" name="edit_name" id="edit_name">
                                @if ($errors->has('name'))<label class="error">{!!$errors->first('name')!!}</label>@endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Email Address</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" placeholder="Email Address" name="edit_email_address" id="edit_email_address">
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

{!! Html::script('js/company/company-registration.js') !!}
{!! Html::script('js/licenses.js') !!}
{!! Html::script('js/payment.js') !!}
@stop

