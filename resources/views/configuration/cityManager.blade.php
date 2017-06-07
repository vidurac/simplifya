<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 6/6/2016
 * Time: 2:05 PM
 */
?>

@extends('layout.dashbord')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-7 text-left"><a href="/configuration/city/new" class="btn btn-info">Create New City</a></div>
                                <div class="col-sm-5 text-right"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed city-table" id="city-table">
                                        <thead>
                                        <tr>
                                            <th>Country</th>
                                            <th>State</th>
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
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade in" id="city-manage-model" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">City Manager</h4>
                </div>
                <form name="city-manage-form" novalidate id="city-manage-form" action="#" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-lg-12" >
                                <label class="col-sm-4 control-label">Country* </label>
                                <div class="col-sm-8">
                                    <select class="form-control country valid" name="country_id" id="country_id" aria-required="true" aria-invalid="false" disabled>
                                        @foreach($country_list as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="err-country"></span>
                                    <input type="hidden" type="text" name="country-id" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">State* </label>
                                <div class="col-sm-8">
                                    <select class="form-control state_id valid" name="state_id" id="state_selection" aria-required="true" aria-invalid="false" disabled>
                                        <option>Select State</option>
                                    </select>
                                    <span id="err-state-name"></span>
                                    <input type="hidden" type="text" name="state-id" id="state-id" value="" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">City List* </label>
                                <div class="col-sm-8">
                                    <div class="input_fields_wrap" id="tab_city_list"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <button type="button" class="btn btn-default close-save-city-form">Cancel</button>
                                    <button type="button" class="btn w-xs btn-primary" id="update-city-btn">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {!! Html::script('js/configuration/city.js') !!}
@stop
