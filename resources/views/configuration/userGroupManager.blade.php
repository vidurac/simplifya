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
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed user-group-table" id="user-group-table">
                                        <thead>
                                        <tr>
                                            <th>Group Name</th>
                                            <th>Company Type</th>
                                            <th></th>
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

    <div class="modal fade in" id="user-group-edit-model" tabindex="-1" role="dialog"  data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Edit User Type</h4>
                </div>
                <form name="user-group-edit-form" novalidate id="user-group-edit-form" action="#" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Group Name* </label>
                                <div class="col-sm-8">
                                    <input type="text" name="group_name" id="group_name" class="form-control"/>
                                    <span id="err-group_name"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Entity List* </label>
                                <div class="col-sm-8">
                                    <select class="form-control company valid" name="entity_id" id="entity_id" aria-required="true" aria-invalid="false" disabled="disabled">
                                        <option value="">Select Entity</option>
                                    </select>
                                    <input type="hidden" name="edit_entity_id" id="edit_entity_id" value="">
                                    <span id="err-entity_id"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <input type="hidden" name="user_group_id" id="user_group_id" value=""/>
                                    <button type="button" class="btn btn-default close-user-group-model" id="close-user-group-model">Close</button>
                                    <button type="button" class="btn w-xs btn-primary" id="update-user-group-btn">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {!! Html::script('js/configuration/userGroup.js') !!}
@stop
