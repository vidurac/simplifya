<div id="request-table" class="req_tab_display">

    <div class="row">
        <div class="col-xs-12">
            <div class="col-md-2 form-group">
                <a href="/request/create" class="btn btn-info">@if((Auth::user()->master_user_group_id == 2) || (Auth::user()->master_user_group_id == 3)) Request a 3rd Party Audit @else New Request @endif</a>
            </div>
        </div>
    </div>

    <div class="row">
        <form id="eventForm" class="form-horizontal">
            <div class="col-lg-12">
                <div class="col-md-2">
                    <div class="form-group">
                        <div class="dateContainer col-xs-12">
                            <div class="input-group input-append date" id="startDatePicker">
                                <input type="text" class="form-control" name="startDate" id="startDate" />
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <div class="dateContainer col-xs-12">
                            <div class="input-group input-append date" id="endDatePicker">
                                <input type="text" class="form-control" name="endDate" id="endDate" />
                                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                            <span id="date_err"></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <div class="col-xs-12">
                            <select class="form-control" name="business" id="business">
                                <option value="">Select MJB</option>
                                @foreach($m_company as $item)
                                    <option value="{{$item->id}}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <div class="col-xs-12">
                            <select class="form-control" name="compliance_company" id="compliance_company">
                                <option value="">Select Compliance Company</option>
                                @foreach($c_company as $item)
                                    <option value="{{$item->id}}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <div class="col-xs-12">
                            <select class="form-control" name="status" id="status">
                                <option value="">Status</option>
                                <option value="0">Pending</option>
                                <option value="1">Accepted</option>
                                <option value="2">Canceled</option>
                                <option value="3">Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="form-group">
                        <div class="col-xs-12 text-right">
                            <a name="submitButton" id="submitButton" class="btn btn-default"><i class="fa fa-search"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-condensed" id="inspection-request-table">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>MJB Name</th>
                        <th>Compliance Company Name</th>
                        <th>Status</th>
                        <th>Manage</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="3rd-party-audit-tab" class="_3rd_tab_display_none">
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
            <input type="hidden" id="entityType" value="mj">
            <input type="hidden" id="mjBusiness" value="{{Auth::user()->company_id}}">
            <input type="hidden" id="companyName" value="">
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
