<?php

namespace Zamovshafu\Devinotelecom\Test;

use Mockery as M;
use PHPUnit\Framework\TestCase;
use Zamovshafu\Devinotelecom\ShortMessage;
use Zamovshafu\Devinotelecom\ShortMessageFactory;

class ShortMessageFactoryTest extends TestCase
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

    public function testItCreatesNewShortMessages()
    {
        $shortMessageFactory = new ShortMessageFactory();

        $shortMessage = $shortMessageFactory->create('receiver', 'message');

        $this->assertInstanceOf(ShortMessage::class, $shortMessage);
        $this->assertEquals('message', $shortMessage->body());
        $this->assertEquals('receiver', $shortMessage->receiversString());
    }
}
