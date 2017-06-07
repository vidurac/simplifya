<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/6/2016
 * Time: 11:24 AM
 */
?>

@extends('layout.default')

@section('content')
<div class="content">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <div class="text-center m-b-md">
                    <h3>Sign Up</h3>
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                            <div class="text-center m-b-md" id="wizardControl">

                                @if($entity_type != 2 && $cc_ge_subscription == 1)
                                    <lable class="btn btn-primary mouse-over-disable"   id="tab1" >Step 1 - Basic information</lable>
                                    <lable class="btn btn-default mouse-over-disable"   id="tab2" >Step 2 - Payment information</lable>
                                @endif

                            </div>

                            <div class="tab-content">
                                <div id="step1" class="p-m tab-pane active">
                                    <form id="companyRegForm" name="companyRegForm">
                                        {{--<div class="row">
                                            <div class="form-group col-lg-12">
                                                <label class="col-sm-4 control-label">Entity Type</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" name="entity_type" id="entity_type">
                                                        <option value="">Select Entity Type</option>
                                                        @foreach ($entities as $entity)
                                                            <option value={{$entity->id}}>{{$entity->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" value="{{$cc_ge_subscription}}" name="cc_ge_subscription" id="cc_ge_subscription">
                                                    @if ($errors->has('entity_type'))<label class="error">{!!$errors->first('entity_type')!!}</label>@endif
                                                </div>
                                            </div>
                                        </div>--}}

                                        <input type="hidden" value="{{$cc_ge_subscription}}" name="cc_ge_subscription" id="cc_ge_subscription">
                                        <input type="hidden" id="entity_type" name="entity_type" value="{{$entity_type}}">
                                        @if (isset($ref_token))
                                        <input type="hidden" id="ref_token" name="ref_token" value="{{$ref_token}}">
                                        @endif

                                        <div class="row">
                                            <div class="form-group col-lg-12 required">
                                                <label class="col-sm-4 control-label">Name of Business / Entity</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" placeholder="Name of Business / Entity " id="name_of_business" name="name_of_business">
                                                    @if ($errors->has('name_of_business'))<label class="error">{!!$errors->first('name_of_business')!!}</label>@endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 required">
                                                <label class="col-sm-4 control-label">FEIN</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" placeholder="FEIN " id="company_registration_no" name="company_registration_no">
                                                    @if ($errors->has('company_registration_no'))<label class="error">{!!$errors->first('company_registration_no')!!}</label>@endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 required">
                                                <label class="col-sm-4 control-label">Your Name</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" placeholder="Your Name" name="your_name" id="your_name" autocomplete="off">
                                                    @if ($errors->has('email'))<label class="error">{!!$errors->first('your_name')!!}</label>@endif
                                                </div>
                                            </div>
                                        </div>
                                        {{--<div class="row">--}}
                                            {{--<div class="form-group col-lg-12">--}}
                                                {{--<label class="col-sm-12 control-label">Will you be the Master Administrator for this business account? </label>--}}
                                                {{--<div class="col-sm-4">--}}

                                                {{--</div>--}}
                                                {{--<div class="col-sm-8">--}}
                                                    {{--<label><input type="radio" name="is_master" class="i-checks" value="1" checked> Yes </label>--}}
                                                    {{--<label><input type="radio" name="is_master" class="i-checks" value="0" > No </label>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        <div class="row">
                                            <div class="form-group col-lg-12 required">
                                                <label class="col-sm-4 control-label">Email Address</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="email" placeholder="Email Address" name="email" id="email" autocomplete="off">
                                                    @if ($errors->has('email'))<label class="error">{!!$errors->first('email')!!}</label>@endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 required">
                                                <label class="col-sm-4 control-label">Confirm Email Address</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="email" placeholder="Confirm Email Address" name="conf_email" id="conf_email" autocomplete="off">
                                                    @if ($errors->has('email'))<label class="error">{!!$errors->first('conf_email')!!}</label>@endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 required">
                                                <label class="col-sm-4 control-label">Password </label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="password" placeholder="Password" name="password" id="password">
                                                    @if ($errors->has('email'))<label class="error">{!!$errors->first('password')!!}</label>@endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 required">
                                                <label class="col-sm-4 control-label">Confirm Password </label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="password" placeholder="Confirm Password" name="conf_password" id="conf_password">
                                                    @if ($errors->has('email'))<label class="error">{!!$errors->first('conf_password')!!}</label>@endif
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="text-right m-t-xs col-md-12 col-xs-12">
                                        @if($entity_type != 2 && $cc_ge_subscription == 1)
                                            <a class="btn btn-primary next" id='next' href="#">Next</a>
                                        @endif
                                        @if($entity_type == 2 || $cc_ge_subscription == 0)
                                            <a class="btn btn btn-primary pull-right" id="reg-without-pay">Sign Up</a>
                                        @endif

                                    </div>

                                </div>
                                @if($entity_type != 2 && $cc_ge_subscription == 1)
                                <div id="step2" class="p-m tab-pane">
                                    <form id="paymentRegForm" name="paymentRegForm">
                                        <div class="row" id="initial_pay">
                                            <div class="form-group col-lg-12">
                                                <div class="col-lg-12 m-b">
                                               <h3><b>Add a payment method</b></h3>
                                               <p>Your card will not be charged now. Your card will only be charged after you log into Simplifya and complete the information about your business(es).</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 required">
                                                        <div class="col-md-12 col-xs-12">
                                                            <ul class="cc_icons pull-right" style="margin-bottom: 0px">
                                                                <li><img id="cc-amex" src="/images/cards/amex.png" /></li>
                                                                <li><img id="cc-visa" src="/images/cards/visa.png" /></li>
                                                                <li><img id="cc-discover" src="/images/cards/discover.png" /></li>
                                                                <li><img id="cc-mastercard" src="/images/cards/master.png" /></li>
                                                            </ul>
                                                        </div>


                                                <label class="col-sm-4 control-label required">Card Number</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" placeholder="Card Number" name="card_number" id="card_number" >
                                                    @if ($errors->has('email'))<label class="error">{!!$errors->first('card_no')!!}</label>@endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-lg-12 required">
                                                <label class="col-sm-4 control-label ">CCV Number</label>
                                                <div class="col-sm-8">
                                                    <input class="form-control" type="text" placeholder="CCV Number" name="ccv_number" id="ccv_number">
                                                    @if ($errors->has('ccv_number'))<label class="error">{!!$errors->first('ccv_number')!!}</label>@endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 required">
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
                                            <div class="form-group col-lg-12 required">
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
                                        <div id="display-only-cc-ge" style="display: none">
                                            {{--<div class="row">--}}
                                            {{--<div class="form-group col-lg-12">--}}
                                            {{--<label class="col-sm-4 control-label">Subscribtion duration</label>--}}
                                            {{--<div class="col-sm-8">--}}
                                            {{--<select class="form-control" name="subs_duration" id="subs_duration">--}}
                                            {{--<option value="">Select subscribtion duration</option>--}}
                                            {{--<option value="1">Monthly</option>--}}
                                            {{--<option value="2">Quarterly</option>--}}
                                            {{--<option value="3">Half yearly</option>--}}
                                            {{--<option value="4">Yearly</option>--}}
                                            {{--</select>--}}
                                            {{--@if ($errors->has('email'))<label class="error">{!!$errors->first('entity_type')!!}</label>@endif--}}
                                            {{--</div>--}}
                                            {{--</div>--}}
                                            {{--</div>--}}
                                            <div class="row">
                                                <div class="form-group col-lg-12">
                                                    <label class="col-sm-4 control-label">Subscription Fee</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" value="" id="subscrib_fee" name="subscrib_fee" class="form-control" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-12 m-t ">
                                                    <div class="col-sm-8 control-label ">
                                                        <input type="checkbox" name="terms" id="terms" value="1"> I have read & agree to the <a target="_blank" href="http://simplifya.com/terms-of-service/"><u>Terms of Service</u></a></div>
                                                    <span id="terms-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12">
                                                <div class="col-sm-6 col-xs-6 col-md-6">
                                                    <a class="btn btn-default prev" id="previous">Previous</a>
                                                </div>
                                                <div class="col-sm-6 col-xs-6 col-md-6 text-right">
                                                    <a class="btn btn btn-primary next pull-right" id="reg-btn">Sign Up</a>
                                                    @if($entity_type != 2 && $cc_ge_subscription != 1)
                                                        <a class="btn btn-pay next pull-right" id="pay-now-btn">Pay Now</a>
                                                    @else
                                                        <a class="btn btn-pay next pull-right" id="pay-now-btn">Pay Now & Sign up</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 m-t">
                                                <div class="col-md-12 card-container">
                                                    <div class="card-images"><img src="../images/cards.png"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                    @endif
                            </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
        {!! Html::script('js/initial-signup.js') !!}

        <script>
            // Toastr options
            toastr.options = {
                "debug": false,
                "newestOnTop": false,
                "positionClass": "toast-top-center",
                "closeButton": true,
                "toastClass": "animated fadeInDown",
            };

            var msg = '<?php if($errors->has()) {foreach ($errors->all() as $error){ echo $error; } $has_error = true;}else{ $has_error = false;}?>';
            var has_error = '<?php echo $has_error?>';
            if(has_error) {
                toastr.error(msg);
            }

        </script>

@stop
