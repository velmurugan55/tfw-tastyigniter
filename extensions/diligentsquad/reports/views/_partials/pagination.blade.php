<nav class="pagination-bar d-flex justify-content-end">
    @if (count($records['top_items']) > 0)
        <div class="align-self-center">
            {{ sprintf(lang('admin::lang.list.text_showing'), $records['first_item'] ?? 0, $records['last_item'] ?? 0, $records['total_items']) }}
        </div>
    @endif
        @if ($records['total_pages'] > 1)
            @php

               $startdate = (isset($_REQUEST['start_date']))?$_REQUEST['start_date']:date("Y-m-d", strtotime("-1 months"));
               $enddate = (isset($_REQUEST['end_date']))?$_REQUEST['end_date']:date("Y-m-d");
               $url_string = '&start_date='.$startdate.'&end_date='.$enddate;
               if(isset($_REQUEST['search'])){
                   $url_string .= '&search='.$_REQUEST['search'];
               }
            @endphp
            <ul class="pagination">
                <li class="page-item{{ ($records['page_number'] < 2) ? ' disabled' : '' }}">
                    <a class="page-link" href="{{ admin_url('diligentsquad/reports/fullreports/loadReport') }}?page={{ $records['page_number']-1 }}{{ $url_string }}" rel="prev">&laquo;</a>
                </li>
                @for($i = 1; $i <= $records['total_pages']; $i++)
                    @if($records['page_number'] == $i)
                        <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ admin_url('diligentsquad/reports/fullreports/loadReport') }}?page={{ $i }}{{ $url_string }}">{{ $i }}</a></li>
                    @endif
                @endfor
                @if($records['page_number'] < $records['total_pages'])
                    <li class="page-item">
                        <a class="page-link" href="{{ admin_url('diligentsquad/reports/fullreports/loadReport') }}?page={{ $records['page_number']+1 }}{{ $url_string }}" rel="next">&raquo;</a>
                    </li>
                @else
                    <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                @endif
            </ul>
        @endif
</nav>
