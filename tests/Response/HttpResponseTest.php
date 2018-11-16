<?php

namespace Zamovshafu\Devinotelecom\Test;

use PHPUnit\Framework\TestCase;
use Zamovshafu\Devinotelecom\Http\Responses\HttpResponse;

class HttpResponseTest extends TestCase
{
    public function testItReturnsTrueIfTheResponseIsSuccessful()
    {
        $httpApiResponse = new HttpResponse("[151103141334228]");

        $this->assertTrue($httpApiResponse->isSuccessful());
    }

    public function testItReturnsNullErrorMessageIfTheResponseIsSuccessful()
    {
        $httpApiResponse = new HttpResponse("[151103141334228]");

        $this->assertNull($httpApiResponse->message());
    }

    public function testItReturnsTheStatusCodeOfTheResponse()
    {
        $httpApiResponse = new HttpResponse("[151103141334228]");

        $this->assertEquals('0', $httpApiResponse->statusCode());
    }

    public function testItReturnsTheStatusMessageOfTheResponse()
    {
        $httpApiResponse = new HttpResponse('{"Code":2, "Desc": "Error description"}');

        $this->assertEquals('Invalid argument', $httpApiResponse->status());
    }

    public function testItReturnsEmptyArrayIfNoMessageReportIdentifiersReturned()
    {
        $httpApiResponse = new HttpResponse('{"Code":2, "Desc": "Error description"}');

        $this->assertEquals([], $httpApiResponse->messageReportIdentifiers());
    }

    public function testItReturnsMessageReportIdentifiers()
    {
        $httpApiResponse = new HttpResponse("[151103141334228,151103141334229]");

        $this->assertEquals([
            '151103141334228',
            '151103141334229',
        ], $httpApiResponse->messageReportIdentifiers());
    }

    public function testItShoutsOutIfTheXmlApiGroupIdRequested()
    {
        $httpApiResponse = new HttpResponse("[151103141334228,151103141334229]");

        $e = null;
        try {
            $httpApiResponse->groupId();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(\BadMethodCallException::class, $e);
    }
}
