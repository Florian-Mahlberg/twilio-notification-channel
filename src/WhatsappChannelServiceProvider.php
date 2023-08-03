<?php

namespace Illuminate\Notifications;

use Illuminate\Notifications\Channels\TwilioWhatsappChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Vonage\Client;

class TwilioChannelServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/twilio.php', 'twilio');

        $this->app->singleton(Client::class, function ($app) {
            return Twilio::make($app['config']['twilio'])->client();
        });

        $this->app->bind(TwilioWhatsappChannel::class, function ($app) {
            return new TwilioWhatsappChannel(
                $app->make(Client::class),
                $app['config']['twilio.sms_from']
            );
        });

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('twilio', function ($app) {
                return $app->make(TwilioWhatsappChannel::class);
            });
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/twilio.php' => $this->app->configPath('twilio.php'),
            ], 'twilio');
        }
    }
}