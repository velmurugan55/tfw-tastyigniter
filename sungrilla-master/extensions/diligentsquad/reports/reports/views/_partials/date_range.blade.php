<p>&nbsp;</p>
<div class="d-sm-flex flex-sm-wrap w-100 no-gutters row">

    <div class="col-lg-2">
        <div class="float-right search btn-group" style="left:10%;">
            @php
                $search_cnt = (isset($_REQUEST['search']))?$_REQUEST['search']:'';
            @endphp
            <input class="form-control search-input" type="search" id="search_value" name="search_value" value="{{ $search_cnt }}" placeholder="Search" autocomplete="off">
            <button type="button" class="btn btn-primary" id="menusearch"><i class="fa fa-search"></i></button>
        </div>
    </div>

    <div class="col col-md-9 col-lg-9">
        <div class="row">
            <div class="col col-md-1">&nbsp;</div>
            <div class="col col-md-7">
                @php
                    $startdate = (isset($_REQUEST['start_date']))?$_REQUEST['start_date']:date("Y-m-d");
                    $enddate = (isset($_REQUEST['end_date']))?$_REQUEST['end_date']:date("Y-m-d");
                    $st_date = date("m/d/Y",strtotime($startdate));
                    $en_date = date("m/d/Y",strtotime($enddate));
                @endphp
                <script type="text/javascript" src="{{ site_url('app/admin/assets/js/jquery.min.js') }}"></script>
                <script type="text/javascript" src="{{ site_url('app/admin/assets/js/moment.min.js') }}"></script>
                <script type="text/javascript" src="{{ site_url('app/admin/assets/js/daterange/daterangepicker.min.js') }}" defer></script>
                <link rel="stylesheet" type="text/css" href="{{ site_url('app/admin/assets/css/daterange/daterangepicker.css') }}" />
                <div class="input-group">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    <input type="text" name="daterange" value="{{ $st_date }} - {{ $en_date }}"  class="form-control text-secondary"/>
                    <input type = "hidden" name="start_date" id="start_date" value="{{ $startdate }}" />
                    <input type = "hidden" name="end_date" id="end_date"  value="{{ $enddate }}" />
                    <button type="button" class="btn btn-primary" id="view_report_dates">@lang('thoughtco.reports::default.btn_view')</button>&nbsp;
                    <button type="button" class="btn btn-outline-danger" id="report_clear"> X </button>
                </div></div>
            <div class="col col-md-4">&nbsp;</div>
        </div>

        <script type="text/javascript">
            $(function() {
                $('input[name="daterange"]').daterangepicker({
                    opens: 'left',
                    startDate: '{{ $st_date }}',
                    endDate:'{{ $en_date }}'
                }, function(start, end, label) {
                    $("#start_date").val(start.format('YYYY-MM-DD'));
                    $("#end_date").val(end.format('YYYY-MM-DD'));
                });
                $("#view_report_dates").click(function()
                {
                    var st_date = $("#start_date").val();
                    var en_date = $("#end_date").val();
                    var search_val = $("#search_value").val();
                    if(search_val != "") {
                        var query_str = 'search=' + search_val + '&start_date=' + st_date + '&end_date=' + en_date;
                    }
                    else {
                        var query_str = 'start_date=' + st_date + '&end_date=' + en_date;

                    }
                    document.location = '{{ admin_url('diligentsquad/reports/fullreports/loadReport') }}?'+query_str;
                });
                $("#report_clear").click(function()
                {
                    document.location = '{{ admin_url('diligentsquad/reports/fullreports/loadReport') }}';
                });
                $("#menusearch").click(function()
                {
                        var st_date = $("#start_date").val();
                        var en_date = $("#end_date").val();
                        var search_val = $("#search_value").val();
                    if(search_val != "") {
                        var query_str = 'search=' + search_val + '&start_date=' + st_date + '&end_date=' + en_date;
                    }
                    else {
                        var query_str = 'start_date=' + st_date + '&end_date=' + en_date;

                    }
                    document.location = '{{ admin_url('diligentsquad/reports/fullreports/loadReport') }}?'+query_str;
                });
            });
        </script>

    </div>

</div>
