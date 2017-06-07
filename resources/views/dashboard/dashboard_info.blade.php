<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 6/17/2016
 * Time: 11:40 AM
 */?>
@extends('layout.dashbord')

@section('content')

    @if (Session::has('error'))
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>{{Session::get('error')}}</strong>
        </div>
    @endif

    @if (Session::has('success'))
        <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>{{Session::get('success')}}</strong>
        </div>
    @endif

    <div class="content" ng-controller="notifications" ng-cloak="">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                    {{--<h3>Inspection Request Manager</h3>--}}
                </div>

                <div class="hpanel hblue" ng-show="!(reportNotofication | isEmpty)">
                    <div class="panel-body">
                        <div class="notification-row" ng-repeat="notification in reportNotofication"
                             ng-click="readReportNotification(notification.id);">
                            {{--<a href="/report/edit/@{{ notification.appointmentId }}"> Inspection report is ready <b>INSPECTION NO :</b> @{{ notification.inspection_number }} <b>LOCATION : </b> @{{ notification.location }} </a> <br/>--}}
                            <a href="/report/edit/@{{ notification.appointment_action_item_comments_id }}"> Audit
                                report is ready <b>AUDIT NO :</b> @{{ notification.inspection_number }} <b>LOCATION
                                    : </b> @{{ notification.location }} </a> <br/>
                        </div>
                    </div>
                </div>
                @if ($role == Config::get('simplifya.MjbMasterAdmin'))
                        <!-- new panel start-->
                @if($module_id == null && $module_id != 'WELCOME')
                    <div class="hpanel hblue" ng-show="!(licenses | isEmpty)">
                        <div id="congrats" class=" full-width-table">
                            <div class="content no-padding">
                                <div class="clearfix">
                                    <div class="hpanel">
                                        <div class="panel-body text-center">
                                            <div class="inner-container">
                                                <h2>Congratulations!</h2>
                                     <span class="col-md-10 col-md-offset-1">
                        <span class="intro"><p>You're officially ready to start auditing your business! To create or
                                request an audit, simply click Audit in the left menu.
                                Compliance is an ongoing task, not a snapshot. Auditing your business with Simplifya at
                                least once a month will help ensure you’re consistently operating in compliance with
                                applicable regulations, and hiring a 3rd party to audit your business(es) using
                                Simplifya at least quarterly will have the added benefit of providing an unbiased
                                perspective.
                            </p>
                        <span class="center-block"><p>Ongoing audits with Simplifya will help prepare you for an audit
                                by your marijuana regulatory body.</p></span>
                            </span>
                        <a class="btn btn-orange closebox" id="welcom_module">Close</a>
                                 </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                            <!-- new panel end-->
                    @if($module_id != null && $module_id == 'WELCOME')
                        <div class="hpanel hblue" ng-show="!(licenses | isEmpty)">
                            <div class="panel-heading hbuilt">
                                <div class="panel-tools">
                                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                    {{--<a class="closebox"><i class="fa fa-times"></i></a>--}}
                                </div>
                                License Expirations
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-hover summary-table">
                                                <thead>
                                                <tr>
                                                    <th>License Type</th>
                                                    <th>License Number</th>
                                                    <th>Location</th>
                                                    <th>Renewal Date</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="license in licenses">
                                                    <td>@{{ license.license}}</td>
                                                    <td>@{{ license.license_number}}</td>
                                                    <td>@{{ license.location}}</td>
                                                    <td>
                                                        <span ng-if="license.remaining > 0">Remaining @{{ license.remaining }}
                                                            days</span>
                                                        <span ng-if="license.remaining == -1">Expired</span>
                                                        <span ng-if="license.remaining == -2">Not defined</span>
                                                        <span ng-if="license.remaining == 0">Expires today</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                        @if ($role == Config::get('simplifya.CcMasterAdmin') || $role == Config::get('simplifya.GeMasterAdmin'))
                                <!-- new panel start-->
                        @if($module_id == null && $module_id != 'WELCOME')

                            <div class="hpanel hblue">
                                <div id="congrats" class=" full-width-table">
                                    <div class="content no-padding">
                                        <div class="clearfix">
                                            <div class="hpanel">
                                                <div class="panel-body text-center">
                                                    <div class="inner-container">
                                                        <h2>Congratulations!</h2>
                                                        <span class="col-md-10 col-md-offset-1">
                                                            <span class="intro"><p>Your business is now ready to conduct
                                                                    audits on Simplifya!</p></span>
                                                            <a class="btn btn-orange closebox"
                                                               id="welcom_module">Close</a>
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

                    @if($role == Config::get('simplifya.MjbManager') || $role == Config::get('simplifya.MjbEmployee') )
                        <div class="hpanel hblue" ng-show="!(rosters | isEmpty)">
                            <div class="panel-heading hbuilt">
                                <div class="panel-tools">
                                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                    {{--<a class="closebox"><i class="fa fa-times"></i></a>--}}
                                </div>
                                Checklist Jobs
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-condensed summary-table">
                                                <thead>
                                                <tr>
                                                    <th>Checklist Name</th>
                                                    <th>Frequency</th>
                                                    <th>Options</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="roster in rosters">
                                                    <td>@{{ roster.name }}</td>
                                                    <td ng-switch="roster.frequency">
                                                        <span ng-switch-when="1">Daily</span>
                                                        <span ng-switch-when="7">Weekly</span>
                                                        <span ng-switch-when="14">Bi-Weekly</span>
                                                        <span ng-switch-when="15">Semi-monthly</span>
                                                        <span ng-switch-when="30">Monthly</span>
                                                    </td>
                                                    <td><a href="#" ng-click="startTask(roster.id,roster.roster_id)"
                                                           class="btn btn-danger">Start Task</a></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($role == Config::get('simplifya.MasterAdmin'))
                        <div class="hpanel hblue">
                            <div class="panel-heading hbuilt">
                                <div class="panel-tools">
                                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                    {{--<a class="closebox"><i class="fa fa-times"></i></a>--}}
                                </div>
                                Pending Approval
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-hover" id="company-pending-table">
                                                <thead>
                                                <tr>
                                                    {{--<th>#</th>--}}
                                                    <th>Date</th>
                                                    <th>Business Name</th>
                                                    <th>Entity Type</th>
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

                    <div class="hpanel hblue">
                        <div class="panel-heading hbuilt">
                            <div class="panel-tools">
                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                {{--<a class="closebox"><i class="fa fa-times"></i></a>--}}
                            </div>
                            Pending Referral Commissions
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-condensed table-hover commission-table"
                                               id="company-commission-table">
                                            <thead>
                                            <tr>
                                                <th width="70%">Referral's Name</th>
                                                {{--<th width="70%">Subscription Name</th>--}}
                                                <th>Commission</th>
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

                        <div class="hpanel hblue">
                            <div class="panel-heading hbuilt">
                                <div class="panel-tools">
                                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                    {{--<a class="closebox"><i class="fa fa-times"></i></a>--}}
                                </div>
                                Registered Active Companies
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-hover summary-table"
                                                   id="company-summary-table">
                                                <thead>
                                                <tr>
                                                    <th width="70%">COMPANY TYPE</th>
                                                    <th>NO OF COMPANIES</th>
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
                    @endif

                    <div class="hpanel hblue" ng-show="!(notifications | isEmpty)">
                        <div class="panel-heading hbuilt">
                            <div class="panel-tools">
                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                {{--<a class="closebox"><i class="fa fa-times"></i></a>--}}
                            </div>
                            Action Item Notifications
                        </div>
                        <div class="forum-box">
                            <div class="panel-body">
                                <div class="forum-comments" style="background: #fff; margin: 0; border: none">
                                    <div class="media notification-row" ng-repeat="notification in notifications"
                                         ng-click="readNotification(notification.id, notification.appointment_id);">
                                        <a class="pull-left">
                                            <img src="<?php echo Config::get('simplifya.BUCKET_IMAGE_PATH') . ltrim(Config::get('simplifya.PROFILE_IMG_DIR'), "/") . '/' ?>@{{notification.profile_pic}}"
                                                 alt="profile-picture" class="desaturate"
                                                 ng-if="notification.profile_pic != null">
                                            <img src="{{ Config::get('simplifya.BUCKET_IMAGE_PATH') . ltrim(Config::get('simplifya.PROFILE_IMG_DIR'), "/"). '/' .Config::get('simplifya.DEFAULT_PROFILE_IMAGE') }}"
                                                 alt="profile-picture" class="desaturate"
                                                 ng-if="notification.profile_pic == null">
                                        </a>

                                        <div class="media-body">
                                            <span class="">@{{ notification.name }} commented on an Action Item</span>
                                            <div>Audit #@{{ notification.inspection_number }}</div>
                                            <div class="font-bold social-content" style="font-style: italic;">
                                                @{{ notification.actionItemName }}
                                            </div>
                                            <div class="social-content">
                                                "@{{ notification.content }}"
                                            </div>
                                            <small class="text-muted"> @{{ notification.created_at | jsDate | date:'short'}}</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hpanel hblue" ng-show="!(action_items | isEmpty)">
                        <div class="panel-heading hbuilt">
                            <div class="panel-tools">
                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                {{--<a class="closebox"><i class="fa fa-times"></i></a>--}}
                            </div>
                            My Action Items
                        </div>
                        <div class="forum-box">
                            <div class="panel-body">
                                <div class="forum-comments" style="background: #fff; margin: 0; border: none">
                                    <div class="media notification-row" ng-repeat="action_item in action_items">
                                        <a class="pull-left">
                                            <i class="fa fa-clock-o" aria-hidden="true" style="font-size: 35px;"></i>
                                        </a>

                                        <div class="media-body">
                                            <a class="font-bold"
                                               href="{{URL('report/edit') }}/@{{ action_item.appointment_id }}">Audit
                                                #@{{ action_item.question_action_items[0].inspection_number }}</a>

                                            <div class="text-muted"
                                                 ng-repeat="question_action_item in action_item.question_action_items">
                                                @{{ question_action_item.name}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hpanel hblue" ng-show="!(appointments | isEmpty)">
                        <div class="panel-heading hbuilt">
                            <div class="panel-tools">
                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                {{--<a class="closebox"><i class="fa fa-times"></i></a>--}}
                            </div>
                            Upcoming Audit Appointments
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-condensed table-hover summary-table">
                                            <thead>
                                            <tr>
                                                <th>Audit Date</th>
                                                <th>Location</th>
                                                <th>Company Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr ng-repeat="appointment in appointments">
                                                <td>@{{ appointment.inspection_date_time | jsDate | date:'MM/dd/yyyy' }}</td>
                                                <td>@{{ appointment.name }}</td>
                                                <td>@{{ appointment.company}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($role == Config::get('simplifya.CcMasterAdmin'))
                        <div class="hpanel hblue" ng-show="!(requests | isEmpty)">
                            <div class="panel-heading hbuilt">
                                <div class="panel-tools">
                                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                    {{--<a class="closebox"><i class="fa fa-times"></i></a>--}}
                                </div>
                                Pending Audit Requests
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-hover summary-table">
                                                <thead>
                                                <tr>
                                                    <th>MJ. Business Name</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="request in requests">
                                                    <td width="70%">@{{ request.name}}</td>
                                                    <td><a href="/request/edit/@{{ request.id}}"
                                                           class="btn btn-sm btn-warning" type="submit">View</a>
                                                        <a href="/appointment/create?manage=1&id=@{{ request.id}}"
                                                           class="btn btn-sm btn-success" type="submit">Make
                                                            Appointment</a>
                                                        <a href="/request/process?manage=3&id=@{{ request.id}}"
                                                           class="btn btn-sm btn-danger"
                                                           type="submit">Reject</a>@{{ appointment.name }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


            </div>
        </div>
        <script type="text/ng-template" id="modelTasks.html">
            <div class="color-line"></div>


            <div class='modal-content'>
                <div class="modal-header">
                    <h4 class="modal-title">Task List</h4>
                </div>
                <div class="modal-body">
                    <form name="save_tasks" class="form-horizontal" novalidate="novalidate" id="save_tasks" action="#"
                          method="post" class="ng-pristine ng-valid">

                        <row class="" ng-repeat="userTask in userTasks">
                            <div class="form-group">
                                <label class="control-label col-sm-8" size="20"
                                       style="text-align: left">@{{ userTask.name }}</label>
                                <div class="col-sm-4">
                                    <input type="checkbox" ng-true-value="'1'" ng-false-value="'0'" class=""
                                           ng-model="userTask.status">
                                </div>
                            </div>
                        </row>

                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" ng-click="save()">Save</button>
                    <button class="btn btn-success" ng-click="complete()">Save & Complete</button>
                    <button class="btn btn-warning" ng-click="cancel()">Cancel</button>
                </div>
            </div>
        </script>
    </div>

    <div class="modal fade in" id="company-details-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Company Details</h4>
                </div>
                <div class="panel-body">
                    <div class="text-center m-b-md" id="wizardControl">

                        <a class="btn btn-primary" href="#step1" data-toggle="tab" id="basic_info_tab">Basic Info</a>
                        <a class="btn btn-default" href="#step2" data-toggle="tab" id="business_location_tab">Business
                            Locations</a>
                        <a class="btn btn-default license-show" href="#step3" data-toggle="tab" id="license_tab">Licenses</a>
                        <a class="btn btn-default" href="#step4" data-toggle="tab" id="employee_tab">Employees</a>

                    </div>

                    <div class="tab-content">
                        <div id="step1" class="p-m tab-pane active">
                            <div class="row">
                                <div class="form-group col-lg-10 col-sm-offset-2">
                                    <label class="col-sm-4 control-label">Entity Type</label>
                                    <div class="col-sm-6">
                                        <input name="company_entity_type" id="company_entity_type" class="form-control"
                                               type="text" placeholder="Entity Type" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-10 col-sm-offset-2">
                                    <label class="col-sm-4 control-label">Name of Business / Entity</label>
                                    <div class="col-sm-6">
                                        <input class="form-control" type="text" placeholder="Name of Business / Entity "
                                               id="name_of_business" value="" name="name_of_business" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-10 col-sm-offset-2">
                                    <label class="col-sm-4 control-label">Company Registration No</label>
                                    <div class="col-sm-6">
                                        <input class="form-control" type="text" placeholder="Company Registration No "
                                               id="company_registration_no" value="" name="company_registration_no"
                                               readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="step2" class="p-m tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="hpanel hblue">
                                        <div class="panel-heading hbuilt hbuilt">
                                            <div class="panel-tools">
                                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                            </div>
                                            Business Locations
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table cellpadding="1" cellspacing="1"
                                                       class="table table-bordered table-striped"
                                                       id="business_location_tbl">
                                                    <thead>
                                                    <tr>
                                                        <th>Location Name</th>
                                                        <th>Address</th>
                                                        {{--<th>Country</th>--}}
                                                        <th>City</th>
                                                        <th>State</th>
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
                                    <div class="hpanel hblue">
                                        <div class="panel-heading hbuilt hbuilt">
                                            <div class="panel-tools">
                                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                            </div>
                                            Licenses
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table cellpadding="1" cellspacing="1"
                                                       class="table table-bordered table-striped" id="licenses_table">
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
                                    <div class="hpanel hblue">
                                        <div class="panel-heading hbuilt hbuilt">
                                            <div class="panel-tools">
                                                <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                            </div>
                                            Employees
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table cellpadding="1" cellspacing="1"
                                                       class="table table-bordered table-striped" id="employe-table">
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
                    <button type="button" class="btn btn-primary pull-right" data-dismiss="modal" id="suspend-company"
                            style="display: none; margin: 3px;">Suspend
                    </button>
                    <button type="button" class="btn btn-success pull-right" data-dismiss="modal"
                            id="approve-pending-company" style="display: none; margin: 3px;">Approve
                    </button>
                    <button type="button" class="btn btn-danger pull-right" id="reject-company"
                            style="display: none; margin: 3px;">Reject
                    </button>
                    <a class="btn btn-default pull-right" data-dismiss="modal" style="margin: 3px;">Close</a>
                </div>
            </div>
        </div>
    </div>

    {!! Html::script('/js/company/company-manager.js') !!}

@stop
@section('scripts')
    {!! Html::script('/js/angular/notifications.js') !!}
@stop


