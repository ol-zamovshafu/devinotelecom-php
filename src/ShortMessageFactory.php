<?php

namespace Zamovshafu\Devinotelecom;

/**
 * Class ShortMessageFactory.
 */
class ShortMessageFactory implements ShortMessageFactoryInterface
{
    public function create($receivers, $body)
    {
        return new ShortMessage($receivers, $body);
    }
}
