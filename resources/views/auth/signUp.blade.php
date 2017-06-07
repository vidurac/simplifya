
<div class="payment-process process-landing-page">

<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/6/2016
 * Time: 11:24 AM
 */
?>

@extends('layout.default')

@section('content')
        <div class="container">
            @if (isset($message))
                <div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>{{$message}}</strong>
                </div>
            @endif
            <h2 class="text-center">Select Your Business</h2>
            <div class="row">
                <div class="col-xs-12">
                    <div class="col-xs-12 col-md-4 business-type">
                        <div class="business-container text-center">
                            <img src="../images/icons/icon-marijuana-business.png" class="img-responsive">
                            <h3>Marijuana <span>Business</span></h3>
                            <div class="business-description">State-licensed marijuana businesses
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-8 col-md-offset-2">
                                    <a href="/company/companyType?entity_type=2" class="btn btn-default">Sign up</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-4 business-type">
                        <div class="business-container text-center">
                            <img src="../images/icons/icon-3rd-party-auditor.png" class="img-responsive">
                            <h3>3rd Party <span>Auditor</span></h3>
                            <div class="business-description">Anyone who would audit a marijuana business (law firms, compliance companies, consultants, etc.)
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-8 col-md-offset-2">
                                    <a href="/company/companyType?entity_type=3" class="btn btn-default btn-orange">Sign up</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-4 business-type">
                        <div class="business-container text-center">
                            <img src="../images/icons/icon-government-entity.png" class="img-responsive">
                            <h3>Government <span>Entity</span></h3>
                            <div class="business-description">State and Local Regulatory Bodies
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-8 col-md-offset-2">
                                    <a href="/company/companyType?entity_type=4" class="btn btn-default btn-red">Sign up</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {!! Html::script('js/initial-signup.js') !!}

    <script>
        // Toastr options
        toastr.options = {
            "debug": false,
            "newestOnTop": false,
            "positionClass": "toast-top-center",
            "closeButton": true,
            "toastClass": "animated fadeInDown",
        };

        var msg = '<?php if($errors->has()) {foreach ($errors->all() as $error){ echo $error; } $has_error = true;}else{ $has_error = false;}?>';
        var has_error = '<?php echo $has_error?>';
        if(has_error) {
            toastr.error(msg);
        }

    </script>
@stop