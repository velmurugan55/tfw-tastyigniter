<?php

namespace Diligentsquad\Reports\Classes;

use Admin\Facades\AdminLocation;
use Admin\Models\Locations_model;
use Admin\Models\Menus_model;
use Admin\Models\Orders_model;
use Carbon\Carbon;
use DB;
use Request;

class FullReportFunction {

    public function bestSelling(){
        $peritem = 10;
        $page_number = Request::get('page')?Request::get('page'):1;

        $end_items = $page_number*$peritem;
        $start_items = $end_items-$peritem+1;

        $locationModel = AdminLocation::getId() ? Locations_model::find(AdminLocation::getId()) : false;

        $st_date = Request::get('start_date');
        $en_date = Request::get('end_date');
        $startDate = ($st_date != "")?$st_date:date("Y-m-d");
        $endDate = ($en_date != "")?$en_date:date("Y-m-d");

        $startDate = new Carbon($startDate);
        $endDate = new Carbon($endDate);
        $results = [
            'top_items' => []
        ];
        $results['page_number'] = $page_number;
        $statusesToQuery = setting('completed_order_status');

        // get order ids for the time period
        $orders = Orders_model::whereBetween('order_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereIn('status_id', $statusesToQuery);

        if ($locationModel) {
            $orders->where('location_id', $locationModel->getKey());
        }

        $search_value = Request::get('search')?Request::get('search'):'';

        $orders = $orders->get();
        $orderIds =  $orders->pluck('order_id');

         if($search_value != '') {
             $orderItems = DB::table('order_menus')
                 ->whereIn('order_id', $orderIds)
                 ->where('name', 'like', '%' . $search_value . '%')
                 ->get();
         }
         else {
             $orderItems = DB::table('order_menus')
                 ->whereIn('order_id', $orderIds)
                 ->get();
         }
        $order_items = $orderItems = $orderItems
            ->groupBy('menu_id')
            ->map(function($orderItems, $key) {
                if (!$first = $orderItems->first())
                    return false;

                return (object)[
                    'subtotal' => $orderItems->sum('subtotal'),
                    'quantity' => $orderItems->sum('quantity'),
                    'menu_id' => $key,
                    'name' => $first->name,
                ];
            });

        $results['total_items'] = $orderItems->count();
        $results['total_pages'] = ceil($results['total_items']  / $peritem);

        // get a list of menus vs categories
        $menusCategories = Menus_model::get()
            ->map(function($menu) {
                return (object)[
                    'menu_id' => $menu->menu_id,
                    'name' => $menu->menu_name,
                    'categories' => $menu->categories->pluck('category_id')->toArray()
                ];
            })
            ->keyBy('menu_id');

        // build menu items to include zero sales
        $menuSalesWithZero = $menusCategories->map(function($menu) use ($orderItems){
            if ($el = $orderItems->firstWhere('menu_id', $menu->menu_id))
                return $el;

            return (object)[
                'subtotal' => 0,
                'quantity' => 0,
                'menu_id' => $menu->menu_id,
                'name' => $menu->name,
            ];
        });

        $results['first_item'] = $start_items;
        if($results['total_pages'] == $page_number) {
            $results['last_item'] = $results['total_items'];
        }
        else{
            $results['last_item'] = $end_items;
        }
        $results['top_items'] = $orderItems
            ->sortBy('name')
            ->sortByDesc('quantity')
            ->slice($start_items-1,$peritem);

        return $results;
    }
}
?>
