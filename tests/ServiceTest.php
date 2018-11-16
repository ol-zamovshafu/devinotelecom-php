<?php

namespace Zamovshafu\Devinotelecom\Test;

use Mockery as M;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Zamovshafu\Devinotelecom\ShortMessage;
use Zamovshafu\Devinotelecom\Service;
use Zamovshafu\Devinotelecom\ShortMessageFactory;
use Zamovshafu\Devinotelecom\ShortMessageCollection;
use Zamovshafu\Devinotelecom\ShortMessageCollectionFactory;
use Zamovshafu\Devinotelecom\Http\Clients\ClientInterface;
use Zamovshafu\Devinotelecom\Http\Responses\ResponseInterface;

class ServiceTest extends TestCase
{
    public static $functions;

    /**
     * @var Service
     */
    private $service;

    /**
     * @var ResponseInterface|MockInterface
     */
    private $response;

    /**
     * @var ShortMessage|MockInterface
     */
    private $shortMessage;

    /**
     * @var ShortMessage|MockInterface
     */
    private $shortMessage2;

    /**
     * @var ClientInterface|MockInterface
     */
    private $client;

    /**
     * @var ShortMessageFactory|MockInterface
     */
    private $shortMessageFactory;

    public function setUp()
    {
        parent::setUp();

        self::$functions = M::mock();
        $this->shortMessage = M::mock(ShortMessage::class);
        $this->shortMessage2 = M::mock(ShortMessage::class);
        $this->response = M::mock(ResponseInterface::class);
        $this->client = M::mock(ClientInterface::class);
        $this->shortMessageFactory = M::mock(ShortMessageFactory::class);

        $this->service = new Service(
            $this->client,
            $this->shortMessageFactory
        );
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    public function testItSendsOneShortMessageToOneRecipient()
    {
        $this->shortMessageFactory->shouldReceive('create')
            ->once()
            ->with('recipient', 'message')
            ->andReturn($this->shortMessage);

        $this->client->shouldReceive('sendShortMessage')
            ->once()
            ->with($this->shortMessage)
            ->andReturn($this->response);

        $response = $this->service->sendShortMessage('recipient', 'message');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testItSendsOneShortMessageToMultipleRecipients()
    {
        $this->shortMessageFactory->shouldReceive('create')
            ->once()
            ->with(['recipient1', 'recipient2'], 'message')
            ->andReturn($this->shortMessage);

        $this->client->shouldReceive('sendShortMessage')
            ->once()
            ->with($this->shortMessage)
            ->andReturn($this->response);

        $response = $this->service->sendShortMessage(['recipient1', 'recipient2'], 'message');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testItCallsTheBeforeAndAfterCallbacks()
    {
        $beforeSingleCallback = function ($message) {
            ServiceTest::$functions->beforeSingle($message);
        };

        $afterSingleCallback = function ($response, $message) {
            ServiceTest::$functions->afterSingle($response, $message);
        };

        $beforeMultipleCallback = function ($collection) {
            ServiceTest::$functions->beforeMultiple($collection);
        };

        $afterMultipleCallback = function ($response, $collection) {
            ServiceTest::$functions->afterMultiple($response, $collection);
        };

        $service = new Service(
            $this->client,
            $this->shortMessageFactory,
            $beforeSingleCallback,
            $afterSingleCallback,
            $beforeMultipleCallback,
            $afterMultipleCallback
        );

        $this->shortMessageFactory->shouldReceive('create')
            ->once()
            ->with('recipient', 'message')
            ->andReturn($this->shortMessage);

        $this->client->shouldReceive('sendShortMessage')
            ->once()
            ->with($this->shortMessage)
            ->andReturn($this->response);

        ServiceTest::$functions->shouldReceive('beforeSingle')->with($this->shortMessage)->once();
        ServiceTest::$functions->shouldReceive('afterSingle')->with($this->response, $this->shortMessage)->once();

        $response = $service->sendShortMessage('recipient', 'message');
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
