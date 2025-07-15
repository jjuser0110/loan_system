
<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">
<!-- Vendor CSS -->
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/bootstrap/css/bootstrap.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/animate/animate.compat.css') }}">
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/font-awesome/css/all.min.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/boxicons/css/boxicons.min.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/magnific-popup/magnific-popup.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/jquery-ui/jquery-ui.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/jquery-ui/jquery-ui.theme.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/bootstrap-multiselect/css/bootstrap-multiselect.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/morris/morris.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/select2/css/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/select2-bootstrap-theme/select2-bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('porto-assets/vendor/datatables/media/css/dataTables.bootstrap5.css') }}" />

<!-- Theme CSS -->
<link rel="stylesheet" href="{{ asset('porto-assets/css/theme.css') }}" />

<!-- Skin CSS -->
<link rel="stylesheet" href="{{ asset('porto-assets/css/skins/default.css') }}" />

<!-- Theme Custom CSS -->
<link rel="stylesheet" href="{{ asset('porto-assets/css/custom.css') }}" />

<!-- Head Libs -->
<script src="{{ asset('porto-assets/vendor/modernizr/modernizr.js') }}"></script>

<style>
    .brown{
        color:brown;
    }   

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Grey background with opacity */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999; /* Ensure it covers other elements */
    }

    /* Spinner */
    .spinner {
        border: 12px solid #f3f3f3;
        border-radius: 50%;
        border-top: 12px solid #3498db;
        width: 60px;
        height: 60px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>