<?php

namespace Diligentsquad\Reports\Controllers;

use Admin\Facades\AdminAuth;
use Admin\Facades\AdminMenu;
use Diligentsquad\Reports\Classes\FullReportFunction;
use Request;
use Template;
use DB;

/**
 * Fullreports Admin Controller
 */
class Fullreports extends \Admin\Classes\AdminController
{
    protected $requiredPermissions = 'Thoughtco.Reports.Manage';

    public function __construct()
    {
        parent::__construct();

        AdminMenu::setContext('fullreports', 'diligentsquad.reports');
    }
    public function loadReport(){
        \Admin\Facades\Template::setTitle(lang('lang:diligentsquad.reports::default.best_selling_full_report'));
        \Admin\Facades\Template::setHeading(lang('lang:diligentsquad.reports::default.best_selling_full_report'));
        $this->vars['records'] = $this->getBestSellingValues();
        return $this->makeView('fullreports/index');
    }
    public function getBestSellingValues()
    {
        $records = (new FullReportFunction)->bestSelling();
        return $records;
    }
}
