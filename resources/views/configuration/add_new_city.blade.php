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
                        <form name="add_city" method="post" id="add_city">
                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">Country* </label>
                                    <div class="col-sm-9">
                                        <select class="form-control country valid" name="country_id" id="country_select" aria-required="true" aria-invalid="false">
                                            <option value="">Select Country</option>
                                            @foreach($country_list as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <span id="err-country"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">State Name* </label>
                                    <div class="col-sm-9">
                                        <select class="form-control state_id valid" name="state_id" id="state_id" aria-required="true" aria-invalid="false">

                                        </select>
                                        <span id="err-state"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row clearfix">
                                <div class="text-right col-lg-12">
                                    <a id="add_row" class="btn btn-info">Add City</a>&nbsp;<a id='delete_row' class="btn btn-danger2">Delete City</a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <label class="col-sm-3 control-label">City List*</label>
                                    <div class="col-sm-9">
                                        <table class="table table-hover" id="tab_city_list">
                                            <thead>
                                            <tr>
                                                <th class="text-center">City Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr id='addr0'>
                                                <td>
                                                    <input type="text" name='city_id' class="form-control city_id" value=""/>
                                                </td>
                                            </tr>
                                            <tr id='addr1'></tr>
                                            </tbody>
                                        </table>
                                        <span id="err-city"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-12">
                                <div class="col-sm-7"></div>
                                <div class="col-sm-5 text-right">
                                    <a class="btn btn-success" id="save_city">Save</a>
                                    <a href="/configuration/city" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Html::script('js/configuration/city.js') !!}
@stop