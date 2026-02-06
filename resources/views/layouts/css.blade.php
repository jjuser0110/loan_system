
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
#loadingOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5); /* dim background */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
</style>

<style>
    :root{
        --background-outstanding: #fff0f0
    }

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

    .cus-action-wrapper{
        display: flex;
        align-items: center;
        gap: 5px
    }
    .cus-action-icon{
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        border-radius: 5px; 
        color: white;
        transition: 0.3s;
    }

    .cus-action-icon:hover{
        transform: translateY(-2px);
        color: white
    }

    .cus-action-icon.info{
        background: #0088CC
    }

    .cus-action-icon.danger{
        background: #cc0000
    }

    .cus-action-icon.success{
        background: #00c221
    }

    .cus-action-icon.warning{
        background: #ffc800
    }

    ..cus-display-only label, .cus-input-show label{
        font-size: 12px !important
    }

    .cus-display-only input, .cus-input-show input{
        min-height: auto !important;
        padding: 5px !important;
        line-height: 1 !important;
        font-size: 12px !important;
        font-weight: 600
    }

    .cus-summary{
        vertical-align: middle !important
    }

    .cus-summary .summary{
        min-height: auto!important
    }

    .cus-summary .title{
        margin-bottom: 5px !important
    }

    .cus-summary .summary-footer{
        margin-top:5px;
        text-align: left
    }

    #form-loan-overview .card-body{
        box-shadow: none;
        border: 1px solid #f1f1f1
    }

    .dataTables_wrapper .cus-table.dataTable{
        margin-top: 15px !important
    }

    .cus-table th{
        font-size: 12px !important;
        font-weight: 600
    }

    .cus-table td{
        font-size: 12px !important;
    }

    .p-note{
        color: red;
        margin: 0;
        padding-top: 2px;
        font-size: 12px;
        margin-left: 5px;
    }

    .swal2-container{
        z-index: 9999999
    }

    .btn-remove-image{
        color: #fff;
        display: flex;
        box-sizing: border-box;
        height: 24px;
        width: 24px;
        padding: 5px !important;
    }

    .btn-remove-image svg > *{
        background: white;
    }
</style>