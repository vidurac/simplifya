<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 6/6/2016
 * Time: 10:34 AM
 */
?>

@extends('layout.dashbord')

@section('content')
    <div class="content" ng-controller="createReferral" ng-cloak="">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <form name="addReferrerForm" novalidate ng-init="form.mjbEntityType=<?php echo $MJB_entity_type?>;@if($referrer_id==0)init()@else;getReferrer(<?php echo $referrer_id?>)@endif">
                            <input type="hidden"  ng-model="form.mjbEntityType">
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Referrer Name*</label>
                                    <div class="col-sm-4">
                                        <input type="text" ng-model="form.name" autocomplete="off" name="name" id="name" class="form-control" ng-maxlength="255" ng-disabled="form.used=='1'"required ng-readonly="<?php echo $read_only ?>"/>
                                        <div ng-messages="addReferrerForm.name.$error" ng-if="addReferrerForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Name is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Email*</label>
                                    <div class="col-sm-9">
                                        <input type="email" ng-model="form.email" autocomplete="off" name="email" id="email" class="form-control" ng-disabled="form.used=='1'" ng-readonly="<?php echo $read_only ?>" required />
                                        <div ng-messages="addReferrerForm.email.$error" ng-if="addReferrerForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Email is required.</p>
                                            <p ng-message="email">Invalid Email.</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Type*</label>
                                    <div class="col-sm-9">
                                        <select name="ref_type" required class="form-control" ng-model="form.type" ng-options="type.value as type.name for type in types" ng-disabled="<?php echo $read_only ?>">
                                            <option value="">Please select referrer type</option>
                                        </select>
                                        <div ng-messages="addReferrerForm.ref_type.$error" ng-if="addReferrerForm.$submitted" class="coupon-error">
                                            <p ng-message="required">Referrer type is required.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">
                                <label class="col-sm-3 control-label">Commission*</label>
                                <div class="col-lg-9  col-xs-12 form-group coupon-detail">
                                    <div class="th-border m-b-none col-lg-12 col-xs-12 no-padding">
                                        <div class="col-lg-3 col-xs-3"><label class="control-label"><strong>Subscription Plan</strong></label></div>
                                        <div class="col-lg-6 col-xs-6"><label class="control-label"><strong>Type</strong></label></div>
                                        <div class="col-lg-3 col-xs-3"><label class="control-label"><strong>Commission</strong></label></div>
                                    </div>
                                    <div class="form-group col-lg-12 col-xs-12 tb-cell-n" ng-repeat="plan_detail in form.plan_details">
                                        <div class="col-sm-3 col-xs-3">
                                            <label class="control-label">@{{plan_detail.name}}</label>
                                        </div>
                                        <div class="col-sm-6 col-xs-6">
                                            <div class="col-sm-6"><label><input type="radio"  autocomplete="off" value="percentage" ng-model="plan_detail.type" ng-disabled="form.used=='1' || <?php echo $read_only?>" /> Percentage</label></div>
                                            <div class="col-sm-6"><label><input type="radio"  autocomplete="off" value="fixed" ng-model="plan_detail.type" ng-disabled="form.used=='1' || <?php echo $read_only?>"/> Fixed</label></div>

                                        </div>
                                        <div class="col-sm-3 col-xs-3">
                                            <label>
                                            <input type="text" ng-if="plan_detail.type == 'fixed'" autocomplete="off" discount="@{{plan_detail.plan_amount}}" name="fixed_@{{$index}}" ng-model="plan_detail.amount" class="input-plans form-control" ng-disabled="form.used=='1'" ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" required ng-readonly="<?php echo $read_only ?>"/>
                                            <input type="text" ng-if="plan_detail.type == 'percentage'" autocomplete="off" ng-pattern="/^(100(?:\.0{1,2})?|0*?\.\d{1,2}|\d{1,2}(?:\.\d{1,2})?)$/" name="percentage_@{{$index}}" ng-model="plan_detail.amount" class="input-plans form-control" ng-disabled="form.used=='1'" required ng-readonly="<?php echo $read_only ?>"/>
                                            </label>
                                            <div ng-messages="addReferrerForm['fixed_' + $index].$error" ng-if="addReferrerForm.$submitted" class="coupon-error">
                                                <p ng-message="pattern">Enter a valid amount</p>
                                                <p ng-message="discount">Commission cannot be greater than @{{plan_detail.plan_amount}}</p>
                                                <p ng-message="required">Amount is required.</p>
                                            </div>
                                            <div ng-messages="addReferrerForm['percentage_' + $index].$error" ng-if="addReferrerForm.$submitted" class="coupon-error">
                                                <p ng-message="pattern">Percentage should be 0 to 100</p>
                                                <p ng-message="required">Percentage is required.</p>
                                            </div>
                                        </div>
                                </div>
                                </div>
                                </div>
                            </div>
                            <div class="row" ng-show="<?php echo $referrer_id ?>!=0 && form.used!='1' && form.code_details.length">
                                <div class="form-group col-lg-12 col-sm-12">
                                    <label class="col-sm-3 control-label">Referral Code</label>
                                    <div class="col-lg-9  col-xs-12 form-group coupon-detail">
                                        <div class="col-sm-12" ng-repeat="code_detail in form.code_details">
                                            <label class="col-sm-2 no-padding control-label" ng-model="code_detail.code">@{{code_detail.code}}</label>
                                            <button style="border: none;color: #428bca;background: none" uib-popover-template="dynamicPopover.templateUrl" popover-title="@{{dynamicPopover.title}}" popover-placement="right" popover-elem>Copy Link</button>
                                        </div>
                                        <script type="text/ng-template" id="myPopoverTemplate.html">
                                            <div class="form-group">
                                                <input type="text" autocomplete="off"  onclick="this.focus();this.select()" class="form-control exclude" value="{{URL('/company/mjb-register')}}/@{{code_detail.token}}" >
                                            </div>
                                            <a style="float: right;" ng-click="toClipboard(code_detail.token)">Copy to Clipboard</a>
                                        </script>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group col-lg-12 col-xs-12">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5 text-right no-padding" ng-hide="<?php echo $read_only ?>">
                                    <button ng-show="<?php echo $referrer_id ?>==0" class="btn btn-success" type="submit" ng-click="saveReferrer(addReferrerForm.$valid)" ng-disabled="savingReferrer">Save</button>
                                    <button ng-show="<?php echo $referrer_id ?>!=0 && form.used!='1'" class="btn btn-warning" type="submit" ng-click="saveReferrer(addReferrerForm.$valid)" ng-disabled="savingReferrer">Update</button>
                                    <a href="/configuration/referrals" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12" ng-show="<?php echo $referrer_id ?>!=0">
                <div class="hpanel">
                    <h5><strong>COMMISSION DETAILS</strong></h5>
                    <div class="panel-body">
                        <form name="referrerCommissions" ng-init="@if($referrer_id!=0)getCommissions(<?php echo $referrer_id?>)@endif">
                            <div class="row">
                                <div class="form-group col-lg-12 col-sm-12">

                                    <div class="col-xs-12 col-sm-12 coupon-detail">
                                        <div class="th-border col-lg-12 col-xs-12 no-padding m-b-none">
                                            <div class="col-lg-1 col-xs-2"><label class="control-label"><strong>#</strong></label></div>
                                            <div class="col-lg-4 col-xs-5">
                                                <a href="#" ng-click="sortTypeCommission = 'mjb_name'; sortReverseCommission = !sortReverseCommission">
                                                    <span class="tb-sort-header-name">
                                                        <strong>MJB Name</strong>
                                                    </span>
                                                    <span class="tb-sort-icons">
                                                        <i ng-show="sortTypeCommission == 'mjb_name' && !sortReverseCommission" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortTypeCommission == 'mjb_name' && sortReverseCommission" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortTypeCommission != 'mjb_name'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="col-lg-2 col-xs-5">
                                                <a href="#" ng-click="sortTypeCommission = 'created_at'; sortReverseCommission = !sortReverseCommission">
                                                    <span class="tb-sort-header-name">
                                                        <strong>Registered Date</strong>
                                                    </span>
                                                    <span class="tb-sort-icons">
                                                        <i ng-show="sortTypeCommission == 'created_at' && !sortReverseCommission" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortTypeCommission == 'created_at' && sortReverseCommission" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortTypeCommission != 'created_at'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="col-lg-2 col-xs-4">
                                                <a href="#" ng-click="sortTypeCommission = 'plan'; sortReverseCommission = !sortReverseCommission">
                                                    <span class="tb-sort-header-name">
                                                        <strong>Plan</strong>
                                                    </span>
                                                    <span class="tb-sort-icons">
                                                          <i ng-show="sortTypeCommission == 'plan' && !sortReverseCommission" class="fa fa-caret-down"></i>
                                                          <i ng-show="sortTypeCommission == 'plan' && sortReverseCommission" class="fa fa-caret-up"></i>
                                                          <i ng-show="sortTypeCommission != 'plan'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="col-lg-2 col-xs-4">
                                                <a href="#" ng-click="sortTypeCommission = 'commission'; sortReverseCommission = !sortReverseCommission">
                                                    <span class="tb-sort-header-name">
                                                        <strong>Commission</strong>
                                                    </span>
                                                    <span class="tb-sort-icons">
                                                        <i ng-show="sortTypeCommission == 'commission' && !sortReverseCommission" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortTypeCommission == 'commission' && sortReverseCommission" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortTypeCommission != 'commission'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="col-lg-1 col-xs-4">
                                                <a href="#" ng-click="sortTypeCommission = 'referral_payment_id'; sortReverseCommission = !sortReverseCommission">
                                                    <span class="tb-sort-header-name">
                                                        <strong>Paid</strong>
                                                    </span>
                                                    <span class="tb-sort-icons">
                                                        <i ng-show="sortTypeCommission == 'referral_payment_id' && !sortReverseCommission" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortTypeCommission == 'referral_payment_id' && sortReverseCommission" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortTypeCommission != 'referral_payment_id'" class="fa fa-sort"></i>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-xs-12 border-bottom p-t-b" dir-paginate="commission in commissions |orderBy:sortTypeCommission:sortReverseCommission| itemsPerPage: 10" pagination-id="commission">
                                            <div class="col-lg-1 col-xs-2"><input  ng-disabled="commission.referral_payment_id!=0" ng-true-value="'1'" ng-false-value="'0'" class="ng-pristine ng-valid ng-not-empty ng-touched" ng-model="commission.status" style="" type="checkbox"></div>
                                            <div class="col-lg-4 col-xs-5">@{{ commission.mjb_name}}</div>
                                            <div class="col-lg-2 col-xs-5">@{{ commission.created_at | date:'MM-dd-yyyy'}}</div>
                                            <div class="col-lg-2 col-xs-4">@{{ commission.plan}} Month</div>
                                            <div class="col-lg-2 col-xs-4">$@{{ commission.commission}}</div>
                                            <div class="col-lg-1 col-xs-4" ng-show="commission.referral_payment_id!=0"><span class="badge badge-success">Yes</span></div>
                                            <div class="col-lg-1 col-xs-4" ng-show="commission.referral_payment_id==0"><span class="badge badge-danger">No</span></div>
                                        </div>
                                        <div class="form-group col-lg-12 col-xs-12 text-center" ng-show="!commissions.length">
                                            <label class="control-label">No commissions due</label>
                                        </div>
                                        <dir-pagination-controls
                                                max-size="5"
                                                pagination-id="commission"
                                                direction-links="true"
                                                boundary-links="true" style="float: right" >
                                        </dir-pagination-controls>
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-xs-12" >
                                    <div class="col-sm-7"></div>
                                    <div class="col-sm-5 text-right">
                                        <button ng-hide="hidePay" class="btn btn-success"  ng-click="startPayment(<?php echo $referrer_id?>)" ng-disabled="savingReferrer">Pay Now</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="hpanel">
                    <h5><strong>PAYMENTS</strong></h5>
                    <div class="panel-body">
                        <form name="referrerCommissionsPayments">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group clearfix" ng-init="@if($referrer_id!=0)getPayments(<?php echo $referrer_id?>)@endif">
                                        <div class="col-lg-12  col-xs-12 form-group coupon-payment-detail">
                                            <div class="col-header border-bottom col-lg-12 col-xs-12 no-padding">
                                                <div class="col-xs-3 col-lg-2">
                                                    <a href="#" ng-click="sortTypePayment = 'created_at'; sortReversePayment = !sortReversePayment">
                                                        <span class="tb-sort-header-name">
                                                            <strong>Date</strong>
                                                        </span>
                                                            <span class="tb-sort-icons">
                                                            <i ng-show="sortTypePayment == 'created_at' && !sortReversePayment" class="fa fa-caret-down"></i>
                                                            <i ng-show="sortTypePayment == 'created_at' && sortReversePayment" class="fa fa-caret-up"></i>
                                                            <i ng-show="sortTypePayment != 'created_at'" class="fa fa-sort"></i>
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="col-xs-3 col-lg-2">
                                                    <a href="#" ng-click="sortTypePayment = 'amount'; sortReversePayment = !sortReversePayment">
                                                    <span class="tb-sort-header-name">
                                                        <strong>Commission</strong>
                                                    </span>
                                                        <span class="tb-sort-icons">
                                                        <i ng-show="sortTypePayment == 'amount' && !sortReversePayment" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortTypePayment == 'amount' && sortReversePayment" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortTypePayment != 'amount'" class="fa fa-sort"></i>
                                                    </span>
                                                    </a>
                                                </div>
                                                <div class="col-xs-6 col-lg-8">
                                                    <a href="#" ng-click="sortTypePayment = 'comment'; sortReversePayment = !sortReversePayment">
                                                    <span class="tb-sort-header-name">
                                                        <strong>Notes</strong>
                                                    </span>
                                                        <span class="tb-sort-icons">
                                                        <i ng-show="sortTypePayment == 'comment' && !sortReversePayment" class="fa fa-caret-down"></i>
                                                        <i ng-show="sortTypePayment == 'comment' && sortReversePayment" class="fa fa-caret-up"></i>
                                                        <i ng-show="sortTypePayment != 'comment'" class="fa fa-sort"></i>
                                                    </span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="tb-row col-lg-12 col-xs-12 no-padding" dir-paginate="payment in payments |orderBy:sortTypePayment:sortReversePayment| itemsPerPage: 10" pagination-id="payments">
                                                <div class="tb-cell col-xs-3 col-lg-2 text-left">@{{payment.created_at | date:'MM-dd-yyyy'}}</div>
                                                <div class="tb-cell col-xs-3 col-lg-2 -center">$@{{payment.amount}}</div>
                                                <div class="tb-cell col-xs-6 col-lg-8">@{{payment.comment}}</div>
                                            </div>
                                            <div class="tb-row col-lg-12 col-xs-12 no-padding text-center" ng-show="!payments.length" >
                                                <label class="control-label font-normal">No payments made</label>
                                            </div>
                                            <dir-pagination-controls
                                                    max-size="5"
                                                    pagination-id="payments"
                                                    direction-links="true"
                                                    boundary-links="true" style="float: right" >
                                            </dir-pagination-controls>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <script type="text/ng-template" id="modelPayments.html">
            <div class="color-line"></div>


            <div class='modal-content commission-modal'>
                <div class="modal-header">
                    <h4 class="modal-title">Commission Payments Details </h4>
                </div>
                <div class="modal-body">
                    <form name="save_tasks" class="form-horizontal" novalidate="novalidate" id="save_tasks" action="#" method="post" class="ng-pristine ng-valid">

                        <div class="row">
                            <div class="form-group col-lg-12 col-sm-12">
                                <label class="col-xs-12 control-label text-center"><h4 class="text-center">Due Commissions</h4></label>
                                <div class="col-xs-12 coupon-detail">
                                    <div class="th-border col-lg-12 col-xs-12 no-padding">
                                        <div class="col-xs-8"><label class="control-label"><strong>MJB Name</strong></label></div>
                                        <div class=" col-xs-4"><label class="control-label"><strong>Commission</strong></label></div>
                                    </div>
                                    <div class="col-lg-12 col-xs-12 no-padding" ng-repeat="commission in commissionsToPay.commissions">
                                        <div class="col-xs-8">@{{ commission.mjb_name}}</div>
                                        <div class="col-xs-4">$@{{ commission.commission_amount}}</div>
                                    </div>
                                    <div class="col-lg-12 col-xs-12 no-padding m-t-md" >
                                        <div class="col-xs-8 control-label"><b>Total Payment</b></div>
                                        <div class="col-xs-4"><label class="control-label border-top"><b>$@{{commissionsToPay.amount}}</b></label></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                            <div class="form-group col-xs-12">
                                <label class="col-sm-3">Notes</label>
                                <div class="col-sm-9">
                                    <textarea style="width: 100%" autocomplete="off" ng-model="paymentDetails.note"></textarea>
                                </div>
                            </div>
                        </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" ng-click="complete()">Add Note</button>
                    <button class="btn btn-warning" ng-click="cancel()">Cancel</button>
                </div>
            </div>
        </script>
    </div>
@stop
@section('scripts')
    {!! Html::script('/js/angular/referrals.js') !!}
@stop