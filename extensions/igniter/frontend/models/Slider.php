<?php

namespace Igniter\Frontend\Models;

use Igniter\Flame\Database\Attach\HasMedia;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Traits\Validation;

/**
 * Slider Model
 */
class Slider extends Model
{
    use Validation;
    use HasMedia;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'igniter_frontend_sliders';

    /**
     * @var array fillable fields
     */
    protected $guarded = [];

    public $timestamps = TRUE;

    public $rules = [
        ['name', 'admin::lang.label_name', 'required|string'],
        ['code', 'igniter.frontend::default.slider.label_code', 'required|alpha_dash'],
    ];

    public $mediable = [
        'images' => ['multiple' => TRUE],
    ];

    public function getMorphClass()
    {
        return 'sliders';
    }
}
