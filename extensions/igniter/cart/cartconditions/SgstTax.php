<?php

namespace Igniter\Cart\CartConditions;

use Igniter\Flame\Cart\CartCondition;
use Igniter\Local\Facades\Location;
use System\Models\Currencies_model;

class SgstTax extends CartCondition
{

    protected $taxSgst;

    protected $taxSgstRate;

    protected $taxSgstRateLabel;

    protected $taxInclusive;

    protected $taxDelivery;

    public $priority = 200;

    public function getLabel()
    {
        $label= $this->taxInclusive ? "{$this->taxSgstRateLabel}% ".lang('igniter.cart::default.text_sgst') : "{$this->taxSgstRateLabel}%";
        return sprintf(lang($this->label), $label);
    }

    public function onLoad()
    {
        $this->taxSgst = (bool)setting('tax_sgst', 1);
        $this->taxSgstRate = $this->taxSgstRateLabel = setting('tax_sgst_percentage', 0);
        $this->taxInclusive = !((bool)setting('tax_menu_price', 1));
        if ($this->taxInclusive)
            $this->taxSgstRate /= (100 + $this->taxSgstRate) / 100;
        $this->taxDelivery = (bool)setting('tax_delivery_charge', 0);
    }

    public function beforeApply()
    {
        if (!$this->taxSgst || !$this->taxSgstRate)
            return FALSE;
    }

    public function getActions()
    {
        $precision = optional(Currencies_model::getDefault())->decimal_position ?? 2;
        return [
            [
                'value' => "{$this->taxSgstRate}%",
                'inclusive' => $this->taxInclusive,
                'valuePrecision' => $precision,
            ]
        ];
    }

    public function calculate($total)
    {
        $excludeDeliveryCharge = Location::orderTypeIsDelivery() && !$this->taxDelivery;
        if ($excludeDeliveryCharge) {
            $deliveryCharge = Location::coveredArea()->deliveryAmount($total);
            $total -= (float)$deliveryCharge;
        }
        $result = parent::calculate($total);

        if ($excludeDeliveryCharge) {
            $result += (float)$deliveryCharge;
        }
        return $result;
    }
}
