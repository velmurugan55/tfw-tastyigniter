	<div class="card-title">
		<h1 class="h4"><i class="stat-icon {{ $listIcon }}"></i> @lang($listLabel)</h1>
	</div>				
	<div class="list-group list-group-flush">
	@foreach ($listItems as $item)
		<div class="list-group-item bg-transparent">
			<b>{{ $item->name }}</b> <em class="pull-right">
@if(isset($item->value))
{{ $item->value }}
@endif
@if(isset($item->quantity))
                    {{ $item->quantity }}
                @endif
</em>
		</div>
		@endforeach
		<div>
            @if($listContext == "top_items")
                @if(count($listItems)>9)
                @php
                    $start_dt = isset($_GET['start_date'])?$_GET['start_date']:date("Y-m-d");
                    $end_dt = isset($_GET['end_date'])?$_GET['end_date']:date("Y-m-d");
                @endphp
                <a href="{{ admin_url('diligentsquad/reports/fullreports/loadReport/') }}?start_date={{ $start_dt }}&end_date={{ $end_dt }}">View Full Report</a>
            @else
                <div>&nbsp;</div>
                @endif
            @endif
        </div>
	</div>
