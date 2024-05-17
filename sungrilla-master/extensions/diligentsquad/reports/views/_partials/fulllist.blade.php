<div class="fixed-table-toolbar row">
    {!! $this->makePartial('date_range') !!}
</div>
<div id="fullreports" class="list-table table-responsive">
    <table id="fullreports" class="table table-hover mb-0 border-bottom" >
        <thead>
        <tr><th>@lang('lang:diligentsquad.reports::default.item_name')</th><th>@lang('lang:diligentsquad.reports::default.item_sold')</th></tr>
        </thead>
        <tbody>
        @if(count($records['top_items']))
            @foreach ($records['top_items'] as $item)
                <tr><td>{{ $item->name }}</td><td>{{ $item->quantity }}</td></tr>
            @endforeach
        @else
            <tr>
                <td colspan="99" class="text-center">@lang('lang:diligentsquad.reports::default.best_selling_empty')</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
<div>
    {!! $this->makePartial('pagination') !!}
</div>
