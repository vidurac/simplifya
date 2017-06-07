
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<!-- Page title -->
<title>Simplifya</title>

<!-- Vendor styles -->
{!! Html::style('vendor/fontawesome/css/font-awesome.css') !!}
{!! Html::style('vendor/metisMenu/dist/metisMenu.css') !!}
{!! Html::style('vendor/animate.css/animate.css') !!}
{!! Html::style('vendor/bootstrap/dist/css/bootstrap.css') !!}
{{--{!! Html::style('vendor/select2-3.5.2/select2.css') !!}--}}
{!! Html::style('vendor/select2-4.0.3/dist/css/select2.css') !!}
{!! Html::style('vendor/summernote/dist/summernote.css') !!}
{!! Html::style('vendor/summernote/dist/summernote-bs3.css') !!}
{!! HTML::style('/js/growl/stylesheets/jquery.growl.css') !!}
{!! HTML::style('/vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css') !!}
{!! Html::style('/vendor/jquery-ui/themes/smoothness/jquery-ui.min.css') !!}
{!! HTML::style('/vendor/bootstrap-datepicker-master/dist/css/bootstrap-datepicker3.min.css') !!}
{!! Html::style('vendor/bootstrap/dist/css/bootstrap.css') !!}
{!! Html::style('vendor/datatables/media/css/jquery.dataTables.min.css') !!}
{!! Html::style('vendor/toastr/build/toastr.min.css') !!}
{!! Html::style('vendor/sweetalert/lib/sweet-alert.css') !!}
{!! Html::style('vendor/ng-inline-edit/ng-inline-edit.min.css') !!}

{!! Html::style('styles/cropper/cropper.min.css') !!}

{{-- Jquery TreeTable --}}
{!! Html::style('vendor/jquery-treetable/css/jquery.treetable.css') !!}
{!! Html::style('vendor/jquery-treetable/css/jquery.treetable.theme.default.css') !!}

<!-- App styles -->
{!! Html::style('fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css') !!}
{!! Html::style('fonts/pe-icon-7-stroke/css/helper.css') !!}
{!! Html::style('styles/custom.css') !!}
{!! Html::style('styles/style.css') !!}
{!! Html::style('styles/select.css') !!}

{!! Html::style('styles/fancybox/jquery.fancybox.css') !!}

<!-- Vendor scripts -->
{!! Html::script('vendor/jquery/dist/jquery.min.js') !!}
{!! Html::script('vendor/jquery-ui/jquery-ui.min.js') !!}
{!! Html::script('vendor/slimScroll/jquery.slimscroll.min.js') !!}
{!! Html::script('vendor/bootstrap/dist/js/bootstrap.min.js') !!}
{!! Html::script('vendor/metisMenu/dist/metisMenu.min.js') !!}
{!! Html::script('vendor/iCheck/icheck.min.js') !!}
{!! Html::script('vendor/sparkline/index.js') !!}
{!! Html::script('vendor/jquery-validation/jquery.validate.min.js') !!}
{{--{!! Html::script('vendor/select2-3.5.2/select2.js') !!}--}}
{!! Html::script('vendor/select2-4.0.3/dist/js/select2.js') !!}
{!! Html::script('vendor/summernote/dist/summernote.min.js') !!}
{!! Html::script('vendor/toastr/build/toastr.min.js') !!}
{!! Html::script('vendor/moment/min/moment.min.js') !!}
{!! Html::script('vendor/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js') !!}
{!! Html::script('vendor/jquery-ui/ui/accordion.js') !!}
{!! Html::script('vendor/sweetalert/lib/sweet-alert.min.js') !!}

{!! Html::script('js/digitalBush-jquery.maskedinput/src/jquery.maskedinput.js') !!}
{!! Html::script('js/jquery-creditcardvalidator/jquery.creditCardValidator.js') !!}

{!! Html::script('js/cropper/cropper.min.js') !!}

{!! Html::script('vendor/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.js') !!}
{!! Html::script('vendor/jquery-validation/formValidation.min.js') !!}
{!! Html::script('vendor/jquery-validation/bootstrap.min.js') !!}
{!! Html::script('vendor/datatables/media/js/jquery.dataTables.min.js') !!}

        <!-- DataTables buttons scripts -->
{!! Html::script('vendor/pdfmake/build/pdfmake.min.js') !!}
{!! Html::script('vendor/pdfmake/build/vfs_fonts.js') !!}
{!! Html::script('vendor/datatables.net-buttons/js/buttons.html5.min.js') !!}
{!! Html::script('vendor/datatables.net-buttons/js/buttons.print.min.js') !!}
{!! Html::script('vendor/datatables.net-buttons/js/dataTables.buttons.min.js') !!}
{!! Html::script('vendor/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') !!}

{{-- Jquery TreeTable --}}
{!! Html::script('vendor/jquery-treetable/jquery.treetable.js') !!}

<!-- App scripts -->
{!! Html::script('scripts/homer.js') !!}
{!! HTML::script('/js/growl/javascripts/jquery.growl.js') !!}
{!! HTML::script('/js/growl/javascripts/notification.js') !!}
{!! HTML::script('/js/angular.min.js') !!}
{!! HTML::script('/js/angular-animate.min.js') !!}
{!! HTML::script('/js/angular-sanitize.min.js') !!}
{!! HTML::script('/js/angular-filter.min.js') !!}
{!! HTML::script('/js/angular-messages.min.js') !!}
{!! HTML::script('/js/dirPagination.js') !!}
{!! HTML::script('/js/ngClipboard.js') !!}
{!! Html::script('vendor/angular-treetable/dist/angular-treetable.min.js') !!}
{!! HTML::script('/js/angular-ui/bootstrap/ui-bootstrap-0.14.3.min.js') !!}
{!! HTML::script('/js/angular-ui/bootstrap/ui-bootstrap-tpls-0.14.3.min.js') !!}
{!! HTML::script('/js/select.js') !!}
{!! HTML::script('vendor/ng-inline-edit/ng-inline-edit.min.js') !!}
{!! Html::script('js/fancybox/jquery.fancybox.js') !!}

<script>
	app = angular.module('simplifiya', ['ui.bootstrap','ngAnimate','ngSanitize','ngMessages','angularUtils.directives.dirPagination', 'angularInlineEdit', 'ngClipboard','ui.select', 'angular.filter', 'ngTreetable']);

	app.constant('config',{
		_base_url: '<?php echo URL("/"); ?>/'
	});

</script>
{!! HTML::script('/js/angular/app.js') !!}
{{--<script src="https://malsup.github.com/jquery.form.js"></script>--}}
{!! HTML::script('js/malsup-jquery-form/jquery.form.js') !!}
<script>
    var token_id = '<?php echo csrf_token() ?>';
	$(document).ready(function() {
		$(".fancybox-button").fancybox({
			prevEffect		: 'none',
			nextEffect		: 'none',
			closeBtn		: true,
			helpers		: {
				title	: { type : 'inside' },
				buttons	: {}
			}
		});
	});

</script>

<!-- <?php echo env('APP_ENV'); ?> -->
<?php if (env('APP_ENV') != 'local'): ?>
{{--ZenDesk codes  (SWA-122) --}}

<!-- Start of simplifya Zendesk Widget script -->
<script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(e){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload="document._l();">'),o.close()}("https://assets.zendesk.com/embeddable_framework/main.js","simplifya.zendesk.com");
	/*]]>*/</script>
<!-- End of simplifya Zendesk Widget script -->
<script>
// disable all console logs!
console.log = function() {};

app.config(['$compileProvider', function ($compileProvider) {
$compileProvider.debugInfoEnabled(false);
}]);
</script>
<?php endif; ?>