<?php

namespace Illuminate\Notifications\Channels;

use Illuminate\Notifications\Messages\TwilioMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Twilio;
use Twilio\Rest\Client as TwilioClient;
use Vonage\SMS\Message\SMS;

class TwilioSmsChannel
{
    /**
     * The Vonage client instance.
     *
     * @var \Twilio\Rest\Client
     */
    protected $client;

    /**
     * The phone number notifications should be sent from.
     *
     * @var string
     */
    protected $from;

    /**
     * Create a new Vonage channel instance.
     *
     * @param  \Twilio\Rest\Client  $client
     * @param  string  $from
     * @return void
     */
    public function __construct(TwilioClient $client, $from)
    {
        $this->from = $from;
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$to = $notifiable->routeNotificationFor('twilio', $notification)) {
            return;
        }

        $message = $notification->toTwilio($notifiable);

        if (is_string($message)) {
            $message = new TwilioMessage($message);
        }

        \Illuminate\Support\Facades\Log::info("Msg type: ".$message->type);

        $twilioSms = $this->client->messages->create(
            $to,
            [
                'from' => $message->from ?: $this->from,
                'body' => trim($message->content)
            ]
        );
    }
}