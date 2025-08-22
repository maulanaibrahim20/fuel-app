<aside class="aside aside-fixed">
    <div class="aside-header">
        <a href="../../index.html" class="aside-logo">{{ config('app.name') }}</a>
        <a href="" class="aside-menu-link">
            <i data-feather="menu"></i>
            <i data-feather="x"></i>
        </a>
    </div>
    <div class="aside-body">
        <div class="aside-loggedin">
            <div class="d-flex align-items-center justify-content-start">
                <a href="" class="avatar">
                    <img src="{{ Storage::url(Auth::user()->avatar) ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}"
                        class="rounded-circle" alt="{{ Auth::user()->name }}"></a>
                <div class="aside-alert-link">
                    <a href="" class="new" data-bs-toggle="tooltip" title="You have 2 unread messages"><i
                            data-feather="message-square"></i></a>
                    <a href="" class="new" data-bs-toggle="tooltip" title="You have 4 new notifications"><i
                            data-feather="bell"></i></a>
                    <a href="javascript:void(0)" id="btn-logout" data-bs-toggle="tooltip" title="Sign out">
                        <i data-feather="log-out"></i>
                    </a>

                    <!-- form logout hidden -->
                    <form id="form-logout" action="{{ route('logout') }}" method="Get" style="display:none;">
                        @csrf
                    </form>
                </div>
            </div>
            <div class="aside-loggedin-user">
                <a href="#loggedinMenu" class="d-flex align-items-center justify-content-between mg-b-2"
                    data-bs-toggle="collapse">
                    <h6 class="tx-semibold mg-b-0">{{ Auth::user()->name }}</h6>
                    <i data-feather="chevron-down"></i>
                </a>
                <p class="tx-color-03 tx-12 mg-b-0">{{ Auth::user()->roles->first()->name }}</p>
            </div>
            <div class="collapse {{ Request::is('~admin/profile') ? 'show' : '' }}" id="loggedinMenu">
                <ul class="nav nav-aside mg-b-0">
                    <li class="nav-item {{ Request::is('~admin/profile') ? 'active' : '' }}"><a
                            href="{{ route('admin.profile') }}" class="nav-link"><i data-feather="edit"></i> <span>Edit
                                Profile</span></a></li>
                    <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link btn-logout" id="btn-logout">
                            <i data-feather="log-out"></i>
                            <span>Sign Out</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div><!-- aside-loggedin -->
        <ul class="nav nav-aside">
            <li class="nav-label">Main</li>
            <li class="nav-item {{ Request::is('~admin/dashboard') ? 'active' : '' }}"><a
                    href="{{ route('admin.dashboard') }}" class="nav-link"><i data-feather="home"></i>
                    <span>Dashboard</span></a>
            </li>
            <li class="nav-label mg-t-25">Master Data</li>
            <li class="nav-item with-sub">
                <a href="" class="nav-link"><i data-feather="user"></i> <span>User Pages</span></a>
                <ul>
                    <li><a href="page-profile-view.html">View Profile</a></li>
                    <li><a href="page-connections.html">Connections</a></li>
                    <li><a href="page-groups.html">Groups</a></li>
                    <li><a href="page-events.html">Events</a></li>
                </ul>
            </li>
            <li class="nav-item with-sub">
                <a href="" class="nav-link"><i data-feather="file"></i> <span>Other Pages</span></a>
                <ul>
                    <li><a href="page-timeline.html">Timeline</a></li>
                </ul>
            </li>
            <li class="nav-label mg-t-25">User Interface</li>
            <li class="nav-item"><a href="../../components" class="nav-link"><i data-feather="layers"></i>
                    <span>Components</span></a></li>
            <li class="nav-item"><a href="../../collections" class="nav-link"><i data-feather="box"></i>
                    <span>Collections</span></a></li>
        </ul>
    </div>
</aside>
