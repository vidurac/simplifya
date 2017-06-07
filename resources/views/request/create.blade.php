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
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                    {{--<h3>Add New Inspection Request</h3>--}}
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <form action="{{ url('/request/store') }}" id="companyReqForm" method="post">
                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">Company Name*</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="company_name" id="company_name">
                                            <option value="">Select Company Name</option>
                                            @if(isset($company))
                                                @foreach($company as $item)
                                                    <option value="{{$item->id}}">{{ $item->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">Location*</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="company_location" id="entity_type">
                                            <option value="">Select Location</option>
                                            @if(isset($location[0]) && $user_group != 3)
                                                @foreach($location[0]->companyLocation as $item)
                                                    <option value="{{$item->id}}">{{ $item->name }}</option>
                                                @endforeach
                                            @else
                                                @foreach($location as $item)
                                                    <option value="{{$item->id}}">{{ $item->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">Comment</label>

                                    <div class="col-sm-9">
                                        <textarea class="form-control summernote" name="message" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <div class="col-sm-7"></div>
                                    <div class="col-sm-5 text-right">
                                        <button class="btn btn-success" type="submit">Submit</button>
                                        <a href="/request/manage" class="btn btn-default">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Html::script('js/request/request.js') !!}
@stop
