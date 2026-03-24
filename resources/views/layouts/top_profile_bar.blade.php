<style>
.dropdown-toggle-no-caret::after {
    display: none !important;
}
</style>
    
    <div class="logo-container">
        <a href="{{route('home')}}" class="logo">
            <img src="{{asset('porto-assets/img/logo.png')}}" width="75" height="35" alt="Porto Admin" />
        </a>

        <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>

    </div>

    <div class="header-right">

        <form action="{{ route('customer.single_customer') }}" method="get" class="search nav-form">
            <div class="input-group">
                <input type="text" class="form-control" name="nric_number" id="q" placeholder="Customer NRIC...">
                <button class="btn btn-default" type="submit"><i class="bx bx-search"></i></button>
            </div>
        </form>

        <span class="separator"></span>

        <!-- Language Selector -->
        <div class="dropdown" style="display: inline-block; vertical-align: middle;">
            <a href="#" class="dropdown-toggle dropdown-toggle-no-caret" data-bs-toggle="dropdown" aria-expanded="false" 
            style="display: inline-block; padding: 10px 25px; color: #777; font-size: 18px; text-decoration: none;">
                <i class="bx bx-globe" style="font-size: 30px;"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{route('change_language','en')}}">English</a></li>
                <li><a class="dropdown-item" href="{{route('change_language','cn')}}">中文</a></li>
                <li><a class="dropdown-item" href="{{route('change_language','bm')}}">Bahasa Melayu</a></li>
            </ul>
        </div>

        <!-- Removed extra separator here -->

        <div id="userbox" class="userbox">
            <a href="#" data-bs-toggle="dropdown">
                <figure class="profile-picture">
                    <img src="{{asset('porto-assets/img/!logged-user.jpg')}}" alt="Joseph Doe" class="rounded-circle" data-lock-picture="{{asset('porto-assets/img/!logged-user.jpg')}}" />
                </figure>
                <div class="profile-info">
                    <span class="name">{{Auth::user()->username??''}}</span>
                    <span class="role">{{Auth::user()->role->title??''}}</span>
                </div>

                <i class="fa custom-caret"></i>
            </a>

            <div class="dropdown-menu">
                <ul class="list-unstyled mb-2">
                    <li class="divider"></li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="#"><i class="bx bx-user-circle"></i> {{ __('sidebar.my_profile') }}</a>
                    </li>
                    <li>
                        <a role="menuitem" tabindex="-1" onclick="openPassModal()" style="cursor:pointer"><i class="bx bx-lock"></i> {{ __('sidebar.change_password') }}</a>
                    </li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bx bx-power-off"></i> {{ __('sidebar.logout') }}</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>