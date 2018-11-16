<?php

namespace Zamovshafu\Devinotelecom\Http\Clients;

use Zamovshafu\Devinotelecom\ShortMessage;
use Zamovshafu\Devinotelecom\Http\Responses\ResponseInterface;

/**
 * Interface ClientInterface.
 */
interface ClientInterface
{
    /**
     * Send a short message using the Devinotelecom services.
     *
     * @param  ShortMessage $shortMessage
     *
     * @return ResponseInterface
     */
    public function sendShortMessage(ShortMessage $shortMessage);
}
