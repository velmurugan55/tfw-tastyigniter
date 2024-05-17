<div class="table-responsive" style="width:80%;">
    <form name="form_delete">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Order Id</th>
            <th width="35%">@lang('admin::lang.orders.column_name_option')</th>
            <th width="15%" class="text-center">Ordered Quantity</th>
            <th width="13%" class="text-center">Reduce Quantity</th>
            <th class="text-left">@lang('admin::lang.orders.column_price')</th>
            <th class="text-left">Amount</th>
           
           </tr>
        </thead>
        <tbody>
        @foreach($menuFullOrder as $k=>$menuItems)
            @foreach($menuItems as $menuItem)
                <tr>
                    <td class="align-top">{{ $menuItem->order_id }}</td>
                    <td><b>{{ $menuItem->name }}</b></td>
                    <td class="align-top text-center">{{ $menuItem->quantity }}</td>
                    <td class="align-top text-center">
                        <select name="quan_{{ $menuItem->order_menu_id }}" >
                        @for($i=0;$i<=$menuItem->quantity;$i++)
                            <option value="{{ $menuItem->order_menu_id }}_{{ $menuItem->order_id }}_{{$menuItem->quantity}}_{{$i}}">{{$i}}</option>
                        @endfor
                        </select>
                    </td>
                    <td class="align-top">{{ $menuItem->price }}<input type="hidden" name="price_{{ $menuItem->order_menu_id }}" value = {{ $menuItem->price }}></td>
                    <td class="align-top">{{ ($menuItem->price*$menuItem->quantity) }}
                    <input type="hidden" name="total_{{ $menuItem->order_menu_id }}" value = {{ ($menuItem->price*$menuItem->quantity) }}></td>
               </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
    <div>
        <input type="hidden" name="orderMenuIds" value = {{ $orderMenuIds }}>
        <a
            class="close btn btn-primary"
            role="button"
            data-control="remove"
            data-request="onDeleteItem"
            data-request-confirm="@lang('admin::lang.alert_warning_confirm')"
        >Update Orders</a>
    </div>
</form>
</div>
