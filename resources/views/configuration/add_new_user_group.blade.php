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
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <form name="add_userGroup" method="post" id="add_userGroup">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-3 control-label">Visibility</label>
                                <div class="col-sm-9">
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="visibility" id="visibility" class="i-checks" value="1" checked="checked" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div>
                                        Active
                                    </label>
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="visibility" id="visibility" class="i-checks" value="0" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div> Inactive
                                    </label>
                                    <span id="err-visibility"></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">Group Name*</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="group_name" id="group_name" class="form-control" value="" />
                                        <span id="err-group_name"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">Company*</label>
                                    <div class="col-sm-9">
                                        <select class="form-control company valid" name="company_id" id="company_id" aria-required="true" aria-invalid="false">
                                            <option value="">Select Company</option>
                                            @foreach($company_list as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <span id="err-company_id"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-12">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5 text-right">
                                    <a class="btn btn-success" id="save-user-group-btn">Save</a>
                                    <a href="/configuration/userGroup" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Html::script('js/configuration/userGroup.js') !!}
@stop