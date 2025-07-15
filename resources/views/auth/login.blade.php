<!doctype html>
<html class="fixed">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

        <title>FB Marketing Backend</title>
		<!-- Web Fonts  -->
		<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

        <!-- Vendor CSS -->
        <link rel="stylesheet" href="{{ asset('porto-assets/vendor/bootstrap/css/bootstrap.css') }}" />
        <link rel="stylesheet" href="{{ asset('porto-assets/vendor/animate/animate.compat.css') }}">
        <link rel="stylesheet" href="{{ asset('porto-assets/vendor/font-awesome/css/all.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('porto-assets/vendor/boxicons/css/boxicons.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('porto-assets/vendor/magnific-popup/magnific-popup.css') }}" />
        <link rel="stylesheet" href="{{ asset('porto-assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css') }}" />

        <!-- Theme CSS -->
        <link rel="stylesheet" href="{{ asset('porto-assets/css/theme.css') }}" />

        <!-- Skin CSS -->
        <link rel="stylesheet" href="{{ asset('porto-assets/css/skins/default.css') }}" />

        <!-- Theme Custom CSS -->
        <link rel="stylesheet" href="{{ asset('porto-assets/css/custom.css') }}">

        <!-- Head Libs -->
        <script src="{{ asset('porto-assets/vendor/modernizr/modernizr.js') }}"></script>
	</head>
	<body>
		<!-- start: page -->
		<section class="body-sign">
			<div class="center-sign">
				<a href="/" class="logo float-start">
					<img src="{{ asset('porto-assets/img/logo.png') }}" height="70" alt="Porto Admin" />
				</a>

				<div class="panel card-sign">
					<div class="card-title-sign mt-3 text-end">
						<h2 class="title text-uppercase font-weight-bold m-0"><i class="bx bx-user-circle me-1 text-6 position-relative top-5"></i> Sign In</h2>
					</div>
					<div class="card-body">
                        <form method="POST" action="{{ route('login') }}" class="theme-form">
                            @csrf
                            <div class="form-group">
                                <label class="col-form-label">Username</label>
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="username" required autocomplete="username" autofocus>
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Password</label>
                                <div class="form-input position-relative">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="password">
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <div class="text-end mt-3">
                                    <button class="btn btn-primary btn-block w-100 btn-square" type="submit">Sign in</button>
                                </div>
                            </div>
                        </form>
					</div>
				</div>

				<p class="text-center text-muted mt-3 mb-3">&copy; Copyright {{ now()->year }}. All Rights Reserved.</p>
			</div>
		</section>
		<!-- end: page -->

		<!-- Vendor -->
        <script src="{{ asset('porto-assets/vendor/jquery/jquery.js') }}"></script>
        <script src="{{ asset('porto-assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js') }}"></script>
        <script src="{{ asset('porto-assets/vendor/popper/umd/popper.min.js') }}"></script>
        <script src="{{ asset('porto-assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('porto-assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
        <script src="{{ asset('porto-assets/vendor/common/common.js') }}"></script>
        <script src="{{ asset('porto-assets/vendor/nanoscroller/nanoscroller.js') }}"></script>
        <script src="{{ asset('porto-assets/vendor/magnific-popup/jquery.magnific-popup.js') }}"></script>
        <script src="{{ asset('porto-assets/vendor/jquery-placeholder/jquery.placeholder.js') }}"></script>

        <!-- Specific Page Vendor -->
        @yield('page-js')

        <!-- Theme Base, Components and Settings -->
        <script src="{{ asset('porto-assets/js/theme.js') }}?aaa"></script>

        <!-- Theme Custom -->
        <script src="{{ asset('porto-assets/js/theme.js') }}?aaa"></script>

        <!-- Theme Initialization Files -->
        <script src="{{ asset('porto-assets/js/theme.init.js') }}"></script>

	</body>
</html>
