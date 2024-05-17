@php
/* Config params */
$showLocalInfo = true;
$showOrderInfo = true;
$showCustomerInfo = true;
$showAddress = true;
$showComment = true;
$showQRCode = true;
$showPaymentInfo= true;
$showIssueMessage = true;

/* Strings - General  */
$stringTelephone = "Telephone no:";
$stringReceiptTitle = "CUSTOMER COPY";
$stringOrderNumber = "ORDER #:";
$stringOrderFor = "Order for:";
$stringName = "Name:";
$stringCustomerPhone = "Phone number:";
$stringDeliveryAddress = "Delivery address:";
$stringQRPhrase = "Bring me there:";
$stringNotPaid = "NOT PAID";
$stringPaid = "PAID";
$stringIssuePhrase = "Any issues with your order please contact";
$stringComment = "Comment:";

/* Strings - Customer Receipt */
$stringItems = "items";

@endphp

||@if ($showLocalInfo)
#### {{ $location_name }}
##### {{ $location_address }}
##### {!! $stringTelephone !!} {{ $location_telephone }}
##### {{ $site_url }}

@endif
#### {!! $stringReceiptTitle !!}

@if ($showOrderInfo)
### {!! $stringOrderNumber !!} {{ $order_id }}
##### {!! $stringOrderFor !!} {{ ucfirst($order_type) }}

@endif
<|@if ($showOrderInfo)-----
### {{ $order_date }} {{ $order_time }}
@endif
-----
@if ($showCustomerInfo)
{!! $stringName !!} {{ ucwords($customer_name) }}
{!! $stringCustomerPhone !!} {{ $telephone }}
@endif
@if ($order_type == 'delivery' && $showAddress)
{!! $stringDeliveryAddress !!}
{{ $order_address }}
@endif
@if (trim($order_comment) != '' && $showComment)
{!! $stringComment !!}
{!! $order_comment !!}
@endif
-----
@if ($order_type == 'delivery' && $showQRCode)

### {!! $stringQRPhrase !!}
[qrcode 5,https://www.google.com/maps/dir/?api=1&destination={{ urlencode($order_address) }}]

-----
@endif

@php $totalItems = 0; @endphp
@foreach ($order_menus as $menu) @php $totalItems += $menu['menu_quantity']; @endphp
#### {{ str_pad(substr($menu['menu_quantity'].'x '.$menu['menu_name'], 0, $charsPerRow - 11), $charsPerRow - 11, ' ', STR_PAD_RIGHT) }}    {{ str_pad($menu['menu_price'], 7, ' ', STR_PAD_LEFT) }}
@if ($menu['menu_options'])@foreach ($menu['menu_options'] as $option)
{{ str_pad(substr($option['menu_option_quantity'].'x '.$option['menu_option_name'], 0, $charsPerRow - 11), $charsPerRow - 11, ' ', STR_PAD_RIGHT) }}    {{ str_pad($option['menu_option_price'], 7, ' ', STR_PAD_LEFT) }}
@endforeach
@endif
@endforeach

{{ $totalItems }} {!! $stringItems !!}
@foreach ($order_totals as $total)
@if (in_array($total['order_total_code'], ['subtotal', 'total']))#### @endif{!! str_pad(substr(strtoupper($total['order_total_title']), 0, $charsPerRow - 11), $charsPerRow - 11, ' ', STR_PAD_RIGHT) !!}    {!! str_pad($total['order_total_value'], 7, ' ', STR_PAD_LEFT) !!}
@endforeach

@foreach ($order_totals as $total)
@if (in_array($total['order_total_title'], ['tip']))### {!! $total['order_total_title'].': '.$total['order_total_value'] !!}@endif
@endforeach
@if ($showPaymentInfo OR $showIssueMessage)
-----
@endif
||
@if ($showPaymentInfo)
#### {{ $order_payment }}

##@if (in_array($order_payment_code, ['none', 'cod'])) {!! $stringNotPaid !!} @else {!! $stringPaid !!} @endif

@endif
@if ($showIssueMessage )
{!! $stringIssuePhrase !!}
{{ $location_telephone }}
@endif
