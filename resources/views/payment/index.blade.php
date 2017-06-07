@extends('layout.dashbord')

@section('content')


    <div class="normalheader transition animated fadeIn">
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            Search and view payments you've made to Simplifya.
        </div>
        <div class="hpanel">
            <div class="panel-body">
                <a class="small-header-action" href="">
                    <div class="clip-header">
                        <i class="fa fa-arrow-up"></i>
                    </div>
                </a>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="input-group input-append date" id="paymentFromDateDatePicker">
                                    <input type="text" class="form-control" name="fromDate" id="paymentFromDate" placeholder="Date From"/>
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                    <span id="err-date"></span>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="input-group input-append date" id="paymentToDateDatePicker">
                                    <input type="text" class="form-control" name="toDate" id="paymentToDate" placeholder="Date To" />
                                    <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                <span id="err-date"></span>
                            </div>

                            {{--<div class="col-md-3">--}}
                                {{--<input type="text" class="form-control" name="txId" id="txId" placeholder="Search by TX ID" />--}}
                            {{--</div>--}}

                            <div class="col-md-3">
                                <input type="text" class="form-control" name="responseId" id="responseId" placeholder="Search by Response ID" />
                            </div>

                            @if($groupId == 1)
                                <div class="col-md-2">
                                    <select class="form-control" id="companyType" placeholder="Company Type">
                                        <option value="0"> Select Company Type </option>
                                        @foreach($entities as $entity)
                                            <option value="{{$entity->id}}"> {{$entity->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select class="form-control" id="companyName" placeholder="Company Name">
                                        <option value="0"> Select Company Name</option>
                                        @foreach($companies as $company)
                                            <option value="{{$company->id}}"> {{$company->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-2">
                                <select class="form-control" id="txStatus" placeholder="TX Status">
                                    <option value="">TX Status</option>
                                    <option value="1">Succeeded</option>
                                    <option value="0">Failed</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-control" id="txType" placeholder="TX Type">
                                    <option value="">TX Type</option>
                                    <option value="Subscription">Subscription</option>
                                    <option value="License">License</option>
                                    <option value="Appointment">Appointment</option>
                                </select>
                            </div>

                            <div class="col-md-1">
                                <a name="searchPayments" id="searchPayments" class="btn btn-default"><i class="fa fa-search"></i></a>
                            </div>

                        </div>
                    </div>

                </div>
                <br>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-condensed table-hover" id="payment-detail-table">
                                <thead>
                                <tr>
                                    <th>Req. Date & Time</th>
                                    {{--<th>Tx ID</th>--}}
                                    <th>Object</th>
                                    <th>Request Currency</th>
                                    <th>Request Amount</th>
                                    <th>Res. Date & Time</th>
                                    <th>Response ID</th>
                                    <th>Response Currency</th>
                                    <th>Response Amount</th>
                                    <th>Company Type</th>
                                    <th>Company Name</th>
                                    <th>Tx Status</th>
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

    <div class="content animate-panel">

    </div>

    {!! Html::script('/js/payment/paymentManager.js') !!}
@stop