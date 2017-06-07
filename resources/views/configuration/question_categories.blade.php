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
                                <div><a href="/configuration/qcategories/new" class="btn btn-info">Create New</a></div>
                                <div class="col-sm-5 text-right"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed question-category-table" id="question-category-table">
                                        <thead>
                                        <tr>
                                            <th>Category Name</th>
                                            <th>Is Required</th>
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
        <div class="row">

        </div>
    </div>

    <div class="modal fade in" id="question-category-edit-model" tabindex="-1" role="dialog"  data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <h4 class="modal-title">Edit Question Category</h4>
                </div>
                <form name="question-category-edit-form" novalidate id="question-category-edit-form" action="#" method="post">
                    <div class="modal-body">
                        <div class="row required_options_btns">
                            <div class="form-group col-lg-12" >
                                <label class="col-sm-4 control-label">Required* </label>
                                <div class="col-sm-8">
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="is_required" id="is_required[]" class="i-checks is_req_yes" value="1" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div>
                                        Yes
                                    </label>
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="is_required" id="is_required[]" class="i-checks is_req_no" value="0" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div> No
                                    </label>
                                    <span id="err-required"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row required_options_btns">
                            <div class="form-group col-lg-12" >
                                <label class="col-sm-4 control-label">Multi-Select* </label>
                                <div class="col-sm-8">
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="is_multiselect" id="is_multiselect[]" class="i-checks is_multi_yes" value="1" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div>
                                        Yes
                                    </label>
                                    <label class="">
                                        <div class="iradio_square-green" style="position: relative;">
                                            <input type="radio" name="is_multiselect" id="is_multiselect[]" class="i-checks is_multi_no" value="0" style="position: absolute; opacity: 0;">
                                            <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                        </div> No
                                    </label>
                                    <span id="err-is_multiselect"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Visible To </label>
                                <div class="col-sm-8">
                                    <label class="checkbox-inline"><input type="checkbox" name="visible_to[]" id="mjb" value="{{ Config::get('simplifya.MARIJUANA_COMPANY_TYPE') }}">MJBusiness</label>
                                    <label class="checkbox-inline"><input type="checkbox" name="visible_to[]" id="cc" value="{{ Config::get('simplifya.COMPLIANCE_COMPANY_TYPE') }}">Compliance Company</label>
                                    <label class="checkbox-inline"><input type="checkbox" name="visible_to[]" id="ge" value="{{ Config::get('simplifya.GOVERNMENT_ENTITY_TYPE') }}">Government Entity</label>
                                    <span id="err-visibility"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label class="col-sm-4 control-label">Category Name </label>
                                <div class="col-sm-8">
                                    <input type="text" name="category_name" id="category_name" class="form-control"/>
                                    <span id="err-cat_name"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div id="option_list">
                                <p>
                                    <label for="option_list" class="col-md-12">
                                        <span class="col-md-5"></span>
                                        <span class="col-md-5"></span>
                                        <span class="col-md-2"><a href="#" id="addScnt" class="btn btn-info pull-right">Add New Option</a></span>
                                    </label>
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <input type="hidden" name="id" id="id" value=""/>
                                    <input type="hidden" name="is_main_cat" id="is_main_cat" value="" />
                                    <button type="button" class="btn btn-default close-qcategory-edit-form" id="close-qcategory-edit-model">Close</button>
                                    <button type="button" class="btn w-xs btn-primary" id="update-qcategory-btn">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {!! Html::script('js/configuration/questionCategory.js') !!}
@stop
