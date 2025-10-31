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
                    <li class="{{ request()->routeIs('customer.*') ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{route('customer.index')}}">
                            <i class="bx bx-home-alt" aria-hidden="true"></i>
                            <span>Customer List</span>
                        </a>
                    </li>
                    @if(Auth::user()->role_id != 4)
                    <li class="nav-parent  {{ request()->routeIs('staff.*') || request()->routeIs('payment_method.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="bx bx-layout" aria-hidden="true"></i>
                            <span>Setting</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('staff.index') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('staff.index')}}">
                                    Company Staff
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('payment_method.index') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('payment_method.index')}}">
                                    Payment Method
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    <li class="nav-parent {{ request()->routeIs('loan.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="far fa-file-alt" aria-hidden="true"></i>
                            <span>Loan</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('loan.index') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('loan.index')}}">
                                    All Loans
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('loan.create') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('loan.create')}}">
                                    Create Loan
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-parent {{ request()->routeIs('schedule.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="far fa-calendar" aria-hidden="true"></i>
                            <span>Schedules</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('schedule.index') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('schedule.index')}}">
                                    All Schedules
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('schedule.create') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('schedule.create')}}">
                                    Create Schedule
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-parent {{ request()->routeIs('payment.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="far fa-money-bill-alt" aria-hidden="true"></i>
                            <span>Payment</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('payment.index') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('payment.index')}}">
                                    All Payments
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('payment.create') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('payment.create')}}">
                                    Create Payment
                                </a>
                            </li>
                        </ul>
                    </li>
                    @if(Auth::user()->role_id == 1)
                    <li class="nav-parent {{ request()->routeIs('cadmin.*') || request()->routeIs('company.*') || request()->routeIs('branch.*') || request()->routeIs('bank.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="bx bx-layout" aria-hidden="true"></i>
                            <span>Main Setting</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('cadmin.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('cadmin.index')}}">
                                    Company Admin
                                </a>
                            </li>
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
                            <li class="{{ request()->routeIs('race.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('race.index')}}">
                                    Race
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('marital_status.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('marital_status.index')}}">
                                    Marital Status
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('house_ownership.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('house_ownership.index')}}">
                                    House Ownership
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </nav>
            <hr class="separator" />
        </div>
        <script>
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