<div class="color-line">
</div>
<div id="logo" class="light-version">
        <span>
           <img src="/images/logo/smplifya_logo.png">
        </span>
</div>
<nav role="navigation" ng-controller="globalHeader">
    <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
    <div class="small-logo">
        <span class="text-primary"><img src="/images/logo/smplifya_logo.png"></span>
    </div>

    <div class="mobile-menu">
        <button type="button" class="navbar-toggle mobile-menu-toggle" data-toggle="collapse" data-target="#mobile-collapse">
            <i class="fa fa-chevron-down"></i>
        </button>
        <div class="collapse mobile-navbar" id="mobile-collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a class="" href="login.html">Login</a>
                </li>
                <li>
                    <a class="" href="{{ URL('auth/logout') }}">Log out</a>
                </li>
                <li>
                    <a class="" href="profile.html">Profile</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="navbar-right">
        <ul class="nav navbar-nav no-borders">

            <li class="dropdown">
                <a href="/users/profile">
                    <i class="pe pe-7s-users text-success"></i>
                    <h5>Profile</h5>
                </a>

                <div class="dropdown-menu hdropdown bigmenu animated flipInX">
                    <table>
                        <tbody>
                        <tr>
                            <td>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </li>

            <li class="dropdown">
                <a href="{{ URL('auth/logout') }}">
                    <i class="pe-7s-upload pe-rotate-90"></i>
                    <h5>Log out</h5>
                </a>
            </li>
        </ul>
    </div>
</nav>