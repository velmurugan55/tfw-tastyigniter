<?php namespace Diligentsquad\Reports;

use System\Classes\BaseExtension;

/**
 * reports Extension Information File
 */
class Extension extends BaseExtension
{
    /**
     * Register method, called when the extension is first registered.
     *
     * @return void
     */
    public function register()
    {

    }
    public function extensionMeta()
    {
        return [
            'name' => 'TouchFreeWaiter Full Report Generation Features',
            'author' => 'Diligentsquad',
            'description' => 'Provides full report generation for Report Module in TouchFreeWaiter',
            'icon' => 'fa fa-file',
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Registers any front-end components implemented in this extension.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
// Remove this line and uncomment the line below to activate
//            'Diligentsquad\Reports\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any admin permissions used by this extension.
     *
     * @return array
     */
    public function registerPermissions()
    {
// Remove this line and uncomment block to activate
        return [
//            'Diligentsquad.Reports.SomePermission' => [
//                'description' => 'Some permission',
//                'group' => 'module',
//            ],
        ];
    }
}
