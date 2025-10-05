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
<script src="{{ asset('porto-assets/vendor/jquery-ui/jquery-ui.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jqueryui-touch-punch/jquery.ui.touch-punch.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jquery-appear/jquery.appear.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/bootstrap-multiselect/js/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jquery.easy-pie-chart/jquery.easypiechart.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/flot/jquery.flot.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/flot.tooltip/jquery.flot.tooltip.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/flot/jquery.flot.pie.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/flot/jquery.flot.categories.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/flot/jquery.flot.resize.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jquery-sparkline/jquery.sparkline.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/raphael/raphael.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/morris/morris.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/gauge/gauge.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/snap.svg/snap.svg.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/liquid-meter/liquid.meter.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jqvmap/jquery.vmap.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jqvmap/data/jquery.vmap.sampledata.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jqvmap/maps/jquery.vmap.world.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jqvmap/maps/continents/jquery.vmap.africa.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jqvmap/maps/continents/jquery.vmap.asia.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jqvmap/maps/continents/jquery.vmap.australia.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jqvmap/maps/continents/jquery.vmap.europe.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jqvmap/maps/continents/jquery.vmap.north-america.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/jqvmap/maps/continents/jquery.vmap.south-america.js') }}"></script>
<script src="{{ asset('porto-assets/vendor/select2/js/select2.js') }}"></script>
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>

<!-- Theme Base, Components and Settings -->
<script src="{{ asset('porto-assets/js/theme.js') }}"></script>

<!-- Theme Custom -->
<script src="{{ asset('porto-assets/js/custom.js') }}"></script>

<!-- Theme Initialization Files -->
<script src="{{ asset('porto-assets/js/theme.init.js') }}"></script>

<!-- Examples -->
<script src="{{ asset('porto-assets/js/examples/examples.dashboard.js') }}"></script>

@yield('page-js')

@yield('scripts')
<script>
    function showLoading(){
        var overlay = document.getElementById('overlay');
        overlay.style.display = 'flex';
    }

    function hideLoading(){
        var overlay = document.getElementById('overlay');
        overlay.style.display = 'none';
    }

    function formatDate(dateString){
        const date = new Date(dateString);
        return date.getFullYear() + '-' +
           ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
           ('0' + date.getDate()).slice(-2) + ' ' +
           ('0' + date.getHours()).slice(-2) + ':' +
           ('0' + date.getMinutes()).slice(-2);
    }

    function formatCredit(x) {
        return parseFloat(x).toFixed(2);
    }

    function setDefaultSwal(icon, title, text){
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            customClass: {
                htmlContainer: 'cus-swal-html',
                confirmButton: 'cus-swal-confirm',
            }
        })
    }

    function setReloadSwal(icon, title, text){
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            customClass: {
                htmlContainer: 'cus-swal-html',
                confirmButton: 'cus-swal-confirm',
            }
        }).then(function() {
            location.reload();
        });
    }

    function setRedirectSwal(icon, title, text, link){
        Swal.fire({
            icon: icon,
            title: title,
            text:text,
            showCancelButton: false,
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link;
            }
        });
    }

    function setConfirmationSwal(title, text, confirmText = "Yes", cancelText = "No") {
        return Swal.fire({
            title: title,
            text: text,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            customClass: {
                htmlContainer: 'cus-swal-html',
                confirmButton: 'cus-swal-confirm',
                cancelButton: 'cus-swal-cancel',
            }
        });
    }
</script>