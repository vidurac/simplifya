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
    <div class="content animated-panel">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                    {{--<h3>Add New Inspection Request</h3>--}}
                </div>

                <?php
                    $panel_color = "";
                    switch($req_details[0]->status)
                    {
                        case 0:
                            $panel_color = "hyellow";
                            break;
                        case 1:
                            $panel_color = "hgreen";
                            break;
                        case 2:
                            $panel_color = "hred";
                            break;
                        case 3:
                            $panel_color = "hblue";
                            break;
                        default:
                            break;
                    }
                ?>

                <div class="hpanel {{ $panel_color }}">
                    <div class="panel-body">
                        <form action="{{ url('/request/store') }}" id="companyReqForm" method="post">
                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label text-right">Company Name*</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="company_name" id="company_name" disabled>
                                            @if(isset($req_details))
                                                @foreach($req_details as $item)
                                                    <option value="{{$item->complianceCompany->id}}">{{ $item->complianceCompany->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label text-right">Location*</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="company_location" id="entity_type" disabled>
                                            @if(isset($req_details))
                                                @foreach($req_details as $item)
                                                    <option value="{{ $item->companyLocation->id }}">{{ $item->companyLocation->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label text-right">Comment</label>

                                    <div class="col-sm-9">
                                        <div id="comment-section"><?php echo $req_details[0]->comment; ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <div class="col-sm-2"></div>
                                    <div class="col-lg-10 text-right">
                                        @if($req_details[0]->status==0 && ($req_details[0]->complianceCompany->id==Auth::user()->company_id))
                                            @if(\Helpers::get_entity_type(Auth::user()->company_id)!=2)
                                            <a href="/appointment/create?manage=1&id={{ $req_details[0]->id }}" class="btn btn-sm btn-success" type="submit">Make Appointment</a>
                                            <a href="/request/process?manage=3&id={{ $req_details[0]->id }}" class="btn btn-sm btn-danger" type="submit">Reject</a>
                                            @endif
                                            {{--<a href="/request/process?manage=2&id={{ $req_details[0]->id }}" class="btn btn-sm btn-warning btn-success" type="submit">Cancel Appointment</a>--}}
                                        @endif

                                        @if($req_details[0]->status==0 && ($req_details[0]->MarijuanaCompany->id==Auth::user()->company_id))
                                            <a href="/request/process?manage=2&id={{ $req_details[0]->id }}" class="btn btn-sm btn-warning btn-success" type="submit">Cancel Appointment</a>
                                        @endif
                                        <a href="/request/manage" class="btn btn-sm btn-default">Back</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--/request/process?manage=1&id={{ $req_details[0]->id--}}
    {!! Html::script('js/request/request.js') !!}
@stop
