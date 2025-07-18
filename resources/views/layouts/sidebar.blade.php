@php

$currentRoute = request()->route()->getName();

@endphp
<aside id="sidebar-left" class="sidebar-left">

    <div class="sidebar-header">
        <div class="sidebar-title">
            Navigation
        </div>
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>

    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">

                <ul class="nav nav-main">
                    <li>
                        <a class="nav-link" href="{{route('home')}}">
                            <i class="bx bx-home-alt" aria-hidden="true"></i>
                            <span>Dashboard</span>
                        </a>                        
                    </li>
                    <!-- <li>
                        <a class="nav-link" href="mailbox-folder.html">
                            <span class="float-end badge badge-primary">182</span>
                            <i class="bx bx-envelope" aria-hidden="true"></i>
                            <span>Mailbox</span>
                        </a>                        
                    </li> -->
                    <li class="nav-parent  {{ request()->routeIs('company.*') || request()->routeIs('branch.*') || request()->routeIs('bank.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="bx bx-layout" aria-hidden="true"></i>
                            <span>Main Setting</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('company.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('company.index')}}">
                                    Company
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('branch.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('branch.index')}}">
                                    Branch
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('bank.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('bank.index')}}">
                                    Bank
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <hr class="separator" />
        </div>

        <script>
            // Maintain Scroll Position
            if (typeof localStorage !== 'undefined') {
                if (localStorage.getItem('sidebar-left-position') !== null) {
                    var initialPosition = localStorage.getItem('sidebar-left-position'),
                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');

                    sidebarLeft.scrollTop = initialPosition;
                }
            }
        </script>

    </div>

</aside>
<!-- end: sidebar -->