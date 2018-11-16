<?php

namespace Zamovshafu\Devinotelecom;

use Zamovshafu\Devinotelecom\Http\Clients\ClientInterface;
use Zamovshafu\Devinotelecom\Http\Responses\ResponseInterface;

/**
 * Class Service.
 */
final class Service
{
    /**
     * The Devinotelecom client implementation.
     *
     * @var ClientInterface
     */
    private $client;

    /**
     * The short message factory implementation.
     *
     * @var ShortMessageFactoryInterface
     */
    private $factory;

    /**
     * The before callback which will be called before sending single messages.
     *
     * @var callable|null
     */
    private $beforeSingleShortMessageCallback;

    /**
     * The after callback which will be called before sending single messages.
     *
     * @var callable|null
     */
    private $afterSingleShortMessageCallback;

    /**
     * Service constructor.
     *
     * @param  ClientInterface                        $client
     * @param  ShortMessageFactoryInterface           $shortMessageFactory
     * @param  callable|null                          $beforeSingleShortMessageCallback
     * @param  callable|null                          $afterSingleShortMessageCallback
     */
    public function __construct(
        ClientInterface $client,
        ShortMessageFactoryInterface $shortMessageFactory,
        $beforeSingleShortMessageCallback = null,
        $afterSingleShortMessageCallback = null
    ) {
        $this->client = $client;
        $this->factory = $shortMessageFactory;
        $this->beforeSingleShortMessageCallback = $beforeSingleShortMessageCallback;
        $this->afterSingleShortMessageCallback = $afterSingleShortMessageCallback;
    }

    /**
     * Send the given body to the given receivers.
     *
     * @param  array|string|ShortMessage $receivers The receiver(s) of the message or the message object.
     * @param  string|null               $body      The body of the message or null when using short message object.
     *
     * @return ResponseInterface The parsed response object.
     */
    public function sendShortMessage($receivers, $body = null)
    {
        if (! $receivers instanceof ShortMessage) {
            $receivers = $this->factory->create($receivers, $body);
        }

        if (is_callable($this->beforeSingleShortMessageCallback)) {
            call_user_func_array($this->beforeSingleShortMessageCallback, [$receivers]);
        }

        $response = $this->client->sendShortMessage($receivers);

        if (is_callable($this->afterSingleShortMessageCallback)) {
            call_user_func_array($this->afterSingleShortMessageCallback, [$response, $receivers]);
        }

        return $response;
    }

    /**
     * Get statuses of messages.
     *
     * @param  array|ResponseInterface $messages The messages ID or Response with sent messages.
     *
     * @return array The array with parsed response objects.
     */
    public function getMessagesStatus($messages)
    {
        return $this->client->getMessagesStatus($messages);
    }
}
