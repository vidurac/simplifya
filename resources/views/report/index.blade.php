@extends('layout.dashbord')

@section('content')
    <div class="content fadeIn">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <a class="small-header-action" href="">
                            <div class="clip-header">
                                <i class="fa fa-arrow-up"></i>
                            </div>
                        </a>

                        <div class="row">
                            <form id="reportForm" class="form-horizontal">
                                <input type="hidden" id="entityType" value="{{$type}}">
                                <div class="col-lg-12">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="dateContainer col-xs-12">
                                                <div class="input-group input-append date" id="startDatePicker">
                                                    <input type="text" class="form-control" name="startDate" id="startDate" placeholder="Start Date" />
                                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="dateContainer col-xs-12">
                                                <div class="input-group input-append date" id="endDatePicker">
                                                    <input type="text" class="form-control" name="endDate" id="endDate" placeholder="End Date" />
                                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                                <span id="date_err"></span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($isMjDisabled)
                                        <div class="col-md-2" style="display: none">
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <select class="form-control" id="mjBusiness" placeholder="MJ Business" disabled="true">
                                                        <option value="">Select Marijuana Company</option>
                                                        @foreach($mjBusinesses as $option)
                                                            <option value="{{$option->id}}"> {{$option->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @if($isCompanyDisabled)
                                            <div class="col-md-3">
                                        @else
                                            <div class="col-md-2">
                                        @endif
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <select class="form-control" id="mjBusiness" placeholder="MJ Business">
                                                        <option value="">Select Marijuana Company</option>
                                                        @foreach($mjBusinesses as $option)
                                                            <option value="{{$option->id}}"> {{$option->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($isCompanyDisabled)
                                        <div class="col-md-3" style="display: none">
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <select class="form-control" id="companyName" placeholder="Company Name">
                                                        <option value="">Select Compliance Company</option>
                                                        @foreach($companies as $option)
                                                            @if($companyId == $option->id)
                                                                <option value="{{$option->id}}"> {{$option->name}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @if($isMjDisabled)
                                            <div class="col-md-3">
                                        @else
                                            <div class="col-md-2">
                                        @endif
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <select class="form-control" id="companyName" placeholder="Company Name">
                                                        <option value="">Select Compliance Company</option>
                                                        @foreach($companies as $option)
                                                            <option value="{{$option->id}}"> {{$option->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    @endif

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" id="audit_type">
                                                    <option value="">Audit Type</option>
                                                    <option value="1">Self-audit</option>
                                                    <option value="2">3rd Party</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <select class="form-control" id="status">
                                                    <option value="">Status</option>
                                                    <option value="0">Pending</option>
                                                    <option value="1">Synced</option>
                                                    <option value="3">Finalized</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group text-right">
                                            <div class="col-xs-12">
                                                <a name="searchAppointments" id="searchAppointments" class="btn btn-default"><i class="fa fa-search"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-hover" id="report-detail-table">
                                        <thead>
                                        <tr>
                                            <th>Audit Date</th>
                                            <th>Audit No</th>
                                            <th>MJ Business Name</th>
                                            <th>Location</th>
                                            {{--<th>Audit Type</th>--}}
                                            <th>Auditing Party</th>
                                            <th>Auditor</th>
                                            <th>Updated</th>
                                            <th>Status</th>
                                            <th>View</th>
                                            {{--<th>Export</th>--}}
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

    {!! Html::script('/js/reports/reports.js') !!}
@stop