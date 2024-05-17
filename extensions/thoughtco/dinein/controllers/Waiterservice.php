<?php

namespace Thoughtco\Dinein\Controllers;

use Admin\Models\Payments_model;
use AdminAuth;
use AdminMenu;
use Admin\Facades\AdminLocation;
use Admin\Models\Orders_model;
use Admin\Models\Tables_model;
use Admin\Widgets\Toolbar;
use ApplicationException;
use Carbon\Carbon;
use DB;
use Template;
use Thoughtco\Printer\Models\Printer;
use Diligentsquad\Gst\Models\GstSettings;

class Waiterservice extends \Admin\Classes\AdminController
{
    public $implement = [
        'Admin\Actions\ListController',
    ];

    public $listConfig = [
        'list' => [
            'model' => 'Admin\Models\Tables_model',
            'title' => 'lang:thoughtco.dinein::default.text_list_title',
            'emptyMessage' => 'lang:thoughtco.dinein::default.text_empty',
            'defaultSort' => ['id', 'DESC'],
            'configFile' => 'waiterservice',
        ],
    ];

    protected $requiredPermissions = 'Thoughtco.Dinein.*';

    public function __construct()
    {
        parent::__construct();
        AdminMenu::setContext('sales', 'waiter');
    }

    public function index()
    {
        $this->asExtension('ListController')->index();
    }

    public function close($context, $id)
    {
        if (!AdminAuth::user()->hasPermission('Thoughtco.Dinein.WaiterService'))
            throw new ApplicationException('Permission denied');

        Template::setTitle(__('lang:thoughtco.dinein::default.text_list_title'));

        $table = Tables_model::find($id);

        //List of Payments
        $payment = Payments_model::listDropdownOptions();

        $open_orders = $this->openOrdersQuery($id);


        if (!$open_orders->count() OR !$table)
            return redirect(admin_url('thoughtco/dinein/waiterservice'));

        $this->vars['table'] = $table;
        $this->vars['menuItems'] = [];
        $this->vars['menuFullOrder'] = [];
	$this->vars['totalItems'] = 0;
        $this->vars['orderTotal'] = 0;
        $this->vars['orderTotals'] = [];
        $this->vars['payments'] = $payment;
        $this->vars['print_url'] = 'thoughtco/dinein/waiterservice/print/'.$id;
        $orderMenuIds = array();
        $orderMenus = array();

        if (!AdminAuth::user()->hasPermission('Thoughtco.Dinein.WaiterService.SingleOrderItemDelete'))
            $this->vars['singleOrderItemDelete'] = 0;
        else
            $this->vars['singleOrderItemDelete'] = 1;


	foreach ($open_orders as $order) {

            foreach ($order->getOrderMenusWithOptions() as $menu) {
                //print_r($menu);
                $menu_options = $menu->menu_options->map(function ($option) {
                    return [
                        'menu_id' => $option->menu_id,
                        'option_name' => $option->order_option_name,
                        'option_price' => $option->order_option_price,
                        'menu_option_id' => $option->order_menu_option_id,
                        'option_value_id' => $option->menu_option_value_id,
                        'option_quantity' => $option->quantity,
                        'option_category' => $option->order_option_category,
                    ];
                });
                $key = md5($menu->menu_id.$menu_options->toJson());
                if (isset($orderMenus[$menu->order_id][$menu->menu_id])){
                    $orderMenus[$menu->order_id][$menu->menu_id]->quantity += $menu->quantity;
                    $orderMenus[$menu->order_id][$menu->menu_id]->subtotal += $menu->subtotal;
                    $orderMenuIds[] = $menu->order_menu_id;
                } else {
                    $orderMenus[$menu->order_id][$menu->menu_id] = $menu;
                    $orderMenuIds[] = $menu->order_menu_id;
                }
            }
            $this->vars['orderMenuIds'] = implode(",",$orderMenuIds);
        }
        $this->vars['menuFullOrder'] = $orderMenus;


	foreach ($open_orders as $order) {
              foreach ($order->getOrderMenusWithOptions() as $menu) {
                $menu_options = $menu->menu_options->map(function($option) {
                    return [
                        'menu_id' => $option->menu_id,
                        'option_name' => $option->order_option_name,
                        'option_price' => $option->order_option_price,
                        'menu_option_id' => $option->order_menu_option_id,
                        'option_value_id' => $option->menu_option_value_id,
                        'option_quantity' => $option->quantity,
                        'option_category' => $option->order_option_category,
                    ];
                });

                $key = md5($menu->menu_id.$menu_options->toJson());

                if (isset($this->vars['menuItems'][$key])){
                    $this->vars['menuItems'][$key]->quantity += $menu->quantity;
                    $this->vars['menuItems'][$key]->subtotal += $menu->subtotal;
                } else {
                    $this->vars['menuItems'][$key] = $menu;
                }

                $this->vars['totalItems'] += $menu->quantity;
            }

            foreach ($order->getOrderTotals() as $total) {

                $found = false;

                foreach ($this->vars['orderTotals'] as $order_total) {
                    if ($total->code == $order_total->code) {
                        $order_total->value += $total->value;
                        $found = true;
                    }
                }

                if (!$found)
                    $this->vars['orderTotals'][] = $total;
            }

        }
        $order_status = $order->status_id;
 $this->vars['status_id'] = $order_status;
	$toolbar_config = [];
        foreach ($this->vars['orderTotals'] as $total)
            if ($total->code == 'total')
                $this->vars['orderTotal'] = $total->value;
                $toolbar_config['buttons']['back'] = ['label' => 'lang:admin::lang.button_icon_back', 'class' => 'btn btn-default', 'href' => 'thoughtco/dinein/waiterservice'];

                if($order_status == 10 || $order_status == 5 || $order_status == 9) {
                    $toolbar_config['buttons']['saveClose'] = ['label' => 'lang:thoughtco.dinein::default.btn_close_table',
                        'class' => 'btn btn-primary',
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#waiter-modal',];
                   /* $toolbar_config = [
                        'buttons' => [
                              'saveClose' => [
                                'label' => 'lang:thoughtco.dinein::default.btn_close_table',
                                'class' => 'btn btn-primary',
                                'data-toggle' => 'modal',
                                'data-target' => '#waiter-modal',
                            ],
                        ],
                    ];*/
                }
                else{
                    $toolbar_config['buttons']['makePay'] = ['label' => 'lang:thoughtco.dinein::default.btn_make_payment_table',
                        'class' => 'btn btn-primary',
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#dinein-payment-modal',];
                }

        if (!\System\Classes\ExtensionManager::instance()->isDisabled('thoughtco.printer')) {

		    $toolbar_config['buttons']['printall'] = [
            'label' => 'lang:thoughtco.dinein::default.button_print',
            'partial' => 'print/toolbar_print_button',
            'printerList' => $this->getPrinterList(),
            'class' => 'btn btn-primary',
            'data-request' => 'onSave',
            'data-progress-indicator' => 'admin::lang.text_saving',
           ];
/*		$toolbar_config['buttons']['print'] = [
                'label' => 'lang:thoughtco.dinein::default.btn_print',
                'class' => 'btn btn-secondary',
                'href' => admin_url('thoughtco/dinein/waiterservice/print/'.$id),
            ];
 */
        }

        $this->vars['toolbarWidget'] = $this->makeWidget('Admin\Widgets\Toolbar', $toolbar_config);

        return $this->makeView('waiterservice/close');
    }
public function getPrinterList(){
        $printerList = Printer::where(['is_enabled' => true])
            ->get()
            ->map(function($printer) {
                return (object)[
                    'id' => $printer->id,
                    'location' => $printer->location->location_id,
                    'label' => $printer->label,
                ];
            });
        return $printerList;
    }
    /*public function close_onClose($context, $id)
    {
        if (!AdminAuth::user()->hasPermission('Thoughtco.Dinein.WaiterService'))
            throw new ApplicationException('Permission denied');

        $completed_statuses = setting('completed_order_status');

        $open_orders = $this->openOrdersQuery($id);

        $first_order_id = 0;
        $order_totals = [];
        foreach ($open_orders as $idx => $order) {

            foreach ($order->getOrderTotals() as $total) {

                $found = false;

                foreach ($order_totals as $order_total) {
                    if ($total->code == $order_total->code) {
                        $order_total->value += $total->value;
                        $found = true;
                    }
                }

                if (!$found)
                    $order_totals[] = $total;
            }

            if ($idx == 0) {
                $first_order_id = $order->order_id;
            } else {

                DB::table("order_menus")
                    ->where('order_id', $order->order_id)
                    ->update(['order_id' => $first_order_id ]);

                DB::table("order_menu_options")
                    ->where('order_id', $order->order_id)
                    ->update(['order_id' => $first_order_id ]);

                $order->delete();

            }

        }

        $order = Orders_model::find($first_order_id);
        $order->table_count = request()->input('table_count', '');
        $order->table_closed_at = Carbon::now();
        $order->save();

        $order->addOrderTotals(json_decode(json_encode($order_totals), true));
        $order->updateOrderStatus(array_shift($completed_statuses));

        return redirect(admin_url('thoughtco/dinein/waiterservice'));

    }*/

    public function close_onClose($context, $id)
    {
        if (!AdminAuth::user()->hasPermission('Thoughtco.Dinein.WaiterService'))
            throw new ApplicationException('Permission denied');

        $table_count = request()->input('table_count', '');
        $payment_mode = request()->input('payment_mode', '');
        if($table_count == '' && $payment_mode == ''){
            flash()->danger(lang('lang:thoughtco.dinein::default.table_count_payment_mode_error'));
            return redirect(admin_url('thoughtco/dinein/waiterservice/close/' . $id));
        }
        else {
            if ($table_count == '') {
                flash()->danger(lang('lang:thoughtco.dinein::default.table_count_error'));
                return redirect(admin_url('thoughtco/dinein/waiterservice/close/' . $id));
            }
            else if ($payment_mode == '') {
                flash()->danger(lang('lang:thoughtco.dinein::default.payment_mode_error'));
                return redirect(admin_url('thoughtco/dinein/waiterservice/close/' . $id));
            }
            else
            {
            $completed_statuses = setting('completed_order_status');
            $open_orders = $this->openOrdersQuery($id);
            $first_order_id = 0;
            $order_totals = [];
            foreach ($open_orders as $idx => $order) {

                foreach ($order->getOrderTotals() as $total) {

                    $found = false;

                    foreach ($order_totals as $order_total) {
                        if ($total->code == $order_total->code) {
                            $order_total->value += $total->value;
                            $found = true;
                        }
                    }

                    if (!$found)
                        $order_totals[] = $total;
                }

                if ($idx == 0) {
                    $first_order_id = $order->order_id;
                } else {

                    DB::table("order_menus")
                        ->where('order_id', $order->order_id)
                        ->update(['order_id' => $first_order_id]);

                    DB::table("order_menu_options")
                        ->where('order_id', $order->order_id)
                        ->update(['order_id' => $first_order_id]);

                    $order->delete();
                }
            }
            $order = Orders_model::find($first_order_id);
            $order->table_count = request()->input('table_count', '');
            $order->payment = request()->input('payment_mode', '');
            $order->table_closed_at = Carbon::now();
            $order->save();
            $order->addOrderTotals(json_decode(json_encode($order_totals), true));
            $order->updateOrderStatus(array_shift($completed_statuses));

            return redirect(admin_url('thoughtco/dinein/waiterservice'));
            }

        }

    }

    public function print($context, $id)
    {
        if (!AdminAuth::user()->hasPermission('Thoughtco.Dinein.WaiterService'))
            throw new ApplicationException('Permission denied');

        Template::setTitle(__('lang:thoughtco.dinein::default.text_list_title'));

        $table = Tables_model::find($id);

        $open_orders = $this->openOrdersQuery($id);

        if (!$open_orders->count() OR !$table)
            return redirect(admin_url('thoughtco/dinein/waiterservice'));

        $order_menus = collect([]);
        $order_menu_options = collect([]);
        $order_totals = [];
        $first_order_id = 0;
        foreach ($open_orders as $idx => $order) {

            foreach ($order->getOrderTotals() as $total) {

                $found = false;

                foreach ($order_totals as $order_total) {
                    if ($total->code == $order_total->code) {
                        $order_total->value += $total->value;
                        $found = true;
                    }
                }

                if (!$found)
                    $order_totals[] = $total;
            }

            if ($idx == 0) {
                $first_order_id = $order->order_id;
            }

            $order_menus = $order_menus->merge($order->getOrderMenus());
            $order_menu_options = $order_menu_options->merge($order->getOrderMenuOptions());

        }

        $order->printer_menus = $order_menus;
        $order->printer_menu_options = $order_menu_options;
        $order->printer_totals = collect($order_totals);

        // hand off to print docket so we dont need to recreate logic around dockets
        $print_docket = new \Thoughtco\Printer\Controllers\Printdocket;

        $js = '';
        foreach (\PrintHelper::getJavascript() as $jsfile)
            $js .= '<script type="text/javascript" src="'.config('app.url').$jsfile.'"></script>';

        return $js.$print_docket->renderPrintdocket($order);
    }

    public function close_onWsPay($context, $id)
    {

        if (!AdminAuth::user()->hasPermission('Thoughtco.Dinein.WaiterService'))
            throw new ApplicationException('Permission denied');

        Template::setTitle(__('lang:thoughtco.dinein::default.text_list_title'));

        $table = Tables_model::find($id);

        $open_orders = $this->openOrdersQuery($id);
        if( $pay = request()->input('payment_mode')  == ''){
            return redirect(admin_url('thoughtco/dinein/waiterservice/close/'.$id));
        }
        if (!$open_orders->count() OR !$table)
            return redirect(admin_url('thoughtco/dinein/waiterservice'));
        foreach ($open_orders as $idx => $order) {
            $order = Orders_model::find($order->order_id);
            $order->payment = request()->input('payment_mode','');
            $order->status_id = 10;
            $order->table_closed_at = Carbon::now();
            $order->save();
        }
        return redirect(admin_url('thoughtco/dinein/waiterservice/close/'.$id));

    }

    private function openOrdersQuery($id)
    {
        $location_id = AdminLocation::getId() ?? AdminLocation::getDefaultLocation();

        return Orders_model::where([
            'location_id' => $location_id,
            'table_number' => $id,
            'order_type' => 'waiter',
        ])
            ->whereNotIn('status_id', [setting('completed_order_status'),setting('canceled_order_status')])
            ->get();
    }

    public function listExtendQuery($query)
    {
        if ($locationId = $this->getLocationId()){
            $query->whereHasLocation($locationId);
        }
    }
    public function close_onDeleteItem($context,$id){
        $values = request()->all();
        $orderMenuIds = request()->input('orderMenuIds', '');
        $menuIds = explode(",",$orderMenuIds);
        $update_order = array();
        $order_ids = array();
        $order_condition = [];
        $gst = 0.00;
        if(count($menuIds)>0){
            foreach($menuIds as $k=>$menus){
                $reduce_quan = explode("_",$values['quan_'.$menus]);
                $price = $values['price_'.$menus];
                $subtotal = $values['total_'.$menus];
                $order_menu_id = $reduce_quan[0];
                $order_id = $reduce_quan[1];
                $available_quan = $reduce_quan[2];
                $new_quan = $reduce_quan[2] - $reduce_quan[3];
                if(array_key_exists($order_id,$update_order)){
                    $update_order[$order_id] = $update_order[$order_id]+$reduce_quan[3];
                }
                else{
                    $update_order[$order_id] = $reduce_quan[3];
                    $order_ids[] = $order_id;
                }
                if($reduce_quan[3] > 0) {
                    if ($new_quan == 0) {
                        DB::table('order_menus')->where(['order_id' => $order_id,'order_menu_id'=>$order_menu_id])->delete();
                        DB::table('order_menu_options')->where(['order_id' => $order_id,'order_menu_id'=>$order_menu_id])->delete();

                    }
                    $single_price = $price;
                    if(GstSettings::get('tax_sgst') == 1){
                        $tax = GstSettings::get('tax_sgst_percentage')*2;
                        $gst = ($single_price * $new_quan) *($tax/2)/100;
                        DB::table('order_menus')->where(["order_id"=>$order_id,"order_menu_id"=>$order_menu_id])->update([
                            "cgst"=>$gst,
                            "sgst"=>$gst
                        ]);
                    }
                    $price = ($available_quan*$price)-($reduce_quan[3]*$price);
                    $subtotal = $subtotal-$reduce_quan[3]*($subtotal/$available_quan);
                    DB::table('order_menus')->where(["order_id"=>$order_id,"order_menu_id"=>$order_menu_id])->update([
                        "quantity"=>$new_quan,
                        "subtotal"=>$price,
                        "price" =>$single_price]);

                }
                else{
                    $price = $available_quan*$price;
                    if(GstSettings::get('tax_sgst') == 1) {
                        $tax = GstSettings::get('tax_sgst_percentage') * 2;
                        $gst = $price * ($tax / 2) / 100;
                    }
                }

                if(array_key_exists($order_id,$order_condition)){
                    $subtotal += $order_condition[$order_id]['subtotal'];
                    $price += $order_condition[$order_id]['price'];
                    $new_quan += $order_condition[$order_id]['new_quan'];
                    if(GstSettings::get('tax_sgst') == 1) {
                        $gst +=  $order_condition[$order_id]['tax_cgst'];
                        $order_condition[$order_id] = array("price" => $price, "subtotal" => $subtotal, "new_quan" => $new_quan,"tax_cgst" => $gst,"tax_sgst"=>$gst);
                    }
                    else{
                        $order_condition[$order_id] = array("price" => $price, "subtotal" => $subtotal, "new_quan" => $new_quan);
                    }

                }
                else {
                    if(GstSettings::get('tax_sgst') == 1) {
                        $order_condition[$order_id] = array("price" => $price, "subtotal" => $subtotal, "new_quan" => $new_quan,"tax_cgst" => $gst,"tax_sgst"=>$gst);
                    }
                    else{
                        $order_condition[$order_id] = array("price" => $price, "subtotal" => $subtotal,"new_quan"=>$new_quan);
                    }
                }
            }
            //Complete delete of order information
            foreach($update_order as $k => $values){
                $total_items = DB::table('orders')->where('order_id',$k)->pluck('total_items');
                foreach ($total_items as $v){
                    if($update_order[$k]>0) {
                        if ($v == $update_order[$k]) {
                            DB::table('order_menus')->where(['order_id' => $k])->delete();
                            DB::table('orders')->where(['order_id' => $k])->delete();
                            DB::table('order_menu_options')->where(['order_id' => $k])->delete();
                            DB::table('order_totals')->where(['order_id' => $k])->delete();
                        } else {
                            $order_total = $order_condition[$k]['subtotal'];
                            if(GstSettings::get('tax_sgst') == 1) {
                                DB::table('order_totals')->where(["order_id"=>$k,"code"=>'tax_cgst'])->update([
                                    "value"=>$order_condition[$k]['tax_cgst']]);
                                DB::table('order_totals')->where(["order_id"=>$k,"code"=>'tax_sgst'])->update([
                                    "value"=>$order_condition[$k]['tax_sgst']]);
                                $order_total = $order_condition[$k]['subtotal']+$order_condition[$k]['tax_cgst']+$order_condition[$k]['tax_sgst'];
                            }
                            DB::table('order_totals')->where(["order_id"=>$k,"code"=>'subtotal'])->update([
                                "value"=>$order_condition[$k]['price']]);
                            DB::table('order_totals')->where(["order_id"=>$k,"code"=>'total'])->update([
                                "value"=>$order_total]);
                            DB::table('orders')->where(["order_id"=>$k])->update([
                                "total_items"=>$order_condition[$k]['new_quan'],"order_total"=>$order_total]);
                        }
                    }
                }
            }
        }
        flash()->success(lang('lang:thoughtco.dinein::default.alert_single_order_item_success'));
        return redirect(admin_url('thoughtco/dinein/waiterservice/close/'.$id));
    }
}
