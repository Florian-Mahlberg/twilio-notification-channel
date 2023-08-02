<?php

namespace Illuminate\Notifications;

use Psr\Http\Client\ClientInterface;
use RuntimeException;
use Twilio\Rest\Client;

class Twilio
{
    /**
     * The Vonage configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The HttpClient instance, if provided.
     *
     * @var \Psr\Http\Client\ClientInterface
     */
    protected $client;

    /**
     * Create a new Vonage instance.
     *
     * @param  array  $config
     * @param  \Psr\Http\Client\ClientInterface|null  $client
     * @return void
     */
    public function __construct(array $config = [], ?ClientInterface $client = null)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Create a new Vonage instance.
     *
     * @param  array  $config
     * @param  \Psr\Http\Client\ClientInterface|null  $client
     * @return static
     */
    public static function make(array $config = [], ?ClientInterface $client = null)
    {
        return new static($config, $client);
    }

    /**
     * Create a new Vonage Client.
     *
     * @return \Twilio\Rest\Client
     *
     * @throws \RuntimeException
     */
    public function client()
    {
        $basicCredentials = null;

        if ($apiSecret = $this->config['api_token'] ?? null) {
            $basicCredentials = [$this->config['api_sid'], $this->config['api_token']];
        }

        if (!$basicCredentials) {
            $combinations = [
                'api_sid + api_token',
            ];

            throw new RuntimeException(
                'Please provide your Twilio API credentials. Possible combinations: '
                . join(', ', $combinations)
            );
        }

        return new Client(...$basicCredentials);
    }
}