<!DOCTYPE html>
<html ng-app="simplifiya">

<head>
    <link rel="icon" href="/images/logo/fav.ico" alt="Simplifya">
    @include('includes.head')
</head>

<body class="fixed-navbar" popover-close exclude-class="exclude">

<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>Loading...</h1><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Header -->
<div id="header">
    @include('includes.header')
</div>

<!-- Navigation -->
<aside id="menu">
    @include('includes.menu')
</aside>

<!-- Main Wrapper -->
<div id="wrapper">
    @include('includes.breadcrumb')
    @yield('content')
    @yield('scripts')
</div>
@include('includes.footer')
</body>
</html>