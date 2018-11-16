<?php

namespace Zamovshafu\Devinotelecom\Test;

use Mockery as M;
use PHPUnit\Framework\TestCase;
use Zamovshafu\Devinotelecom\ShortMessage;

class ShortMessageTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    public function testItConstructsWithSingleRecipient()
    {
        $shortMessage = new ShortMessage('recipient', 'message');

        $this->assertEquals(['recipient'], $shortMessage->receivers());
        $this->assertEquals('recipient', $shortMessage->receiversString('|'));
        $this->assertEquals('message', $shortMessage->body());
    }

    public function testItConstructsWithMultipleRecipients()
    {
        $shortMessage = new ShortMessage(['recipient1', 'recipient2'], 'message');

        $this->assertTrue($shortMessage->hasManyReceivers());
        $this->assertEquals(['recipient1', 'recipient2'], $shortMessage->receivers());
        $this->assertEquals('recipient1|recipient2', $shortMessage->receiversString('|'));
        $this->assertEquals('message', $shortMessage->body());
    }

    public function testItCanBeCastedToArray()
    {
        $shortMessage = new ShortMessage(['recipient1', 'recipient2'], 'message');

        $this->assertEquals([
            'DestinationAddresses' => ['recipient1', 'recipient2'],
            'Data' => 'message',
        ], $shortMessage->toArray());
    }
}
