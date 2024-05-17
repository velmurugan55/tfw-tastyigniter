<?php
use \Admin\Facades\AdminLocation;
use DB;
return [
    'list' => [
        'filter' => [
            'search' => [
                'prompt' =>  'lang:diligentsquad.reports::default.search_item_name',
                'mode' => 'all',
            ],
            'scopes' => [
                'report_daterange' => [
                    'label' => 'lang:admin::lang.text_filter_date',
                    'type' => 'report_daterange',
                    'conditions' => 'orders.order_date >= CAST(:filtered_start AS DATE) AND orders.order_date <= CAST(:filtered_end AS DATE)',
                ],
            ],
        ],
        'columns' => [
            'menu_name' => [
                'label' => 'lang:diligentsquad.reports::default.item_name',
                'type' => 'text',
                'searchable' => true,
                'sortable' => true,
                'formatter' => function($record, $column, $value) {

                    if (!AdminLocation::getId() AND !AdminLocation::hasOneLocation())
                        return '';
                    $location_id = AdminLocation::getId() ?? AdminLocation::getDefaultLocation();
                    $name = DB::table('orders')
                        ->join('order_menus','order_menus.order_id', '=', 'orders.order_id')
                        ->where([
                            'orders.status_id' => '5',
                            'orders.location_id' =>$location_id,
                            'order_menus.name' => $value
                        ])
                        ->distinct()
                        ->select('order_menus.name')
                        ->orderBy('order_menus.name', 'ASC')
                        ->get();
                    if (!$name)
                        //return '';

                        return $name;
                }
            ],
            'menu_id' => [
                'label' => 'lang:diligentsquad.reports::default.item_sold',
                'type' => 'text',
                'searchable' => false,
                'sortable' => false,
                'formatter' => function($record, $column, $value) {
                    $startdate = (isset($_REQUEST['start_date']))?$_REQUEST['start_date']:date("Y-m-d", strtotime("-1 months"));
                    $enddate = (isset($_REQUEST['end_date']))?$_REQUEST['end_date']:date("Y-m-d");

                    if (!AdminLocation::getId() AND !AdminLocation::hasOneLocation())
                        return '';
                    $location_id = AdminLocation::getId() ?? AdminLocation::getDefaultLocation();
                    $quantity = DB::table('orders')
                        ->join('order_menus','order_menus.order_id', '=', 'orders.order_id')
                        ->where([
                            'orders.status_id' => '5',
                            'orders.location_id' =>$location_id,
                            'order_menus.menu_id' => $value
                        ])
                        ->whereBetween('orders.order_date', [$startdate, $enddate])
                        ->distinct()
                        ->selectRaw('sum(quantity) as quan')
                        ->groupBy('order_menus.name')
                        ->get();
                    $values = (array) json_decode($quantity);
                    $quansold = array_column($values, 'quan');
                    if (count($values) == 0)
                        return '<span class="text-danger"><b>0</b></span>';
                    return $quansold[0];
                }
            ]
        ],
    ],
];
