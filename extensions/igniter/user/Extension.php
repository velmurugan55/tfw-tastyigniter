<?php

namespace Igniter\User;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Main\Facades\Auth;

class Extension extends \System\Classes\BaseExtension
{
    public function register()
    {
        $this->registerEventGlobalParams();

        $this->registerRequestRebindHandler();
    }

    public function boot()
    {
        $this->configureRateLimiting();
    }

    public function registerAutomationRules()
    {
        return [
            'events' => [
                'igniter.user.register' => \Igniter\User\AutomationRules\Events\CustomerRegistered::class,
            ],
            'actions' => [],
            'conditions' => [
                \Igniter\User\AutomationRules\Conditions\CustomerAttribute::class,
            ],
        ];
    }

    public function registerComponents()
    {
        return [
            'Igniter\User\Components\Session' => [
                'code' => 'session',
                'name' => 'lang:igniter.user::default.session.component_title',
                'description' => 'lang:igniter.user::default.session.component_desc',
            ],
            'Igniter\User\Components\Account' => [
                'code' => 'account',
                'name' => 'lang:igniter.user::default.account.component_title',
                'description' => 'lang:igniter.user::default.account.component_desc',
            ],
            'Igniter\User\Components\ResetPassword' => [
                'code' => 'resetPassword',
                'name' => 'lang:igniter.user::default.reset.component_title',
                'description' => 'lang:igniter.user::default.reset.component_desc',
            ],
            'Igniter\User\Components\AddressBook' => [
                'code' => 'accountAddressBook',
                'name' => 'lang:igniter.user::default.addressbook.component_title',
                'description' => 'lang:igniter.user::default.addressbook.component_desc',
            ],
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'igniter.user::mail.password_reset' => 'Password reset email to customer',
            'igniter.user::mail.password_reset_request' => 'Password reset request email to customer',
            'igniter.user::mail.registration' => 'Registration email to customer',
            'igniter.user::mail.registration_alert' => 'Registration email to admin',
            'igniter.user::mail.activation' => 'Registration activation email to customer',
        ];
    }

    public function registerActivityTypes()
    {
        return [
            ActivityTypes\CustomerRegistered::class => 'customerRegistered',
        ];
    }

    protected function registerEventGlobalParams()
    {
        if (class_exists(\Igniter\Automation\Classes\EventManager::class)) {
            \Igniter\Automation\Classes\EventManager::instance()->registerCallback(function ($manager) {
                $manager->registerGlobalParams([
                    'customer' => Auth::customer(),
                ]);
            });
        }

        if (class_exists(\Igniter\EventRules\Classes\EventManager::class)) {
            \Igniter\EventRules\Classes\EventManager::instance()->registerCallback(function ($manager) {
                $manager->registerGlobalParams([
                    'customer' => Auth::customer(),
                ]);
            });
        }
    }

    protected function registerRequestRebindHandler()
    {
        $this->app->rebinding('request', function ($app, $request) {
            if ($request instanceof \Dingo\Api\Http\Request)
                return;

            $request->setUserResolver(function () use ($app) {
                if ($app->runningInAdmin())
                    return $app['admin.auth']->getUser();

                return $app['auth']->getUser();
            });
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('web', function (\Illuminate\Http\Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->getKey() ?: $request->ip());
        });

        if ($this->app->runningInAdmin())
            return;

        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->appendMiddlewareToGroup('web', \Igniter\User\Middleware\ThrottleRequests::class);

        Event::listen('igniter.user.beforeThrottleRequest', function ($request, $params) {
            $handler = str_after($request->header('x-igniter-request-handler'), '::');
            if (in_array($handler, [
                'onLogin',
                'onRegister',
                'onActivate',
                'onForgotPassword',
                'onResetPassword',
            ])) {
                $params->maxAttempts = 6;
                $params->decayMinutes = 1;

                return TRUE;
            }
        });
    }
}
