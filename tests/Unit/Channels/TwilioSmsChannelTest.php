<?php

namespace Illuminate\Notifications\Tests\Unit\Channels;

use Hamcrest\Core\IsEqual;
use Illuminate\Notifications\Channels\TwilioSmsChannel;
use Illuminate\Notifications\Messages\TwilioMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Twilio\Rest\Client;

class TwilioSmsChannelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testSmsIsSentViaTwilio()
    {
        $notification = new NotificationTwilioSmsChannelTestNotification;
        $notifiable = new NotificationTwilioSmsChannelTestNotifiable;

        $channel = new TwilioSmsChannel(
            $Twilio = m::mock(Client::class), '4444444444'
        );

        $mockSms = (new SMS(
            '5555555555',
            '4444444444',
            'this is my message',
            'text'
        ));

        $Twilio->shouldReceive('sms->send')
            ->with(IsEqual::equalTo($mockSms))
            ->once();

        $channel->send($notifiable, $notification);
    }

    public function testSmsWillSendAsUnicode()
    {
        $notification = new NotificationTwilioUnicodeSmsChannelTestNotification;
        $notifiable = new NotificationTwilioSmsChannelTestNotifiable;

        $channel = new TwilioSmsChannel(
            $Twilio = m::mock(Client::class), '4444444444'
        );

        $mockSms = (new SMS(
            '5555555555',
            '4444444444',
            'this is my message',
            'unicode'
        ));

        $Twilio->shouldReceive('sms->send')
               ->with(IsEqual::equalTo($mockSms))
               ->once();

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaTwilioWithCustomClient()
    {
        $customTwilio = m::mock(Client::class);
        $customTwilio->shouldReceive('sms->send')
            ->with(IsEqual::equalTo(new SMS(
                '5555555555',
                '4444444444',
                'this is my message'
            )))
            ->once();

        $notification = new NotificationTwilioSmsChannelTestCustomClientNotification($customTwilio);
        $notifiable = new NotificationTwilioSmsChannelTestNotifiable;

        $channel = new TwilioSmsChannel(
            $Twilio = m::mock(Client::class), '4444444444'
        );

        $Twilio->shouldNotReceive('sms->send');

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaTwilioWithCustomFrom()
    {
        $notification = new NotificationTwilioSmsChannelTestCustomFromNotification;
        $notifiable = new NotificationTwilioSmsChannelTestNotifiable;

        $channel = new TwilioSmsChannel(
            $Twilio = m::mock(Client::class), '4444444444'
        );

        $mockSms = (new SMS(
            '5555555555',
            '5554443333',
            'this is my message'
        ));

        $Twilio->shouldReceive('sms->send')
            ->with(IsEqual::equalTo($mockSms))
            ->once();

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaTwilioWithCustomFromAndClient()
    {
        $customTwilio = m::mock(Client::class);

        $mockSms = new SMS(
            '5555555555',
            '5554443333',
            'this is my message',
        );

        $customTwilio->shouldReceive('sms->send')
            ->with(IsEqual::equalTo($mockSms))
            ->once();

        $notification = new NotificationTwilioSmsChannelTestCustomFromAndClientNotification($customTwilio);
        $notifiable = new NotificationTwilioSmsChannelTestNotifiable;

        $channel = new TwilioSmsChannel(
            $Twilio = m::mock(Client::class), '4444444444'
        );

        $Twilio->shouldNotReceive('sms->send');

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaTwilioWithCustomFromAndClientRef()
    {
        $notification = new NotificationTwilioSmsChannelTestCustomFromAndClientRefNotification;
        $notifiable = new NotificationTwilioSmsChannelTestNotifiable;

        $channel = new TwilioSmsChannel(
            $Twilio = m::mock(Client::class), '4444444444'
        );

        $mockSms = new SMS(
            '5555555555',
            '5554443333',
            'this is my message',
        );

        $mockSms->setClientRef('11');

        $Twilio->shouldReceive('sms->send')
            ->with(IsEqual::equalTo($mockSms))
            ->once();

        $channel->send($notifiable, $notification);
    }

    public function testSmsIsSentViaTwilioWithCustomClientFromAndClientRef()
    {
        $customTwilio = m::mock(Client::class);

        $mockSms = new SMS(
            '5555555555',
            '5554443333',
            'this is my message',
        );

        $mockSms->setClientRef('11');

        $customTwilio->shouldReceive('sms->send')
            ->with(IsEqual::equalTo($mockSms))
            ->once();

        $notification = new NotificationTwilioSmsChannelTestCustomClientFromAndClientRefNotification($customTwilio);
        $notifiable = new NotificationTwilioSmsChannelTestNotifiable;

        $channel = new TwilioSmsChannel(
            $Twilio = m::mock(Client::class), '4444444444'
        );

        $Twilio->shouldNotReceive('sms->send');

        $channel->send($notifiable, $notification);
    }

    public function testCallbackIsApplied()
    {
        $notification = new NotificationTwilioSmsChannelTestCallback;
        $notifiable = new NotificationTwilioSmsChannelTestNotifiable;

        $channel = new TwilioSmsChannel(
            $Twilio = m::mock(Client::class), '4444444444'
        );

        $mockSms = (new SMS(
            '5555555555',
            '4444444444',
            'this is my message'
        ));

        $mockSms->setDeliveryReceiptCallback('https://example.com');

        $Twilio->shouldReceive('sms->send')
               ->with(IsEqual::equalTo($mockSms))
               ->once();

        $channel->send($notifiable, $notification);
    }
}

class NotificationTwilioSmsChannelTestNotifiable
{
    use Notifiable;

    public $phone_number = '5555555555';

    public function routeNotificationForTwilio($notification)
    {
        return $this->phone_number;
    }
}

class NotificationTwilioSmsChannelTestNotification extends Notification
{
    public function toTwilio($notifiable)
    {
        return new TwilioMessage('this is my message');
    }
}

class NotificationTwilioUnicodeSmsChannelTestNotification extends Notification
{
    public function toTwilio($notifiable)
    {
        return (new TwilioMessage('this is my message'))->unicode();
    }
}

class NotificationTwilioSmsChannelTestCustomClientNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioMessage('this is my message'))->usingClient($this->client);
    }
}

class NotificationTwilioSmsChannelTestCustomFromNotification extends Notification
{
    public function toTwilio($notifiable)
    {
        return (new TwilioMessage('this is my message'))->from('5554443333');
    }
}

class NotificationTwilioSmsChannelTestCustomFromAndClientNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioMessage('this is my message'))->from('5554443333')->usingClient($this->client);
    }
}

class NotificationTwilioSmsChannelTestCustomFromAndClientRefNotification extends Notification
{
    public function toTwilio($notifiable)
    {
        return (new TwilioMessage('this is my message'))->from('5554443333')->clientReference('11');
    }
}

class NotificationTwilioSmsChannelTestCustomClientFromAndClientRefNotification extends Notification
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioMessage('this is my message'))
            ->from('5554443333')
            ->clientReference('11')
            ->usingClient($this->client);
    }
}

class NotificationTwilioSmsChannelTestCallback extends Notification
{
    public function toTwilio($notifiable)
    {
        return (new TwilioMessage('this is my message'))->statusCallback('https://example.com');
    }
}
