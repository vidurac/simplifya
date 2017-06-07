<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<footer class="footer">

    <div class="row">
        <div class="col-md-5 col-xs-12 col">
            <div class="address">
                <h4>SIMPLIFYA</h4>
                455 Sherman Street, Suite 510, Denver, CO 80203
                </div>
            <div class="phone">
                877.464.8398
            </div>

        </div>
        <div class="col-md-2 col-sm-4 col-xs-6 col text-center">
            <h4>FIND US</h4>
            <div class="social-icons">
                <span class="fb"><a href="https://www.facebook.com/simplifya" target="_blank"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></span>
                <span class="tt"><a href="https://twitter.com/@simplifya1" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a></span>
                <span class="in"><a href="https://www.linkedin.com/company/simplifya" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a></span>
            </div>
        </div>
        <div class="col-md-5 col-sm-8 col-xs-6 col">
            <div class="download-wrapper pull-right">
                    <h4>DOWNLOAD APPS</h4>
                    <div class="downloads">
                        <a href="" target="_blank" class="m-b app-store"><i class="fa fa-apple" aria-hidden="true"></i><span class="app-store"><small>Download on the</small><br><span class="title">App Store</span></span></a>
                        <a href="" target="_blank" class="google-play"><i class="fa fa-android" aria-hidden="true"></i><span class="google-play"><small>Get it on</small><br><span class="title">Google Play</span></span></a>
                    </div>
                </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12 copyrights">
            <div class="inner">
                Copyright &copy; 2016-17, <span class="company">Simplifya, LLC</span>. All Rights Reserved.<span class="line">|</span>
                <a href="http://simplifya.com/privacy-policy/" target="_blank" title="Privacy Policy">Privacy Policy</a>
                <a href="http://simplifya.com/terms-of-service/" target="_blank" title="Term of Service">Term of Service</a>
                <div class="godaddy-seal">
                <span id="siteseal">
                    <?php if (env('APP_ENV') != 'local'): ?>

                    <script async type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=uLyppC85fi3iaLVZaWu4bhesbgqI5fL1NxwUB8tsIZkmBEq3ZND4MD0k9hzp"></script>
                    <?php endif; ?>
                </span>
                </div>
            </div>
        </div>
    </div>
</footer>


