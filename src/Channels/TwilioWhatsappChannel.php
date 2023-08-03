<?php

namespace Illuminate\Notifications\Channels;

use Illuminate\Notifications\Messages\WhatsappMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Twilio;
use Twilio\Rest\Client as TwilioClient;
use Vonage\SMS\Message\SMS;

class TwilioWhatsappChannel
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
        if (!$to = $notifiable->routeNotificationFor('whatsapp', $notification)) {
            return;
        }

        $message = $notification->toWhatsapp($notifiable);

        if (is_string($message)) {
            $message = new WhatsappMessage($message);
        }

        $twilioSms = $this->client->messages->create(
            "whatsapp:".$to,
            [
                'from' => "whatsapp:".$message->from ?: $this->from,
                'body' => trim($message->content)
            ]
        );
    }
}