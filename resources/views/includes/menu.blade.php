
<div id="navigation">
    <div class="profile-picture">
        <a href="/users/profile">
            <img src="{{Session('profile_image')}}" class="img-circle m-b" alt="logo" style="height: 90px; width: 90px;">
        </a>

        <div class="stats-label text-color">
            <span class="font-extra-bold font-uppercase">{{ Auth::user()->name }}</span>

            <div class="dropdown">
                <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                    <small class="text-muted">{{ \Helpers::get_user_type(Auth::user()->id) }}<b class="caret"></b></small>
                </a>
                <ul class="dropdown-menu animated flipInX m-t-xs">
                    <li><a href="/users/profile">Profile</a></li>
                    <li><a href="auth/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php if(Session('company_status') == 2){?>

        <!-- if supper admin -->
        @if(Auth::user()->master_user_group_id == 1)
            <ul class="nav" id="side-menu">
                <li {{ (Request::is('/') ? 'class=active' : '') }}>
                    <a href="{{ URL('dashboard') }}"> <span class="nav-label">Dashboard</span></a>
                </li>

                <li {{ (Request::is('question') ? 'class=active' : '') }}>
                    <a href="{{ URL('question') }}"> <span class="nav-label">Question Manager</span></a>
                </li>

                <li {{ (Request::is('company/manager') ? 'class=active' : '') }}>
                    <a href="{{ URL('company/manager') }}"> <span class="nav-label">COMPANY MANAGER</span></a>
                </li>

                <li {{ (Request::is('mailchimp') ? 'class=active' : '') }}>
                    <a href="{{ URL('mailchimp') }}"> <span class="nav-label">User Manager</span></a>
                </li>

                <li {{ (Request::is('checklist') ? 'class=active' : '') }}>
                    <a href="{{ URL('checklist') }}"> <span class="nav-label">CHECKLISTS</span></a>
                </li>

                @if(Request::is('request/manage') || Request::is('appointment*') || Request::is('report*'))
                <li class="active">
                @else
                <li>
                @endif
                    <a href="#"><span class="nav-label">AUDITS</span><span class="fa arrow"></span> </a>
                    <ul class="nav nav-second-level">
                        <li {{ (Request::is('request/manage') ? 'class=active' : '') }}>
                            <a href="{{ URL('request/manage') }}"> <span class="nav-label">REQUESTS</span></a>
                        </li>

                        <li {{ (Request::is('appointment*') ? 'class=active' : '') }}>
                            <a href="{{ URL('appointment') }}"> <span class="nav-label">APPOINTMENTS</span></a>
                        </li>

                        {{--<li {{ (Request::is('report*') ? 'class=active' : '') }}>--}}
                            {{--<a href="{{ URL('reports') }}"> <span class="nav-label">REPORTS</span></a>--}}
                        {{--</li>--}}

                    </ul>
                </li>

                <li {{ (Request::is('payment') ? 'class=active' : '') }}>
                    <a href="{{ URL('payment') }}"> <span class="nav-label">Payments</span></a>
                </li>

                <li {{ (Request::is('configuration') ? 'class=active' : '') }}>
                    <a href="{{ URL('configuration') }}"> <span class="nav-label">Configurations</span></a>
                </li>

                <li {{ (Request::is('users') ? 'class=active' : '') }}>
                    <a href="{{ URL('users') }}"> <span class="nav-label">ADMIN MANAGER</span></a>
                </li>

                <li {{ (Request::is('reports/type/list') ? 'class=active' : '') }}>
                    <a href="{{ URL('reports/type/list') }}"> <span class="nav-label">Reports</span></a>
                </li>

                <li {{ (Request::is('change/company/info') ? 'class=active' : '') }}>
                    <a href="{{ URL('change/company/info') }}"> <span class="nav-label">Company Info</span></a>
                </li>

            </ul>

        @else
            <ul class="nav" id="side-menu">
                <li {{ (Request::is('/') ? 'class=active' : '') }}>
                    <a href="{{ URL('dashboard') }}"> <span class="nav-label">Dashboard</span></a>
                </li>

                @if(Request::is('request/manage') || Request::is('appointment*') || Request::is('report*'))
                    <li class="active">
                @else
                    <li>
                        @endif
                        <a href="#"><span class="nav-label">AUDITS</span><span class="fa arrow"></span> </a>
                        <ul class="nav nav-second-level">
                            @if((Auth::user()->master_user_group_id == 2) || (Auth::user()->master_user_group_id == 5) || (Auth::user()->master_user_group_id == 3))
                                <li {{ (Request::is('request/manage') ? 'class=active' : '') }}>
                                    <a href="{{ URL('request/manage') }}"> @if((Auth::user()->master_user_group_id == 2) || (Auth::user()->master_user_group_id == 3))<span class="nav-label">3rd PARTY AUDIT</span>@else <span class="nav-label">REQUESTS</span>@endif</a>
                                </li>
                            @endif

                            @if((Auth::user()->master_user_group_id == 5 || (Auth::user()->master_user_group_id == 7) || ((Auth::user()->master_user_group_id == 2) || (Auth::user()->master_user_group_id == 3))))
                                <li {{ (Request::is('appointment*') ? 'class=active' : '') }}>
                                    <a href="{{ URL('appointment') }}"> @if((Auth::user()->master_user_group_id == 2) || (Auth::user()->master_user_group_id == 3))<span class="nav-label">SELF-AUDIT</span>@else<span class="nav-label">APPOINTMENTS</span>@endif</a>
                                </li>
                            @endif

                            <li {{ (Request::is('report*') ? 'class=active' : '') }}>
                                <a href="{{ URL('reports') }}"> <span class="nav-label">REPORTS</span></a>
                            </li>

                        </ul>

                    </li>

                    @if((Auth::user()->master_user_group_id == 2) || ((Auth::user()->master_user_group_id == 5) || (Auth::user()->master_user_group_id == 7)))
                        <li {{ (Request::is('change/company/info') ? 'class=active' : '') }}>
                            <a href="{{ URL('change/company/info') }}"> <span class="nav-label">Company Info</span></a>
                        </li>
                    @endif

                    @if((Auth::user()->master_user_group_id == 2) || ((Auth::user()->master_user_group_id == 5) || (Auth::user()->master_user_group_id == 7)))
                        <li {{ (Request::is('location/info') ? 'class=active' : '') }}>
                            <a href="{{ URL('location/info') }}"> <span class="nav-label">Locations</span></a>
                        </li>
                    @endif

                    @if((Auth::user()->master_user_group_id == 2) || (Auth::user()->master_user_group_id == 3) || ((Auth::user()->master_user_group_id == 5) || (Auth::user()->master_user_group_id == 7)))
                        <li {{ (Request::is('users') ? 'class=active' : '') }}>
                            <a href="{{ URL('users') }}"> <span class="nav-label">ADMIN MANAGER</span></a>
                        </li>
                    @endif
                    @if(Auth::user()->master_user_group_id == 2)
                        <li {{ (Request::is('licenses') ? 'class=active' : '') }}>
                            <a href="{{ URL('licenses') }}"> <span class="nav-label">Licenses</span></a>
                        </li>
                    @endif
                    @if((Auth::user()->master_user_group_id == 2)||(Auth::user()->master_user_group_id == 3)||(Auth::user()->master_user_group_id == 4))
                        @if(Request::is('roster*'))
                            <li class="active">
                        @else
                            <li>
                                @endif
                            <a href="#"><span class="nav-label">CHECKLISTS</span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                @if((Auth::user()->master_user_group_id == 2)||(Auth::user()->master_user_group_id == 3))
                                    <li {{ (Request::is('roster/list') ? 'class=active' : '') }}>
                                        <a href="{{ URL('roster/list') }}"> <span class="nav-label">LIST</span></a>
                                    </li>
                                    <li {{ (Request::is('roster/assignees') ? 'class=active' : '') }}>
                                        <a href="{{ URL('roster/assignees') }}"> <span class="nav-label">ASSIGNEE</span></a>
                                    </li>
                                @endif

                                <li {{ (Request::is('roster/jobs') ? 'class=active' : '') }}>
                                    <a href="{{ URL('roster/jobs') }}"> <span class="nav-label">JOBS</span></a>
                                </li>

                            </ul>

                        </li>
                    @endif
                    @if((Auth::user()->master_user_group_id == 2))
                        <li {{ (Request::is('subscription/plan') ? 'class=active' : '') }}>
                            <a href="{{ URL('subscription/plan') }}"> <span class="nav-label">Subscription Plan</span></a>
                        </li>
                    @endif
                    @if((Auth::user()->master_user_group_id == 2) || ((Auth::user()->master_user_group_id == 5) || (Auth::user()->master_user_group_id == 7)))
                        <li {{ (Request::is('payment') ? 'class=active' : '') }}>
                            <a href="{{ URL('payment') }}"> <span class="nav-label">Payments</span></a>
                        </li>
                    @endif



            </ul>

        @endif



    <?php }elseif(Session('company_status') == 0) {?>
        {{--<ul class="nav" id="side-menu">--}}
            {{--<li {{ (Request::is('/') ? 'class=active' : '') }}>--}}
                {{--<a href="{{ URL('dashboard') }}"> <span class="nav-label">Dashboard</span></a>--}}
            {{--</li>--}}
        {{--</ul>--}}
    <?php }elseif(Session('company_status') == 4){?>
    <ul class="nav" id="side-menu">
        <li {{ (Request::is('/') ? 'class=active' : '') }}>
            <a href="{{ URL('dashboard') }}"> <span class="nav-label">Dashboard</span></a>
        </li>
    </ul>
    <?php }elseif(Session('company_status') == 5){?>
    <ul class="nav" id="side-menu">
        <li {{ (Request::is('/') ? 'class=active' : '') }}>
            <a href="{{ URL('dashboard') }}"> <span class="nav-label">Dashboard</span></a>
        </li>
    </ul>
    <?php }?>
</div>