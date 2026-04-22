@php
$currentRoute = request()->route()->getName();
@endphp
<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <div class="sidebar-title">
            {{ __('sidebar.navigation') }}
        </div>
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>

    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    @if(Auth::user()->role_id != 4)
                    <li class="{{ request()->routeIs('home') ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{route('home')}}">
                            <i class="bx bx-home-alt" aria-hidden="true"></i>
                            <span>{{ __('sidebar.dashboard') }}</span>
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->role_id == 4)
                    <li class="{{ request()->routeIs('staff.*') ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{route('staff.home')}}">
                            <i class="bx bx-home-alt" aria-hidden="true"></i>
                            <span>{{ __('sidebar.dashboard') }}</span>
                        </a>
                    </li>
                    @endif
                    <li class="{{ request()->routeIs('customer.*') ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{route('customer.index')}}">
                            <i class="bx bx-user" aria-hidden="true"></i>
                            <span>{{ __('sidebar.customer_list') }}</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('reference.*') ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{route('reference.index')}}">
                            <i class="bx bx-user-pin" aria-hidden="true"></i>
                            <span>{{ __('sidebar.reference_list') }}</span>
                        </a>
                    </li>
                    
                    <li class="{{ request()->routeIs('loan.index') ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{route('loan.index')}}">
                            <i class="far fa-file-alt" aria-hidden="true"></i>
                            <span>{{ __('sidebar.all_loans') }}</span>
                        </a>
                    </li>
                    <li class="nav-parent {{ request()->routeIs('schedule.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="far fa-calendar" aria-hidden="true"></i>
                            <span>{{ __('sidebar.schedules') }}</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('schedule.index') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('schedule.index')}}">
                                    {{ __('sidebar.all_schedules') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('schedule.create') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('schedule.create')}}">
                                    {{ __('sidebar.create_schedule') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-parent {{ request()->routeIs('payment.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="far fa-money-bill-alt" aria-hidden="true"></i>
                            <span>{{ __('sidebar.payment') }}</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('payment.index') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('payment.index')}}">
                                    {{ __('sidebar.all_payments') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('payment.create') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('payment.create')}}">
                                    {{ __('sidebar.create_payment') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    @if(Auth::user()->role_id != 4)
                    <li class="nav-parent {{ request()->routeIs('report.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="bx bx-file" aria-hidden="true"></i>
                            <span>{{ __('sidebar.report') }}</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('report.daily_report') || request()->routeIs('report.load_daily_reports') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{ route('report.daily_report') }}">
                                    {{ __('sidebar.daily_report') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('report.cash_book_report') || request()->routeIs('report.load_cash_book_reports') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{ route('report.cash_book_report') }}">
                                    {{ __('sidebar.cash_book_report') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('report.cash_book_report_history') || request()->routeIs('report.load_cash_book_reports') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{ route('report.cash_book_report_history') }}">
                                    {{ __('sidebar.cash_book_report_history') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    <li class="{{ request()->routeIs('expense.*') ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{ route('expense.index') }}">
                            <i class="fas fa-receipt" aria-hidden="true"></i>  {{-- icon added --}}
                            <span>{{ __('sidebar.expenses') }}</span>
                        </a>
                    </li>
                    @if(Auth::user()->role_id != 4)
                    <li class="nav-parent  {{ request()->routeIs('staff.*') || request()->routeIs('payment_method.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="bx bx-layout" aria-hidden="true"></i>
                            <span>{{ __('sidebar.setting') }}</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('staff.index') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('staff.index')}}">
                                    {{ __('sidebar.company_staff') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('payment_method.index') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('payment_method.index')}}">
                                    {{ __('sidebar.payment_method') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if(Auth::user()->role_id != 4)
                    <li class="nav-parent {{ request()->routeIs('badmin.*') || request()->routeIs('cadmin.*') || request()->routeIs('company.*') || request()->routeIs('branch.*') || request()->routeIs('bank.*') || request()->routeIs('employer_type.*') || request()->routeIs('race.*') || request()->routeIs('marital_status.*')|| request()->routeIs('house_ownership.*')|| request()->routeIs('reference_type.*')|| request()->routeIs('expenses_type.*')|| request()->routeIs('non_expenses_type.*') ? 'nav-expanded nav-active' : ''}}">
                        <a class="nav-link" href="#">
                            <i class="bx bx-wrench" aria-hidden="true"></i>
                            <span>{{ __('sidebar.main_setting') }}</span>
                        </a>
                        <ul class="nav nav-children">
                            <li class="{{ request()->routeIs('badmin.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('badmin.index')}}">
                                    {{ __('sidebar.branch_admin') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('cadmin.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('cadmin.index')}}">
                                    {{ __('sidebar.company_admin') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('company.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('company.index')}}">
                                    {{ __('sidebar.company') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('branch.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('branch.index')}}">
                                    {{ __('sidebar.branch') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('bank.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('bank.index')}}">
                                    {{ __('sidebar.bank') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('employer_type.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('employer_type.index')}}">
                                    {{ __('sidebar.employer_type') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('race.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('race.index')}}">
                                    {{ __('sidebar.race') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('marital_status.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('marital_status.index')}}">
                                    {{ __('sidebar.marital_status') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('house_ownership.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('house_ownership.index')}}">
                                    {{ __('sidebar.house_ownership') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('reference_type.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('reference_type.index')}}">
                                    {{ __('sidebar.reference_type') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('expenses_type.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('expenses_type.index')}}">
                                    {{ __('sidebar.expenses_type') }}
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('non_expenses_type.*') ? 'nav-active' : ''}}">
                                <a class="nav-link" href="{{route('non_expenses_type.index')}}">
                                    {{ __('table.non_expenses_type') }}
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