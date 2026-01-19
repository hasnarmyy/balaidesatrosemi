<div class="row mt-5 mb-4 footer">
    <div class="col-sm-8">
        <span>Copyright &copy; {{ date('Y') }} - <a class="text-theme" href="#">Designed by Hasna
                Rofifah</a></span>
    </div>
</div>

<script src="{{ asset('assets/js/sweetalert.js') }}"></script>
<script src="{{ asset('assets/js/progressbar.min.js') }}"></script>
<script src="{{ asset('assets/js/charts/jquery.flot.min.js') }}"></script>
<script src="{{ asset('assets/js/charts/jquery.flot.pie.min.js') }}"></script>
<script src="{{ asset('assets/js/charts/jquery.flot.categories.min.js') }}"></script>
<script src="{{ asset('assets/js/charts/jquery.flot.stack.min.js') }}"></script>
<script src="{{ asset('assets/js/charts/chart.min.js') }}"></script>
<script src="{{ asset('assets/js/charts/chartist.min.js') }}"></script>
<script src="{{ asset('assets/js/charts/chartist-data.js') }}"></script>
<script src="{{ asset('assets/js/charts/demo.js') }}"></script>
<script src="{{ asset('assets/js/maps/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('assets/js/maps/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('assets/js/maps/jvector-maps.js') }}"></script>
<script src="{{ asset('assets/js/calendar/bootstrap_calendar.js') }}"></script>
<script src="{{ asset('assets/js/calendar/demo.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>

<script>
    $('.bulk-actions').niceSelect();
    $('#example').DataTable();
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    })
</script>
