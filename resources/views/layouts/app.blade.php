<!doctype html>
<html class="fixed">
	<head>
    @include('layouts.head')
    @include('layouts.css')
	<style>
		 .dt-row {
			overflow-x: auto;
		}
		.cus-header{
			color: #0088cc;
			border-bottom: 2px solid;
		}
	</style>
	@yield('css')
	</head>
	<body>
		
		<div id="loadingOverlay" style="display: none;">
			<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>

		<section class="body">
			<div id="overlay" class="overlay" style="display: none;">
				<div class="spinner"></div>
			</div>
			<!-- start: header -->
			<header class="header">
                @include('layouts.top_profile_bar')
			</header>

			<div class="inner-wrapper">
                @include('layouts.sidebar')
                <section role="main" class="content-body">
                @yield('content')
                </section>
            </div>

		</section>
		<div class="modal" id="passwordModal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form enctype="multipart/form-data" method="post" action="{{ route('change_password') }}" onsubmit="return onSubmitForm()">
					<div class="modal-header">
						<h5 class="modal-title"><b style="color:orange">{{ __('table.change_password') }}</b></h5>
						<a class="btn-close" onclick="closePassModal()" aria-label="Close"></a>
					</div>
					<div class="modal-body">
						@csrf
                        <div class="mb-3">
                            <label class="col-form-label">{{ __('table.password') }}</label>
                            <input class="form-control" type="password" name="new_password" placeholder="password" autocomplete='false'>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label">{{ __('table.confirm_password') }}</label>
                            <input class="form-control" type="password" name="new_password_confirmation" placeholder="confirm password" autocomplete='false'>
                        </div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">{{ __('table.confirm') }}</button>
						<a class="btn btn-default" onclick="closePassModal()">{{ __('table.close') }}</a>
					</div>
					</form>
				</div>
			</div>
		</div>
        @include('layouts.script')
		<script>
			function openPassModal(){
				$("#passwordModal").show()
			}
			function closePassModal(){
				$("#passwordModal").hide()
			}
		</script>
        <script>

            function onSubmitForm() {
                var form = document.querySelector('form');
                if (form.checkValidity()) {
                    showLoading();
                    return true;
                } else {
                    return false;
                }
            }
        </script>
	</body>
</html>
