<!doctype html>
<html>
<head>
    <link rel="icon" href="/images/logo/fav.ico" alt="Simplifya">
    @include('includes.head')
</head>
<body class="blank">
<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>Loading...</h1><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<div class="color-line"></div>
<section class="l-main">

    @yield('content')

</section>

<footer class="l-footer">

</footer>
</body>

</html>
