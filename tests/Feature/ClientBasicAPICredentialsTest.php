<?php

namespace Illuminate\Notifications\Tests\Feature;

use Twilio\Rest\Client;
use Vonage\Client\Credentials\Basic;

class ClientBasicAPICredentialsTest extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('twilio.api_sid', 'my_api_sid');
        $app['config']->set('twilio.api_token', 'my_api_token');
    }

    public function testClientCreatedWithBasicAPICredentials()
    {
        $credentials = $this->app->make(Client::class)->getCredentials();

        $this->assertInstanceOf(Basic::class, $credentials);
        $this->assertEquals(['api_sid' => 'my_api_sid', 'api_token' => 'my_api_token'], $credentials->asArray());
    }
}
