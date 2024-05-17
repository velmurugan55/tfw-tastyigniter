<?php

namespace Diligentsquad\Reports\Models;

use Igniter\Flame\Database\Model;

/**
 * fullreports Model
 */
class Fullreport extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'order_menus';

    public $timestamps = TRUE;

    public $casts = [
        'builderjson' => 'array',
        'list_columns' => 'array',
        'csv_columns' => 'array',
    ];

    protected $fillable = ['name', 'quantity'];


    public $rules = [
        ['name', 'lang:diligentsquad.reports::default.label_title', 'required|string'],
        ['quantity', 'lang:diligentsquad.reports::default.label_title', 'required|string'],
    ];


    public static $allowedSortingColumns = [
        'name asc', 'name desc'
    ];
    public $relation = [
        'belongsTo' => [
            'orders' => ['Admin\Models\Orders_model', 'foreignKey' => 'menu_id', 'otherKey' => 'menu_id']
        ],
    ];
}
