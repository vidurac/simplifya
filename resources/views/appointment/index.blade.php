@extends('layout.dashbord')

@section('content')
    <div class="normalheader transition animated fadeIn">
        <div class="hpanel">
            <div class="panel-body">
                <a class="small-header-action" href="">
                    <div class="clip-header">
                        <i class="fa fa-arrow-up"></i>
                    </div>
                </a>

                @if($type != "admin")
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group text-left">
                                <a href="/appointment/create?manage=2" class="btn btn-info" id="new-user-model">@if((Auth::user()->master_user_group_id == 2) || (Auth::user()->master_user_group_id == 3)) Create New Self-Audit @else Add New Appointment @endif</a>
                            </div>

                        </div>
                    </div>
                @endif

                <input type="hidden" id="entityType" value="{{$type}}">
                <div class="row">
                    <form id="eventForm" class="form-horizontal">
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <div class="input-group input-append date" id="fromDateDatePicker">
                                        <input type="text" class="form-control" name="fromDate" id="fromDate" placeholder="Date From"  />
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                    <span id="err-date"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <div class="input-group input-append date" id="toDateDatePicker">
                                        <input type="text" class="form-control" name="toDate" id="toDate" placeholder="Date To" />
                                        <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                    <span id="err-date"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    @if($isMjDisabled)
                                        <select class="form-control" id="mjBusiness" placeholder="MJ Business" disabled="true">
                                            @foreach($mjBusinesses as $option)
                                                @if($companyId == $option->id)
                                                    <option value="{{$option->id}}"> {{$option->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    @else
                                        <select class="form-control" id="mjBusiness" placeholder="MJ Business">
                                            <option value=""> Select...</option>
                                            @foreach($mjBusinesses as $option)
                                                <option value="{{$option->id}}"> {{$option->name}}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    @if($isCompanyDisabled)
                                        <select class="form-control" id="companyName" placeholder="Company Name" disabled="true">
                                            @foreach($companies as $option)
                                                @if($companyId == $option->id)
                                                    <option value="{{$option->id}}"> {{$option->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    @else
                                        <select class="form-control" id="companyName" placeholder="Company Name">
                                            <option value=""> Select...</option>
                                            @foreach($companies as $option)
                                                <option value="{{$option->id}}"> {{$option->name}}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <input type="hidden" value="1" id="status">


                        <div class="col-md-1">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <a name="searchAppointments" id="searchAppointments" class="btn btn-default"><i class="fa fa-search"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>



                <div class="row" style="margin-bottom: 5%;">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-condensed table-hover" id="appointment-detail-table">
                                <thead>
                                <tr>
                                    {{--<th>#</th>--}}
                                    <th>Audit Date</th>
                                    <th>MJ Business Name</th>
                                    <th>Company Name</th>
                                    <th>Inspector</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>View</th>
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

    {!! Html::script('/js/appointment/appointment.js') !!}
@stop