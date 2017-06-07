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
                                <div class="col-sm-7 text-left"><a href="/configuration/state/new" class="btn btn-info">Create New State</a></div>
                                <div class="col-sm-5 text-right"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed state-table" id="state-table">
                                        <thead>
                                        <tr>
                                            <th>State Name</th>
                                            <th>Country</th>
                                            <th>Status</th>
                                            <th>Actions</th>
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

    <div class="modal fade in" id="state-manage-model" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">State Manager</h4>
                </div>
                <form name="state-manage-form" novalidate id="state-manage-form" action="#" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-lg-12" >
                                <label class="col-sm-4 control-label">Visibility </label>
                                <div class="col-sm-8">
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="visibility" id="visibility_yes" class="i-checks" value="1" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div>
                                        Active
                                    </label>
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="visibility" id="visibility_no" class="i-checks" value="0" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div> Inactive
                                    </label>
                                    <span id="err-visibility"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">State Name </label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" placeholder="State Name" name="state_name" id="edit_state_name">
                                    <span id="err-edit-name"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Country </label>
                                <div class="col-sm-8">
                                    <select class="form-control country valid" name="country_id" id="edit-country" aria-required="true" aria-invalid="false">
                                        @foreach($country_list as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <button type="button" class="btn btn-default close-edit-state-form">Cancel</button>
                                    <button type="button" class="btn w-xs btn-primary" id="edit-state-btn">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {!! Html::script('js/configuration/state.js') !!}
@stop
