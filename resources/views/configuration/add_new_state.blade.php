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
                        <form name="add_state" method="post" id="add_state">
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
                                    <label class="col-sm-3 control-label">State Name*</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="state_name" id="state_name" class="form-control"/>
                                        <span id="err-name"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">Country Name*</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="country_id" name="country_id">
                                            @foreach($country_list as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <span id="err-country"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-12">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5 text-right">
                                    <a class="btn btn-success" id="save_state">Save</a>
                                    <a href="/configuration/state" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Html::script('js/configuration/state.js') !!}
@stop