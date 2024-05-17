<?php

namespace Igniter\Cart\CartConditions;

use Igniter\Flame\Cart\CartCondition;
use Igniter\Local\Facades\Location;
use System\Models\Currencies_model;

class CgstTax extends CartCondition
{

    protected $taxCgst;

    public $taxCgstRate;

    protected $taxCgstRateLabel;

    protected $taxInclusive;

    protected $taxDelivery;

    public $priority = 200;

    public function getLabel()
    {
        $label= $this->taxInclusive ? "{$this->taxCgstRateLabel}% ".lang('igniter.cart::default.text_cgst') : "{$this->taxCgstRateLabel}%";
        return sprintf(lang($this->label), $label);
    }

    public function onLoad()
    {
        $this->taxCgst = (bool)setting('tax_cgst', 1);
        $this->taxCgstRate = $this->taxCgstRateLabel = setting('tax_cgst_percentage', 0);
        $this->taxInclusive = !((bool)setting('tax_menu_price', 1));
        if ($this->taxInclusive)
            $this->taxCgstRate /= (100 + $this->taxCgstRate) / 100;
        $this->taxDelivery = (bool)setting('tax_delivery_charge', 0);
    }

    public function beforeApply()
    {
        if (!$this->taxCgst || !$this->taxCgstRate)
            return FALSE;
    }

    public function getActions()
    {
        $precision = optional(Currencies_model::getDefault())->decimal_position ?? 2;
        return [
            [
                'value' => "{$this->taxCgstRate}%",
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
