<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 5/6/2016
 * Time: 11:24 AM
 */
?>

@extends('layout.dashbord')

@section('content')
    <div class="content animate-panel" xmlns="http://www.w3.org/1999/html">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                    {{--<h3>Add New Appointment</h3>--}}
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2 class="text-center checklist-intro-hd">Generate your checklist</h2>

                                <div class="col-xs-12  col-lg-10 col-lg-offset-1">

                                    <div class="checklist-intro text-center">
                                        @if($auditType == 2)
                                            <p>To generate a checklist for the audit select the license types you'd like to inspect for. The license types you
                                                can select from are what company holds at the location you're inspecting.</p>

                                            <p>Once you've paid for the checklist, you'll be able to log into the iPad app,
                                            download the checklist, and then conduct the audit.</p>
                                        @endif
                                        @if($auditType == 1)
                                        <p>Once you've generated the checklist, you'll be able to log into the iPad app,
                                            download the checklist, and then conduct the audit.</p>
                                        @endif

                                        <p><b>Note:</b> You must be connected to the internet in order to download the checklist
                                        to your iPad app. You should log into the iPad app prior to arriving at the
                                        audit. If wifi is unavailable at the audit location, you will not be
                                        able to log into the app.</p>
                                    </div>
                                    {{--<form action="{{ url('/appointment/store') }}" id="companyAppForm" name="companyAppForm" method="post">--}}
                                    <div class="row">
                                        <div class="col-xs-12 col-lg-10 col-lg-offset-1">
                                            <form id="companyAppForm" name="companyAppForm">
                                                <div class="row">
                                                    <div class="form-group col-xs-12">
                                                        <label class="col-sm-5 control-label text-left">Business
                                                            Name*</label>
                                                        <div class="col-sm-7 text-right">
                                                            <?php
                                                            $status = "";
                                                            $onclick = "";

                                                            if (isset($_GET['id'])) {
                                                                echo "<input type='hidden' name='request_id' value='" . $_GET['id'] . "' />";
                                                            }

                                                            if (isset($company)) {
                                                                $status = "disabled";
                                                                echo "<input type='hidden' name='audit_type' id='audit_type' value='" . $auditType . "' />";
                                                            } else {
                                                                echo "<input type='hidden' name='audit_type' id='audit_type' value='" . $auditType . "' />";
                                                            }
                                                            echo "<input id='cc_ge_foc' type='hidden' value='".$cc_ge_foc."'>"
                                                            ?>
                                                            @if(isset($edit))
                                                                <input type="hidden" id="appointmentID"
                                                                       value="{{$_GET['appointmentId']}}">
                                                            @endif

                                                            <select class="form-control" name="company_name"
                                                                    id="company_name" {{$status}}>
                                                                @if(isset($company_id))
                                                                    <option value="{{ $company_id }}">{{ $company }}</option>
                                                                @elseif(isset($company_list))
                                                                    <option value="">Select...</option>
                                                                    @foreach($company_list as $item)
                                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                                    @endforeach
                                                                @else
                                                                    <option value="">Select</option>
                                                                @endif
                                                            </select>
                                                                @if(isset($manage) && $manage==2)
                                                                    <a style="float: left;color: #337ab7;" href="/appointment/create/nonmjb"  id="new-user-model">Click here if business is not in dropdown list</a>
                                                                @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @if(!isset($hide_location))
                                                <div class="row">
                                                    <div class="form-group col-xs-12">

                                                        <label class="col-sm-5 control-label text-left">Location*</label>

                                                        <div class="col-sm-7 text-right">

                                                            @if(isset($location))
                                                                <select class="form-control" name="company_location"
                                                                        id="company_location" {{$status}}>
                                                                    <option value="{{ $location_id }}">{{ $location }}</option>
                                                                </select>

                                                            @elseif(isset($company_locations))
                                                                <select class="form-control" name="company_location"
                                                                        id="company_location">
                                                                    <option value="">Select...</option>
                                                                    @foreach($company_locations as $company_location)
                                                                        <option value="{{ $company_location->id }}">{{ $company_location->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <select class="form-control" name="company_location"
                                                                        id="company_location" {{$status}}>
                                                                    <option value="">Select...</option>
                                                                </select>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="row hidden">
                                                    <div class="form-group col-xs-12">

                                                        <label class="col-sm-5 control-label text-left">Location*</label>

                                                        <div class="col-sm-7 text-right">

                                                            @if(isset($location))
                                                                <select class="form-control" name="company_location"
                                                                        id="company_location" {{$status}}>
                                                                    <option value="{{ $location_id }}">{{ $location }}</option>
                                                                </select>

                                                            @elseif(isset($company_locations))
                                                                <select class="form-control" name="company_location"
                                                                        id="company_location">
                                                                    <option value="">Select...</option>
                                                                    @foreach($company_locations as $company_location)
                                                                        <option value="{{ $company_location->id }}">{{ $company_location->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <select class="form-control" name="company_location"
                                                                        id="company_location" {{$status}}>
                                                                    <option value="">Select...</option>
                                                                </select>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                                @endif


                                                <div class="row">
                                                    <div class="form-group col-xs-12">
                                                        <label class="col-sm-5 control-label text-left">Comment</label>

                                                        <div class="col-sm-7 text-right">
                                                            @if(isset($comment))
                                                                <textarea class="form-control" name="comment"
                                                                          id="comment" rows="4"
                                                                          disabled="true">{{$comment}}</textarea>
                                                            @else
                                                                <textarea class="form-control" name="comment"
                                                                          id="comment"
                                                                          rows="4"></textarea>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-xs-12">
                                                        <label class="col-sm-5 control-label text-left">Assign
                                                            To*</label>
                                                        <div class="col-sm-7 text-right">
                                                            @if(isset($inspector_id))
                                                                @if($appointmentStatus == 1)
                                                                    @if($userType == 'MasterAdmin')
                                                                        <select class="form-control" name="assign_to"
                                                                                id="assign_person">
                                                                            <option value="{{ $inspector_id}}"
                                                                                    selected>{{ $inspectorName }}</option>
                                                                        </select>
                                                                    @elseif($type == 'third_party' && $userType == 'MJB')
                                                                        <select class="form-control" name="assign_to"
                                                                                id="assign_person">
                                                                            <option value="{{ $inspector_id}}"
                                                                                    selected>{{ $inspectorName }}</option>
                                                                        </select>
                                                                    @else
                                                                        <select class="form-control" name="assign_to"
                                                                                id="assign_person">
                                                                            @foreach($inspector as $item)
                                                                                @if($item->id == $inspector_id)
                                                                                    <option value="{{ $item->id }}"
                                                                                            selected>{{ $item->name }}</option>
                                                                                @else
                                                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    @endif

                                                                @else
                                                                    <select class="form-control" name="assign_to"
                                                                            id="assign_person"
                                                                            disabled="true">
                                                                        @foreach($inspector as $item)
                                                                            @if($item->id == $inspector_id)
                                                                                <option value="{{ $item->id }}"
                                                                                        selected>{{ $item->name }}</option>
                                                                            @else
                                                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                @endif
                                                            @else
                                                                <select class="form-control" name="assign_to"
                                                                        id="assign_person">
                                                                    <option value="">Select</option>
                                                                    @if(isset($inspector))
                                                                        @foreach($inspector as $item)
                                                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-xs-12">
                                                        <label class="col-sm-5 control-label text-left">Audit Date
                                                            &
                                                            Time*</label>
                                                        <div class="col-sm-7 text-right">
                                                            <div class="input-group input-append date"
                                                                 id="startDatePicker"
                                                                 data-date-format="MM/DD/YYYY HH:mm:ss">
                                                                @if(isset($dateTime))
                                                                    <?php $selectedDate = date('m-d-Y H:i:s', strtotime($dateTime));  ?>
                                                                    @if($appointmentStatus == 1)
                                                                        <input type="text" class="form-control"
                                                                               name="startDate"
                                                                               id="startDate"
                                                                               value="{{$selectedDate}}"/>
                                                                    @else
                                                                        <input type="text" class="form-control"
                                                                               name="startDate"
                                                                               id="startDate" value="{{$selectedDate}}"
                                                                               disabled="true"/>
                                                                    @endif
                                                                @else
                                                                    <input type="text" class="form-control"
                                                                           name="startDate"
                                                                           id="startDate"/>
                                                                @endif
                                                                <span class="input-group-addon add-on"><span
                                                                            class="glyphicon glyphicon-calendar"></span></span>
                                                            </div>
                                                            <span id="err-date"></span>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="form-group col-xs-12">
                                                        <label class="col-sm-5 control-label text-left">License
                                                            Types*</label>
                                                        <div class="col-sm-7 text-right">
                                                            @if(isset($masterLicences))
                                                                <select class="form-control padding0"
                                                                        id="license_types_edit"
                                                                        name="license_types_edit" multiple="multiple"
                                                                        disabled="disabled"
                                                                        style="overflow: hidden; height: auto;">
                                                                    @foreach($masterLicences as $masterLicence)
                                                                        <?php $isExists = false ?>
                                                                        @foreach($licenceTypes as $licenceType)
                                                                            @if($masterLicence->id == $licenceType->option_value)
                                                                                <?php $isExists = true; ?> @break;
                                                                            @endif
                                                                        @endforeach
                                                                        @if($isExists)
                                                                            <option value="{{ $masterLicence->id}}"
                                                                                    selected>{{ $masterLicence->name }}</option>

                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <select class="form-control padding0" id="license_types"
                                                                        name="license_types" multiple="multiple"
                                                                        style="overflow: hidden; height: auto;"></select>
                                                            @endif
                                                            <span id="err_license"></span>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </div>


                                                @if((isset($selectedClassifications) && count($selectedClassifications) > 0 && isset($edit)) || (!isset($edit) && count($classifications) > 0))
                                                    <div class="row" id="appointmentClassificationRow">
                                                        <div class="form-group col-xs-12">
                                                            <label class="col-sm-5 control-label text-left">Classifications</label>
                                                            <div class="col-sm-7 text-right">
                                                                <table id="reqClassifictionTable" class="col-sm-12">
                                                                    {{--<thead>--}}
                                                                    {{--<tr>--}}
                                                                    {{--<th class="col-sm-8 text-right">Classification Type</th>--}}
                                                                    {{--<th class="col-sm-4">Options</th>--}}
                                                                    {{--</tr>--}}

                                                                    {{--</thead>--}}
                                                                    <tbody>
                                                                    @if(isset($selectedClassifications))
                                                                        @foreach($selectedClassifications as $selectedClassification)
                                                                            <?php $isExists = false; $classificationName = ""; $value = ""; ?>
                                                                            @foreach($classifications as $classification)
                                                                                @if($selectedClassification->entity_type == $classification['id'])
                                                                                    <?php $isExists = true; $classificationName = $classification['name']; ?>
                                                                                    @foreach($classification['options'] as $option)
                                                                                        @if($option['id'] == $selectedClassification->option_value)
                                                                                            <?php $value = $option['name']; ?>
                                                                                        @endif
                                                                                    @endforeach

                                                                                @endif
                                                                            @endforeach

                                                                            @if($isExists)
                                                                                <tr>
                                                                                    <td class="col-sm-7"
                                                                                        style="text-align: left; padding: 0"> {{ $classificationName }} </td>
                                                                                    <td class="col-sm-5"
                                                                                        style="padding: 0;">
                                                                                        <select class="marginButtom1 col-sm-11 form-control"
                                                                                                disabled="disabled">
                                                                                            <option value="">{{$value}}</option>
                                                                                        </select>
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                        @endforeach

                                                                    @else
                                                                        @foreach($classifications as $classification)
                                                                            @if($classification['status'] == "1")
                                                                                <tr>
                                                                                    <td class="col-sm-7"
                                                                                        style="text-align: left; padding: 0"> {{ $classification['name'] }} </td>
                                                                                    <td class="col-sm-5"
                                                                                        style="padding: 0;">
                                                                                        <select class="marginButtom1 col-sm-11 form-control"
                                                                                                name="req_classification_{{ $classification['id'] }}"
                                                                                                id="req_classification_{{ $classification['id'] }}"
                                                                                                classification-id="{{ $classification['id'] }}">
                                                                                            <option value="">Choose...
                                                                                            </option>
                                                                                            @foreach($classification['options'] as $option)
                                                                                                @if($option["status"] == "1")
                                                                                                    <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                                                                                @endif

                                                                                            @endforeach
                                                                                        </select>
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($auditType == 2)
                                                {{--price box start--}}
                                                <div class="clearfix m-b-lg @if(!isset($edit)) hidden @endif" id="licence_fee_div">
                                                    <div class="col-xs-12">
                                                        <div class="price-box">
                                                            {{--<div class="row">
                                                                <div class="form-group col-lg-10 col-lg-offset-1">
                                                                    <label class="col-xs-7 col-sm-5 control-label light-label text-left">Checklist
                                                                        fee per license:</label>
                                                                    <div class="col-xs-5 col-sm-7 text-right">
                                                                        $XXX.XX

                                                                    </div>
                                                                </div>
                                                            </div>--}}
                                                            @if(!isset($edit))
                                                                <div class="row" >
                                                                    <div class="col-lg-10 col-lg-offset-1">
                                                                        <ul  class="list-block">
                                                                            <li class="table-head"><div class="col-xs-7 col-sm-9 control-label text-left">license Name
                                                                                </div>
                                                                                <div class="col-xs-5 col-sm-3 text-right">
                                                                                    Fee
                                                                                </div></li>
                                                                        </ul>
                                                                    <ul id="licence_fee" class="list-block">

                                                                    </ul>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="row">
                                                                <div class="form-group col-lg-10 col-lg-offset-1">
                                                                    <label class="col-xs-7 col-sm-9 control-label text-left">
                                                                        <div class="m-t"><b>Checklist fee</b></div>
                                                                    </label>
                                                                    <div class="col-xs-5 col-sm-3 text-right">

                                                                        <div class="m-t">
                                                                            @if(isset($cost) && $cost > 0 )
                                                                                <b>$<span id="cost_amount">{{$cost}}</span></b>
                                                                            @elseif(isset($appointmentType) ){{--&& $entity_type != "MJ"--}}
                                                                                <b>$<span id="cost_amount">0</span>&nbsp;<span id="free_label"style="color: #ff0000 "></span></b>
                                                                            @elseif(isset($cc_ge_foc) && $cc_ge_foc==1 && isset($cost)&& $cost==0){{--&& $entity_type != "MJ"--}}
                                                                                <b><span id="free_label"style="color: #ff0000 ">Free</span></b>
                                                                            @endif
                                                                            <input type="text" name="amount_cost"
                                                                                   id="amount_cost" value=""
                                                                                   readonly="true"
                                                                                   style="display: none"/>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{--price box end--}}
                                                @endif
                                                <input type="hidden" name="from_company_id"
                                                       id="from_company_id"
                                                       value="{{ Auth::user()->company_id }}"/>
                                                @if($auditType != 2)
                                                <input type="text" name="amount_cost"
                                                       id="amount_cost" value=""
                                                       readonly="true"
                                                       style="display: none"/>
                                                @endif
                                                @if($auditType != 1 && $cc_data_added == 1)
                                                    <div class="row">
                                                        <div class="form-group col-xs-12">

                                                            <div class="col-sm-12 text-left">
                                                                @if(!isset($edit) && $auditType != 0)
                                                                    <label><input type="checkbox" name="terms" value="1"> I
                                                                        have read &
                                                                        agree to the <a target="_blank"
                                                                                        href="http://simplifya.com/terms-of-service/"><u>Terms
                                                                                of Service</u></a></label>
                                                                @endif
                                                                <span id="err-terms"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="row">
                                                    <div class="form-group col-xs-12">
                                                        @if(isset($edit))
                                                            @if($appointmentStatus == 1)
                                                                @if($reportStatus == 0)
                                                                    @if(isset($from_company_id))
                                                                        @if(Auth::user()->company_id == $from_company_id)
                                                                            <div class="col-xs-12 col-sm-4 pull-right text-right">
                                                                                <button class="btn btn-info btn-xs-12"
                                                                                        id="edit_appointment">
                                                                                    Update Appointment
                                                                                </button>
                                                                            </div>
                                                                            <div class="col-xs-12 col-sm-3 pull-right" style="margin-right: -1%">
                                                                                <button class="btn btn-danger" id="cancel_appointment">Cancel Appointment</button>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="col-xs-12 col-sm-4 pull-right text-right">
                                                                            <button class="btn btn-info btn-xs-12"
                                                                                    id="edit_appointment">Update
                                                                                Appointment
                                                                            </button>
                                                                        </div>
                                                                        <div class="col-xs-12 col-sm-3 pull-right" style="margin-right: -1%">
                                                                            <button class="btn btn-danger" id="cancel_appointment">Cancel Appointment</button>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            @else
                                                                @if(!$auditType == 1)
                                                                    <div class="col-xs-12 col-sm-6 pull-right text-right">
                                                                        <button class="btn btn-info btn-xs-12"
                                                                                id="re_pay_btn"><b>RE PAY NOW</b>
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @else
                                                            @if($auditType == 1)
                                                                <div class="col-xs-12 col-sm-6 pull-right text-right">
                                                                    <button class="btn btn-info btn-xs-12"
                                                                            id="create_appointment">Create
                                                                        Appointment
                                                                    </button>
                                                                </div>
                                                            @elseif(($auditType != 0))
                                                                @if($cc_ge_foc==0)
                                                                @if($cc_data_added == 1)
                                                                    <div class="col-xs-12 col-sm-6 pull-right text-right">
                                                                        <button class="btn btn-info btn-xs-12"
                                                                                id="paynow_btn"><b>PAY NOW</b> & Generate
                                                                            Checklist
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                                    @else
                                                                    <div class="col-xs-12 col-sm-6 pull-right text-right">
                                                                        <button class="btn btn-info btn-xs-12"
                                                                                id="generate_checkist">Generate
                                                                            Checklist
                                                                        </button>
                                                                    </div>
                                                                @endif

                                                                    @if($cc_data_added == 0 && $cc_ge_foc!=1)
                                                                    <!-- if cc or gov company does not save card details -->
                                                                        <form name="payment_form" novalidate id="payment_form" action="#" method="post">
                                                                                <div class="row">
                                                                                    <div class="form-group col-xs-12">
                                                                                        <div class="col-md-12 col-xs-12">
                                                                                            <ul class="cc_icons pull-right" style="margin-bottom: 0px">
                                                                                                <li><img id="cc-amex" src="/images/cards/amex.png" /></li>
                                                                                                <li><img id="cc-visa" src="/images/cards/visa.png" /></li>
                                                                                                <li><img id="cc-discover" src="/images/cards/discover.png" /></li>
                                                                                                <li><img id="cc-mastercard" src="/images/cards/master.png" /></li>
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="form-group col-xs-12">
                                                                                    <label class="col-sm-5 control-label text-left">Card Number</label>
                                                                                    <div class="col-sm-7 text-right">
                                                                                        <input class="form-control" type="text" placeholder="Card Number" name="card_number" id="card_number" >
                                                                                        {{--@if ($errors->has('card_number'))<label class="error" id="err-card_number">{!!$errors->first('card_number')!!}</label>@endif--}}
                                                                                    </div>
                                                                                </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="form-group col-xs-12">
                                                                                        <label class="col-sm-5 control-label text-left">CCV Number</label>
                                                                                        <div class="col-sm-7 text-right">
                                                                                            <input class="form-control" type="text" placeholder="CCV Number" name="ccv_number" id="ccv_number">
                                                                                            {{--@if ($errors->has('ccv_number'))<label class="error">{!!$errors->first('ccv_number')!!}</label>@endif--}}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="row">
                                                                                    <div class="form-group col-xs-12">
                                                                                        <label class="col-sm-5 control-label text-left">Expiration Month</label>
                                                                                        <div class="col-sm-7 text-right">
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
                                                                                            {{--@if ($errors->has('exp_month'))<label class="error">{!!$errors->first('exp_month')!!}</label>@endif--}}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                    <div class="row">
                                                                                    <div class="form-group col-xs-12">
                                                                                        <label class="col-sm-5 control-label text-left">Expiration Year</label>
                                                                                        <div class="col-sm-7 text-right">
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


                                                                            <input type="hidden" class="form-control" id="payment_entity_type" readonly>
                                                                            <input type="hidden" class="form-control" id="payment_business_name" readonly>
                                                                            <input type="hidden"class="form-control" id="payment_no_of_license" readonly>
                                                                            <input type="hidden" class="form-control" name="payment_subscription_fee" id="payment_subscription_fee"readonly>
                                                                            <input type="hidden" id="payment_type" name="payment_type" value="subscription">
                                                                            <input type="hidden" id="entity_type" name="entity_type" value="{{$entity_type}}">
                                                                            <div class="row" id="term_condition" style="display: block">
                                                                                <div class="form-group col-xs-12 border-top terms">
                                                                                    <div class="checkbox checkbox-single checkbox-success col-md-12">
                                                                                        <label><input type="checkbox" name="terms" value="1"> I have read & agree to the <a target="_blank" href="http://simplifya.com/terms-of-service/"><u>Terms of Service</u></a></label>
                                                                                        <br><span id="err-terms"></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="form-group">
                                                                                    <div class="col-xs-12 col-sm-6 pull-left text-left">
                                                                                        <a href="/appointment" class="btn btn-default btn-xs-12">Back</a>
                                                                                    </div>
                                                                                    <div class="col-sm-6 text-right">
                                                                                        <button type="button" class="btn btn-pay" id="appointment_payment" ><b>PAY NOW</b> & Generate
                                                                                            Checklist</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                        <!-- end -->
                                                                    @endif
                                                            @endif
                                                        @endif
                                                            <div class="col-xs-12 col-sm-4 pull-left text-left">

                                                            @if(isset($hide_location))
                                                                <a href="/appointment/edit/nonmjb/{{$company_id}}" class="btn btn-success btn-xs-12">Back</a>
                                                            @else
                                                                @if($cc_data_added != 0)
                                                                    <a href="/appointment" class="btn btn-default btn-xs-12">Back</a>
                                                                @endif
                                                            @endif
                                                            </div>
                                                    </div>
                                                </div>
                                                @if($auditType != 1 && $cc_ge_foc!=1)
                                                    <div class="clearfix">
                                                        <div class="row">
                                                            <div class="form-group col-lg-12 m-t">
                                                                <div class="col-md-12 card-container">
                                                                    <div class="card-images"><img src="../images/cards.png">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </form>
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
    {!! Html::script('js/appointment/appointmentManage.js') !!}
@stop
