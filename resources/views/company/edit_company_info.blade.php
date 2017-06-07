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

                    <div class="panel-body">
                        <div class="text-center m-b-md" id="wizardControl">
                            @if(Session('entity_type') == 1)
                                <a class="btn btn-primary"  data-toggle="tab" id="tab_1"> Basic Info</a>
                            @else
                            <a class="btn btn-primary"  data-toggle="tab" id="tab_1" data-tab_no="1"> Basic Info</a>
                            <a class="btn btn-default"  data-toggle="tab" id="tab_2" data-tab_no="2"> Business Locations</a>
                            <a class="btn btn-default"  data-toggle="tab" id="tab_3" data-tab_no="3"> Invite Employees</a>
                            <a class="btn btn-default"  data-toggle="tab" id="tab_4" data-tab_no="4"> Card Details</a>
                            @endif
                            <input type="hidden" id="company_id" value="{{$company_id}}">
                            <input type="hidden" id="entity_type" value="{{$entity_type}}">
                            <input type="hidden" id="master_user_group_id" value="{{$master_user_group_id}}">
                            <input type="hidden" id="cc_ge_subscription" value="{{$cc_ge_subscription}}">
                        </div>

                        <div class="tab-content">
                            <div id="step1" class="p-m tab-pane active">
                                <div class="col-md-3">
                                    <div class="user-pro-pic">
                                        <div class="dropzone">
                                            <img class="user-pro-pic img-responsive" src="{{$imageUrl}}">
                                            <output id="list"></output>
                                            <a href="#">Update Logo <i class="fa fa-camera"></i></a>
                                            <input type="file" name="profilePicture" id="profilePicture">
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-8">
                                <form name="edit_company_details" novalidate id="edit_company_details" action="#" method="post">
                                    @if(Session('entity_type') != 2)
                                        <div class="row">
                                            <div class="form-group col-lg-10">
                                                <label class="col-sm-4 control-label">Account Status</label>
                                                <div class="col-sm-6">
                                                    @if($company_detail['company_status'] == 2)
                                                        <label><input type="radio" name="is_active" class="i-checks" value="2" checked> Active </label>
                                                        <label><input type="radio" name="is_active" class="i-checks" value="4" > Inactive </label>
                                                    @elseif($company_detail['company_status'] == 4)
                                                        <label><input type="radio" name="is_active" class="i-checks" value="2" > Active </label>
                                                        <label><input type="radio" name="is_active" class="i-checks" value="4" checked> Inactive </label>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="form-group col-lg-10">
                                            <label class="col-sm-4 control-label">Entity Type</label>
                                            <div class="col-sm-6">
                                                <input name="entity_type" id="entity_type" class="form-control" type="text" placeholder="Entity Type" value="{{$company_detail['company_type']}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10">
                                            <label class="col-sm-4 control-label">Name of Business / Entity</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="Name of Business / Entity " id="name_of_business" value="{{$company_detail['company_name']}}" name="name_of_business" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10">
                                            <label class="col-sm-4 control-label">FEIN</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" type="text" placeholder="{{$company_detail['fein_display']}}" id="company_registration_no"  value="" name="company_registration_no" >
                                                <input type="hidden" class="form-control" id="company_registration_noH"  value="" name="company_registration_noH" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-10">
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-6">
                                                <button type="button" class="btn w-xs btn-primary" id="change-basic-info">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                @if(Session('entity_type') != 1)
                                    <div class="text-right m-t-xs">
                                        <a class="btn btn-primary edit-next" id='next' href="#">Next</a>
                                    </div>
                                @endif
                                </div>
                            </div>

                            <div id="step2" class="p-m tab-pane">
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

                                <div class="text-right m-t-xs">
                                    <a class="btn btn-default prev" data-target="#myModal5" href="#">Previous</a>
                                    <a class="btn btn-primary edit-next" href="#">Next</a>
                                </div>

                            </div>
                            <div id="step3" class="p-m tab-pane">
                                <div class="row">
                                    <div class="form-group col-lg-12">
                                        <div class="col-sm-4">
                                            <button type="button" class="btn w-xs btn-info" id="invite-emp">Invite Employee</button>
                                        </div>
                                        <div class="col-sm-8">

                                        </div>
                                    </div>
                                </div>
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
                                    <a class="btn btn-default prev" data-target="#myModal5" href="#">Previous</a>
                                    <a class="btn btn-primary edit-next" href="#">Next</a>
                                </div>

                            </div>

                            <div id="step4" class="p-m tab-pane">
                                @if($cc_added != 0)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group text-left">
                                            <button class="btn btn-info" id="new-card-btn">Add New Card</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="h-panel">
                                    <div class="panel-heading hbuilt">
                                        <div class="panel-tools">
                                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                        </div>
                                        Card Details
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table cellpadding="1" cellspacing="1" class="table table-bordered table-striped" id="credit_card_table">
                                                <thead>
                                                <tr>
                                                    <th>Card Number</th>
                                                    <th>Expiry Month</th>
                                                    <th>Expiry Year</th>
                                                    <th>Card Type</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                                @endif

                                <!-- payment details -->
                                {{--@if($entity_type != 2 && $cc_added == 0)--}}
                                @if($cc_added == 0)
                                <form name="payment_form" novalidate id="payment_form" action="#" method="post">
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
                                                    <label class="col-sm-4 control-label">Card Number</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" type="text" placeholder="Card Number" name="card_number" id="card_number" >
                                                        @if ($errors->has('card_number'))<label class="error" id="err-card_number">{!!$errors->first('card_number')!!}</label>@endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-lg-12">
                                                    <label class="col-sm-4 control-label">CCV Number</label>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" type="text" placeholder="CCV Number" name="ccv_number" id="ccv_number">
                                                        @if ($errors->has('ccv_number'))<label class="error">{!!$errors->first('ccv_number')!!}</label>@endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-12">
                                                    <label class="col-sm-4 control-label">Expiration Month</label>
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
                                                    <label class="col-sm-4 control-label">Expiration Year</label>
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
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-1">
                                            <div class="col-sm-6">
                                                <a class="btn btn-default prev" href="#">Previous</a>
                                            </div>
                                            <div class="col-sm-6 text-right">
                                                {{--<button type="button" class="btn btn-pay" @if($entity_type != 2) id="payment_subscription" @endif @if($entity_type == 2) id="mjb_subscription" @endif >@if($entity_type == 2)PAY NOW @endif @if($entity_type != 2) SAVE CARD DETAILS  @endif </button>--}}
                                                <button type="button" class="btn btn-pay" id="payment_subscription" >SAVE CARD DETAILS</button>
                                                <button type="button" class="btn btn-primary pull-right" id="active_account" style="display: none">Activate</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-1">
                                            <div class="col-md-12 card-container">
                                                <div class="card-images"><img src="/images/cards.png"></div>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            @endif
                                <!-- payment details end -->

                                @if($cc_added == 1)
                                {{--<form name="edit_card_details" novalidate id="edit_card_details" action="#" method="post">
                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label"> Card Number</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="card_number" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Expiration Month</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="exp_month" name="exp_month">
                                            </div>
                                         </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <label class="col-sm-4 control-label">Expiration Year</label>
                                            <div class="col-sm-6">
                                                <input type="text"class="form-control" id="exp_year" name="exp_year">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-10 col-sm-offset-2">
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-6">
                                                <button type="button" class="btn w-xs btn-primary" id="change-card-info">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>--}}

                                <div class="text-right m-t-xs">
                                    <a class="btn btn-default prev" href="#">Previous</a>
                                </div>
                                @endif
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
                                    <input class="form-control" type="text" placeholder="Name " id="edit_name_of_location" name="edit_name_of_location" maxlength="50" autocomplete="off">
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


    <div class="modal fade in" id="edit-card-details" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Edit Card Details</h4>
                    <small class="font-bold"></small>
                </div>
                <form name="edit_card_details" novalidate id="edit_card_details" class="edit-card-details-custom-fix" action="#" method="post">
                    <div class="row">
                        <div class="form-group col-lg-10 col-sm-offset-2">
                            <label class="col-sm-4 control-label"> Card Number</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="card_number" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-10 col-sm-offset-2">
                            <label class="col-sm-4 control-label">Expiry Month</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="exp_month" name="exp_month">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-10 col-sm-offset-2">
                            <label class="col-sm-4 control-label">Expiry Year</label>
                            <div class="col-sm-6">
                                <input type="text"class="form-control" id="exp_year" name="exp_year">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-10 col-sm-offset-2">
                            <div class="col-sm-4"></div>
                            <div class="col-sm-6">
                                <button type="button" class="btn w-xs btn-primary" id="change-card-info">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!--  -->
    <div class="modal fade in" id="new-card-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">New Card Details</h4>
                    <small class="font-bold"></small>
                </div>
                <form name="payment_form" novalidate id="payment_form" action="#" method="post">
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
                                <label class="col-sm-4 control-label">Card Number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="Card Number" name="new_card_number" id="new_card_number" >
                                    @if ($errors->has('new_card_number'))<label class="error" id="err-card_number">{!!$errors->first('new_card_number')!!}</label>@endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">CCV Number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="CCV Number" name="new_ccv_number" id="new_ccv_number">
                                    @if ($errors->has('new_ccv_number'))<label class="error">{!!$errors->first('new_ccv_number')!!}</label>@endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Expiry Month</label>
                                <div class="col-sm-8">
                                    <select name="new_exp_month" class="form-control" id="new_exp_month">
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
                                    @if ($errors->has('new_exp_month'))<label class="error">{!!$errors->first('new_exp_month')!!}</label>@endif
                                </div>
                            </div>
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Expiry Year</label>
                                <div class="col-sm-8">
                                    <select name="new_exp_year" id="new_exp_year" class="form-control">
                                        <?php
                                        $this_year = date('Y', time());
                                        for ($i = 0; $i < 10; $i++) {
                                            echo '<option value="' . $this_year . '">' . $this_year . '</option>';
                                            $this_year++;
                                        }
                                        ?>
                                    </select>
                                    @if ($errors->has('new_exp_year'))<label class="error">{!!$errors->first('new_exp_year')!!}</label>@endif
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

                    <div class="row">
                        <div class="form-group col-lg-10 col-sm-offset-1">
                            <div class="col-sm-6 col-sm-offset-6 text-right">
                                <button type="button" class="btn btn-pay" id="addCompanyCard" >SAVE CARD DETAILS</button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-10 col-sm-offset-1">
                            <div class="col-md-12 card-container">
                                <div class="card-images"><img src="/images/cards.png"></div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <!-- Crop modal -->
    <div class="modal fade" id="crop-modal" style="z-index: 9999999999999">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('upload_file') }}" method="post" id="savecropdataform">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">Crop Picture</h4>
                    </div>
                    <div class="modal-body">
                        <div class="bootstrap-modal-cropper">
                            <img id="crop-img" width="100%" src="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="crop_image">
                        <input type="hidden" id="image_name" name="image_name">
                        <a class="btn btn-default pull-left" style="margin: 15px" data-loading-text="Saving..." data-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="btn-crop" style="margin: 15px" data-loading-text="Cropping...">Crop</button>
                    </div>
                    <input type="hidden" name="bucket_path" value="{{ Config::get('simplifya.PROFILE_IMG_DIR') }}">
                    <input type="hidden" name="upload_type" value="{{ Config::get('simplifya.UPD_TYPE_PROFILE') }}">
                    <input type="hidden" name="config_type" value="{{ Config::get('simplifya.IMG_SIZE_USER') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
        </div>
    </div>
    <style type="text/css">
        .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
            background-color: #eee!important;
            opacity: 1 !important;
        }
        .dropzone {
            position: relative;
            width: 90% !important;
        }
        .dropzone a {
            height: 40px;
            width: 100%;
            background-color: #30465a;
            color: #fff;
            position: absolute;
            left: 0px;
            bottom: 0px;
            text-transform: uppercase;
            text-align: center;
            line-height: 40px;
            text-decoration: none;
            display: block;
            z-index: 1031; }
        .dropzone a i {
            margin-left: 10px; }
        .dropzone img {
            min-height: 220px; }
        .dropzone input[type="file"] {
            visibility: hidden; }

    </style>

    <script type="application/javascript">

        var bucket_path = '{{ Config::get('simplifya.BUCKET_URL').Config::get('simplifya.COMPANY_LOGO_IMG_DIR').'/' }}';
        /**
         * Image cropping
         */
        $(document).ready(function (){

            /* == Crop images == */

            $(document).on('submit', '#savecropdataform', function() {
                var inputid = $(document).find('#crop-img').data('target');
                $('#btn-crop').button('loading');
                $('#savecropdataform').ajaxSubmit({
                    type:"POST",
                    data: {
                        x: $('#crop-img').cropper('getData').x,
                        y: $('#crop-img').cropper('getData').y,
                        w: $('#crop-img').cropper('getData').width,
                        h: $('#crop-img').cropper('getData').height,
                        catagory:'company',
                        image: $('#crop_image').val()
                    },
                    dataType:'json',
                    success: function(result) {

                        file = document.getElementById("profilePicture");

                        if(result.success == 'true'){
                            var span = document.createElement('span');
                            var span = '<input type="hidden" name="cropfiles" value="'+result.data.fileid+'">';
                            var imageHtml = '<img src="'+bucket_path+result.data.filename+ '" title="'+result.data.filename+ '" class="user-pro-pic img-responsive"/>'
                            $(".user-pro-pic").find("img").remove();

                            $('#list').after(imageHtml);
                            document.getElementById('list').innerHTML = span;
                        }else{
                            car.msg('warning',result.msg, 4000);
                        }
                        $('#btn-crop').button('reset')
                        $('#crop-img').data('modal', null);
                        $('#crop-modal').modal('hide');

                        $("#profilePicture").valid();
                    }
                });
                return false;
            });

            var models = [];
            $(document).on('shown.bs.modal', '#crop-modal', function() {

                $('#crop-img').cropper({cropBoxResizable:true, aspectRatio: {{ Config::get('simplifya.RESIZE_USER_CROP_WIDTH') }}/{{ Config::get('simplifya.RESIZE_USER_CROP_HEIGHT') }} });

                $('#crop-img').cropper("setData", {
                    width: {{ Config::get('simplifya.RESIZE_USER_CROP_WIDTH') }},
                    height: {{ Config::get('simplifya.RESIZE_USER_CROP_HEIGHT') }}
                });

            }).on('hidden.bs.modal', '#crop-modal', function() {
                $('#crop-img').cropper('destroy');
                $('body').find('.modal').each(function() {
                    //
                });
            });

            document.getElementById('profilePicture').addEventListener('change', handleFileSelect, false);
            $('body').on('hidden', function () { $(this).removeData('modal'); });

        });

        function handleFileSelect(evt) {
            var files = evt.target.files; // FileList object
            // Loop through the FileList and render image files as thumbnails.
            for (var i = 0, f; f = files[i]; i++) {
                // Only process image files.
                if (!f.type.match('image.*')) {
                    continue;
                }
                var reader = new FileReader();
                // Closure to capture the file information.
                reader.onload = (function(theFile) {
                    return function(e) {
                        // Render thumbnail.
                        var span = document.createElement('span'); selectOnBlur: true,
                                $('#crop_image').val(e.target.result);
                        $('#crop-img').attr('src',e.target.result);
                        $('#image_name').val(escape(theFile.name));

                        var image = $('#profilePicture').val();
                        $('#crop-modal').modal({
                            show: true,
                            backdrop: 'static',
                            keyboard: true});
                    };
                })(f);
                // Read in the image file as a data URL.
                reader.readAsDataURL(f);
            }
        }

    </script>
    {!! Html::script('js/company/company-registration.js') !!}
    {!! Html::script('js/company/edit-company-prof.js') !!}
@stop
