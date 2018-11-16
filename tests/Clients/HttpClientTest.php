<?php

namespace Zamovshafu\Devinotelecom\Test\Clients;

use Mockery as M;
use GuzzleHttp\Client;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Zamovshafu\Devinotelecom\ShortMessage;
use Psr\Http\Message\ResponseInterface;
use Zamovshafu\Devinotelecom\Http\Clients\HttpClient;
use Zamovshafu\Devinotelecom\Http\Responses\ResponseInterface as DevinotelecomResponseInterface;

class HttpClientTest extends TestCase
{
    /**
     * @var Client|MockInterface
     */
    private $httpClient;

    /**
     * @var ShortMessage|MockInterface
     */
    private $shortMessage;

    /**
     * @var Client|ResponseInterface
     */
    private $httpResponse;

    public function setUp()
    {
        parent::setUp();

        $this->httpClient = M::mock(Client::class);
        $this->shortMessage = M::mock(ShortMessage::class);
        $this->httpResponse = M::mock(ResponseInterface::class);
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    public function testItSendsSingleShortMessage()
    {
        $this->httpResponse->shouldReceive('getBody')->once()
            ->andReturn('"SESSION_ID"');

        $this->httpClient->shouldReceive('request')->with(
            'GET',
            'foo' . 'user/sessionid',
            ['query' => [
                'login' => 'bar',
                'password' => 'baz',
            ]]
        )->andReturn($this->httpResponse);

        $client = new HttpClient(
            $this->httpClient,
            'foo',
            'bar',
            'baz',
            'qux'
        );

        $this->shortMessage->shouldReceive('toArray')->once()->andReturn([
            'foo' => 'bar',
        ]);

        $this->httpResponse->shouldReceive('getBody')->once()
            ->andReturn("[151103141334228]");

        $this->httpClient->shouldReceive('request')->with(
            'POST',
            'foo' . 'sms/sendbulk',
            [
                'form_params' => [
                    'foo' => 'bar',
                    'SourceAddress' => 'qux',
                    'SessionId' => 'SESSION_ID',
                ],
            ]
        )->andReturn($this->httpResponse);

        $response = $client->sendShortMessage($this->shortMessage);

        $this->assertInstanceOf(DevinotelecomResponseInterface::class, $response);
        $this->assertTrue($response->isSuccessful());
    }
}
