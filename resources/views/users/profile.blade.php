<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/27/2016
 * Time: 3:19 PM
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
                        <form id="update-user-profile" action="/users/updateProfile" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-3">

                                    <div class="user-pro-pic">
                                        <div class="dropzone">
                                            <img class="user-pro-pic img-responsive" src="{{$imageUrl}}">
                                            <output id="list"></output>
                                            <a href="#">Update Image <i class="fa fa-camera"></i></a>
                                            <input type="file" name="profilePicture" id="profilePicture">
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">

                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-2 control-label">Name*</label>
                                            <div class="col-sm-8">
                                                <input id="name" name="name" class="form-control" value="{{$user->name}}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-2 control-label">Email Address*</label>
                                            <div class="col-sm-8">
                                                <input id="email" name="email" class="form-control" value="{{$user->email}}" disabled />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label class="col-sm-2 control-label">Permission Level* </label>
                                            <div class="col-sm-8">
                                                <input id="group" name="group" class="form-control" value="{{$userGroup->name}}" disabled />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-2">

                                        </div>

                                        <div class="hpanel collapsed col-lg-8">
                                            <div class="panel-heading hbuilt" style="margin-left: 2%; margin-right: 2%;">
                                                <div class="panel-tools">
                                                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                                                </div>
                                                Change Password
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="form-group col-lg-12">
                                                        <label class="col-sm-4 control-label">New Password*</label>
                                                        <div class="col-sm-8">
                                                            <input type="password" id="newPassword" name="newPassword" class="form-control" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-lg-12">
                                                        <label class="col-sm-4 control-label">Confirm Password*</label>
                                                        <div class="col-sm-8">
                                                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group pull-right">
                                        <input type="submit" id="updateProfile" class="btn btn-primary" value="Update">

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Crop modal -->
    <div class="modal fade" id="crop-modal" style="z-index: 9999999999999">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('upload_file') }}" method="post" id="savecropdataform">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">Crop Picture</h4>
                    </div>
                    <div class="modal-body">
                        <div class="bootstrap-modal-cropper">
                            <img id="crop-img" width="100%" src="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="crop_image">
                        <input type="hidden" id="image_name" name="image_name">
                        <a class="btn btn-default pull-left" style="margin: 15px" data-loading-text="Saving..." data-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="btn-crop" style="margin: 15px" data-loading-text="Cropping...">Crop</button>
                    </div>
                    <input type="hidden" name="bucket_path" value="{{ Config::get('simplifya.PROFILE_IMG_DIR') }}">
                    <input type="hidden" name="upload_type" value="{{ Config::get('simplifya.UPD_TYPE_PROFILE') }}">
                    <input type="hidden" name="config_type" value="{{ Config::get('simplifya.IMG_SIZE_USER') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
        </div>
    </div>

    <style type="text/css">
        .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
            background-color: #eee!important;
            opacity: 1 !important;
        }
        .dropzone {
            position: relative;
            width: 90% !important;
        }
        .dropzone a {
            height: 40px;
            width: 100%;
            background-color: #30465a;
            color: #fff;
            position: absolute;
            left: 0px;
            bottom: 0px;
            text-transform: uppercase;
            text-align: center;
            line-height: 40px;
            text-decoration: none;
            display: block;
            z-index: 1031; }
        .dropzone a i {
            margin-left: 10px; }
        .dropzone img {
            min-height: 220px; }
        .dropzone input[type="file"] {
            visibility: hidden; }

    </style>

    <script type="application/javascript">

        var bucket_path = '{{ Config::get('simplifya.BUCKET_URL').Config::get('simplifya.USER_PROFILE_IMG_DIR').'/' }}';
        /**
         * Image cropping
         */
        $(document).ready(function (){

            /* == Crop images == */

            $(document).on('submit', '#savecropdataform', function() {
                var inputid = $(document).find('#crop-img').data('target');
                $('#btn-crop').button('loading');
                $('#savecropdataform').ajaxSubmit({
                    type:"POST",
                    data: {
                        x: $('#crop-img').cropper('getData').x,
                        y: $('#crop-img').cropper('getData').y,
                        w: $('#crop-img').cropper('getData').width,
                        h: $('#crop-img').cropper('getData').height,
                        image: $('#crop_image').val()
                    },
                    dataType:'json',
                    success: function(result) {

                        file = document.getElementById("profilePicture");

                        if(result.success == 'true'){
                            var span = document.createElement('span');
                            var span = '<input type="hidden" name="cropfiles" value="'+result.data.fileid+'">';
                            var imageHtml = '<img src="'+bucket_path+result.data.filename+ '" title="'+result.data.filename+ '" class="user-pro-pic img-responsive"/>'
                            var imageCircleHtml = '<img src="'+bucket_path+result.data.filename+ '" title="'+result.data.filename+ '" class="img-circle m-b" style="height: 90px; width: 90px;"/>'
                            $(".user-pro-pic").find("img").remove();
                            $(".profile-picture").find("img").remove();

                            $('#list').after(imageHtml);
                            $('.profile-picture > a').after(imageCircleHtml);
                            document.getElementById('list').innerHTML = span;
                        }else{
                            car.msg('warning',result.msg, 4000);
                        }
                        $('#btn-crop').button('reset')
                        $('#crop-img').data('modal', null);
                        $('#crop-modal').modal('hide');

                        $("#profilePicture").valid();
                    }
                });
                return false;
            });

            var models = [];
            $(document).on('shown.bs.modal', '#crop-modal', function() {

                $('#crop-img').cropper({cropBoxResizable:true, aspectRatio: {{ Config::get('simplifya.RESIZE_USER_CROP_WIDTH') }}/{{ Config::get('simplifya.RESIZE_USER_CROP_HEIGHT') }} });

                $('#crop-img').cropper("setData", {
                    width: {{ Config::get('simplifya.RESIZE_USER_CROP_WIDTH') }},
                    height: {{ Config::get('simplifya.RESIZE_USER_CROP_HEIGHT') }}
                });

            }).on('hidden.bs.modal', '#crop-modal', function() {
                $('#crop-img').cropper('destroy');
                $('body').find('.modal').each(function() {
                    //
                });
            });

            document.getElementById('profilePicture').addEventListener('change', handleFileSelect, false);
            $('body').on('hidden', function () { $(this).removeData('modal'); });

        });

        function handleFileSelect(evt) {
            var files = evt.target.files; // FileList object
            // Loop through the FileList and render image files as thumbnails.
            for (var i = 0, f; f = files[i]; i++) {
                // Only process image files.
                if (!f.type.match('image.*')) {
                    continue;
                }
                var reader = new FileReader();
                // Closure to capture the file information.
                reader.onload = (function(theFile) {
                    return function(e) {
                        // Render thumbnail.
                        var span = document.createElement('span'); selectOnBlur: true,
                                $('#crop_image').val(e.target.result);
                        $('#crop-img').attr('src',e.target.result);
                        $('#image_name').val(escape(theFile.name));

                        var image = $('#profilePicture').val();
                        $('#crop-modal').modal({
                            show: true,
                            backdrop: 'static',
                            keyboard: true});
                    };
                })(f);
                // Read in the image file as a data URL.
                reader.readAsDataURL(f);
            }
        }

    </script>

    {!! Html::script('/js/Users/profile.js') !!}

@stop


