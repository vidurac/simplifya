<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 6/28/2016
 * Time: 3:37 PM
 */
?>
@extends('layout.dashbord')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Add the license types your business holds that you’d like to be audited for. Whether you are self-auditing your business or having a 3rd party inspector audit your business, Simplifya’s audit checklists are generated based upon the licenses entered here. Our simple pricing structure of ${{$plan_amount}}/month per license makes it easy for you to have peace of mind when it comes to your business’s legal compliance.
                </div>
                <div class="hpanel">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group text-left">
                                    <a class="btn btn-info" id="new-license-btn" href="{{URL('configuration/licenses/new')}}">Add New License</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <form id="eventForm" class="form-horizontal">
                                <div class="col-lg-12">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                                <input class="form-control" type="text" name="license_number" id="license_number" placeholder="Search by License No">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" name="license_type" id="license_type">
                                                    <option value="">Select License Type</option>
                                                    @foreach($licenses as $license)
                                                        <option value="{{$license['license_id']}}">{{ $license['license_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" name="license_location" id="license_location">
                                                    <option value="">Select Location</option>
                                                    @foreach($locations as $location)
                                                        <option value="{{$location['loc_id']}}">{{ $location['loc_name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <a name="license_search" id="license_search" class="btn btn-default"><i class="fa fa-search"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed" id="license-table-manager">
                                        <thead>
                                        <tr>
                                            {{--<th>#</th>--}}
                                            <th>License Number</th>
                                            <th>License Type</th>
                                            <th>Location Name</th>
                                            <th>Renew By Date</th>
                                            <th>Renewal Status</th>
                                            <th>License Status</th>
                                            <th>Action</th>
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

    <div class="modal fade in" id="edit-license-details" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Edit License Details</h4>
                    <small class="font-bold"></small>
                </div>
                <form name="license_details_form_edit"  id="license_details_form_edit">
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">License Type*</label>
                                <div class="col-sm-8">
                                    <input name="license_type_edit" class="form-control" id="license_type_edit" readonly>
                                    <input type="hidden" name="edit_license_type" id="edit_license_type">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">License Number*</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="License Number" name="edit_license_number" id="edit_license_number">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Location*</label>
                                <div class="col-sm-8">
                                    <input name="edit_license_location" id="edit_license_location" class="form-control" readonly>
                                    <input type="hidden" name="license_location_edit" id="license_location_edit">
                                </div>
                            </div>
                        </div>
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-lg-12">--}}
                                {{--<label class="col-sm-4 control-label">Name / DBA*</label>--}}
                                {{--<div class="col-sm-8">--}}
                                    {{--<input class="form-control" type="text" placeholder="Name / DBA" name="dba_name" id="dba_name">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Expiration date *</label>
                                <div class="col-sm-8">
                                    <div class="input-group input-append date" id="licenseDatePicker">

                                        <input type="text" class="form-control" name="edit_license_date" id="edit_license_date" />

                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                    <span id="err-date"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Renew By Date *</label>
                                <div class="col-sm-8">
                                    <div class="input-group input-append date" id="renewalDatePicker">

                                        <input type="text" class="form-control" name="edit_renewal_date" id="edit_renewal_date" />

                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                    <span id="err-date"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Renewal Reminders*</label>
                                <div class="col-sm-8">
                                    <select name="reminder" class="form-control" id="reminder" multiple="multiple">
                                        <option value="180">180 Days</option>
                                        <option value="120">120 Days</option>
                                        <option value="90">90 Days</option>
                                        <option value="1">1 Day</option>
                                    </select>
                                    <small class="text-muted">Set renewal reminders to receive an email when you need to renew.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default cls-edit-license-form" data-dismiss="modal" >Close</button>
                        <button type="button" class="btn btn-primary save-license-changes" >Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade in" id="new-license-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Add New Licenses</h4>
                    <small class="font-bold"></small>
                </div>
                <form name="license_add_form"  id="license_add_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <h2 class="text-center">Add a new license to your account</h2>
                                <div class="col-xs-12">
                                   <p>The monthly fee to use Simplifya is $150 per license, charged on the 1st of each month. You will be charged a prorated amount today. This will now allow you to inspect for this particular license type at this location.</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">License Type*</label>
                                <div class="col-sm-8">
                                    <select name="new_license_type" class="form-control" id="new_license_type">
                                        <option value="">Select License Type</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">License Number*</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="License Number" name="new_license_number" id="new_license_number">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Location*</label>
                                <div class="col-sm-8">
                                    <select name="new_license_location" id="new_license_location" class="form-control">
                                        <option value="">Select Location</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        {{--<div class="row">--}}
                            {{--<div class="form-group col-lg-12">--}}
                                {{--<label class="col-sm-4 control-label">Name / DBA*</label>--}}
                                {{--<div class="col-sm-8">--}}
                                    {{--<input class="form-control" type="text" placeholder="Name / DBA" name="new_dba_name" id="new_dba_name">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Expiration date *</label>
                                <div class="col-sm-8">
                                    <div class="input-group input-append date" id="newLicenseDatePicker">

                                        <input type="text" class="form-control" name="new_license_date" id="new_license_date" />

                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                    <span id="err-date"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Renew By Date *</label>
                                <div class="col-sm-8">
                                    <div class="input-group input-append date" id="newRenewalDatePicker">

                                        <input type="text" class="form-control" name="new_renewal_date" id="new_renewal_date" />

                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                    <span id="err-date"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Renewal Reminders*</label>
                                <div class="col-sm-8">
                                    <select name="new_reminder" class="form-control" id="new_reminder" multiple="multiple">
                                        <option value>Select Reminder</option>
                                        <option value="180">180 Days </option>
                                        <option value="120">120 Days</option>
                                        <option value="90">90 Days</option>
                                        <option value="1">1 Day</option>
                                    </select>
                                    <small class="text-muted">Set renewal reminders to receive an email when you need to renew.</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                        <label><input type="checkbox" name="terms" value="1"> I have read & agree to the <a target="_blank" href="http://simplifya.com/terms-of-service/"><u>Terms of Service</u></a></label>
                                    <span id="err-terms"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <?php if (isset($foc)): ?>
                                        <?php if ($foc != 1): ?>
                                            <h2>Cost: $<span id="cost_amount">0</span></h2>
                                        <?php else: ?>
                                            {{--<h2 class="text-danger"><strong>Free</strong></h2>--}}
                                        <?php endif;?>
                                    <?php endif;?>
                                    <input type="text" name="amount_cost" id="amount_cost" value="" readonly="true" style="display: none">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <?php if (isset($foc)): ?>
                                        <?php if ($foc != 1): ?>
                                            <button class="btn btn-info" id="paynow_btn"><b>Add License & Pay Now</b></button>
                                        <?php else: ?>
                                            <button class="btn btn-info" id="paynow_btn"><b>Add License For Free</b></button>
                                        <?php endif;?>
                                    <?php else:?>
                                        <button class="btn btn-info" id="paynow_btn"><b>Add License & Pay Now</b></button>
                                    <?php endif;?>

                                </div>
                            </div>
                        </div>

                        <?php if (isset($foc)): ?>
                            <?php if ($foc != 1): ?>
                                <div class="row">
                                    <div class="form-group col-lg-12">
                                        <div class="col-md-12 card-container">
                                            <div class="card-images"><img src="../images/cards.png"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif;?>
                        <?php else:?>
                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <div class="col-md-12 card-container">
                                        <div class="card-images"><img src="../images/cards.png"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>


                    </div>
                    <div class="modal-footer">

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade in" id="license-perches" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">License Activate</h4>
                    <small class="font-bold"></small>
                </div>
                <form name="license_details_form_edit"  id="license_details_form_edit">
                    <div class="modal-body">

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">License Type</label>
                                <div class="col-sm-8">
                                    <input name="new_license_type_edit" class="form-control" id="new_license_type_edit" readonly>
                                    <input type="hidden" name="perches_license_type" id="perches_license_type">
                                    <input type="hidden" name="license_location_id" id="license_location_id">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">License Number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="License Number" name="perches_license_number" id="perches_license_number" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Location</label>
                                <div class="col-sm-8">
                                    <input name="perches_license_location" id="perches_license_location" class="form-control" readonly>
                                    <input type="hidden" name="license_location_perches" id="license_location_perches">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <?php if (isset($foc)): ?>
                                    <?php if ($foc != 1): ?>
                                        <h2>Cost: $<span id="active_cost_amount">0</span></h2>
                                    <?php else: ?>
                                        {{--<h2 class="text-danger"><strong>Free</strong></h2>--}}
                                    <?php endif;?>
                                    <?php else:?>
                                        <button class="btn btn-info" id="license_active_btn"><b>PAY NOW</b><br>&<br>Activate License</button>
                                    <?php endif;?>
                                    <input type="text" name="active_amount_cost" id="active_amount_cost" value="" readonly="true" style="display: none">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <?php if (isset($foc)): ?>
                                        <?php if ($foc != 1): ?>
                                            <button class="btn btn-info" id="license_active_btn"><b>PAY NOW</b><br>&<br>Activate License</button>
                                        <?php else: ?>
                                            <button class="btn btn-info" id="license_active_btn">Activate License for free</button>
                                        <?php endif;?>
                                    <?php else:?>
                                        <button class="btn btn-info" id="license_active_btn"><b>PAY NOW</b><br>&<br>Activate License</button>
                                    <?php endif;?>

                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>


    {!! Html::script('js/license/manage.js') !!}
@stop
