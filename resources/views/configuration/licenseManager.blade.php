<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 6/1/2016
 * Time: 4:09 PM
 */
        ?>
@extends('layout.dashbord')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">

                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <a type="button" class="btn btn-info" id="add-new-license" data-toggle="modal" href="{{URL('/configuration/licenses/new')}}">Add New Licenses</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 ">
                                <div class="table-responsive">
                                    <table class="table table-condensed table-hover" id="license-manager-table">
                                        <thead>
                                        <tr>
                                            <th>License Name</th>
                                            <th>Fee</th>
                                            <th>State</th>
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
    {!! Html::script('js/licenses.js') !!}


<div class="modal fade in" id="add_new_license_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Add License</h4>
            </div>
            <form name="add_license_form"  id="add_license_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">License Name*</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" placeholder="License Name" name="license_name" id="license_name">
                                @if ($errors->has('license_name'))<label class="error">{!!$errors->first('license_name')!!}</label>@endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Select License Type*</label>
                            <div class="col-sm-8">
                                <select name="type" class="form-control location" id="type">
                                    <option value="NORMAL">NORMAL</option>
                                    <option value="FEDERAL">FEDERAL</option>
                                </select>
                            </div>
                            @if ($errors->has('type'))<label class="error">{!!$errors->first('type')!!}</label>@endif

                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Select Country*</label>
                            <div class="col-sm-8">
                                <select name="country" class="form-control location" id="country">
                                    <option value=""> Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                            @if ($errors->has('country'))<label class="error">{!!$errors->first('country')!!}</label>@endif

                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Select State*</label>
                            <div class="col-sm-8">
                                <select name="state" class="form-control location" id="state">

                                </select>
                            </div>
                            @if ($errors->has('state'))<label class="error">{!!$errors->first('state')!!}</label>@endif

                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Checklist Fee*</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" placeholder="Check List Fee" name="checklist_Fee" id="checklist_Fee">
                                @if ($errors->has('checklist_Fee'))<label class="error">{!!$errors->first('checklist_Fee')!!}</label>@endif
                                <small class="text-muted">Amount CCs/GEs will be charged to generate a checklist for this license.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default cls-add-license-form" data-dismiss="modal" >Cancel</button>
                    <button type="button" class="btn btn-primary save-license" >Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade in" id="edit_license_type" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <h4 class="modal-title">Edit License Type</h4>
            </div>
            <form name="edit_license_form"  id="edit_license_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">License Name*</label>
                            <div class="col-sm-8">
                                <input type="hidden" name="license_id" id="license_id">
                                <input class="form-control" type="text" placeholder="License Name" name="edit_license_name" id="edit_license_name">
                                @if ($errors->has('edit_license_name'))<label class="error">{!!$errors->first('edit_license_name')!!}</label>@endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Select License Type*</label>
                            <div class="col-sm-8">
                                <select name="edit_type" class="form-control location" id="edit_type">
                                    <option value="NORMAL">NORMAL</option>
                                    <option value="FEDERAL">FEDERAL</option>
                                </select>
                            </div>
                            @if ($errors->has('edit_country'))<label class="error">{!!$errors->first('edit_country')!!}</label>@endif

                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Select Country*</label>
                            <div class="col-sm-8">
                                <select name="edit_country" class="form-control location" id="edit_country">
                                    <option value=""> Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                            @if ($errors->has('edit_country'))<label class="error">{!!$errors->first('edit_country')!!}</label>@endif

                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Select State*</label>
                            <div class="col-sm-8">
                                <select name="edit_state" class="form-control location" id="edit_state">

                                </select>
                            </div>
                            @if ($errors->has('edit_state'))<label class="error">{!!$errors->first('edit_state')!!}</label>@endif

                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-12">
                            <label class="col-sm-4 control-label">Checklist Fee*</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" placeholder="Check List Fee" name="edit_checklist_Fee" id="edit_checklist_Fee">
                                @if ($errors->has('edit_checklist_Fee'))<label class="error">{!!$errors->first('edit_checklist_Fee')!!}</label>@endif
                                <small class="text-muted">Amount CCs/GEs will be charged to generate a checklist for this license.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default edit-cls-license-form" data-dismiss="modal" >Cancel</button>
                    <button type="button" class="btn btn-primary save-license-changes" >Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
