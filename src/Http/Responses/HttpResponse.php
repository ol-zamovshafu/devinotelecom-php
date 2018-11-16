<?php

namespace Zamovshafu\Devinotelecom\Http\Responses;

use BadMethodCallException;

/**
 * Class HttpResponse.
 */
final class HttpResponse implements ResponseInterface
{
    /**
     * The read response of SMS message request..
     *
     * @var array
     */
    private $responseAttributes = [];

    /**
     * The Devinotelecom codes.
     * https://docs.devinotele.com/httpapi.html#sms-viber
     *
     * @var array
     */
    private static $statuses = [
        '0' => 'Operation complete',
        '1' => 'Argument cannot be null or empty',
        '2' => 'Invalid argument',
        '3' => 'Invalid session id',
        '4' => 'Unauthorized access',
        '5' => 'Not enough credits',
        '6' => 'Invalid operation',
        '7' => 'Forbidden',
        '8' => 'Gateway error',
        '9' => 'Internal server error',
        '-1' => 'Отправлено (передано в мобильную сеть)',
        '-2' => 'В очереди',
        '47' => 'Удалено',
        '-98' => 'Остановлено',
        '0' => 'Доставлено абоненту',
        '10' => 'Неверно введен адрес отправителя',
        '11' => 'Неверно введен адрес получателя',
        '41' => 'Недопустимый адрес получателя',
        '42' => 'Отклонено смс-центром',
        '46' => 'Просрочено (истек срок жизни сообщения)',
        '48' => 'Отклонено Платформой',
        '69' => 'Отклонено',
        '99' => 'Неизвестный',
        '255' => 'статус: *сообщение еще не успело попасть в БД, *сообщение старше 48 часов.',
    ];

    /**
     * The message (Desc) of the response
     *
     * @var string
     */
    private $message;

    /**
     * Create a message response.
     *
     * @param  string $responseBody
     */
    public function __construct($responseBody)
    {
        $this->responseAttributes = $this->readResponseBody($responseBody);
    }

    /**
     * Determine if the operation was successful or not.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return '0' === $this->statusCode();
    }

    /**
     * Get the status code.
     *
     * @return string
     */
    public function statusCode()
    {
        return (string) $this->responseAttributes['statusCode'];
    }

    /**
     * Get the string representation of the status.
     *
     * @return string
     */
    public function status()
    {
        return array_key_exists($this->statusCode(), self::$statuses)
        ? self::$statuses[$this->statusCode()]
        : 'Unknown';
    }

    /**
     * Get the message of the response.
     *
     * @return null|string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Get the group identifier from the response.
     */
    public function groupId()
    {
        throw new BadMethodCallException(
            "Devinotelecom Http API responses do not group bulk message identifiers. "
                . "Use messageReportIdentifiers instead."
        );
    }

    /**
     * Get the message report identifiers for the messages sent.
     * Message report id returns -1 if invalid Msisdns, -2 if invalid message text.
     *
     * @return array
     */
    public function messageReportIdentifiers()
    {
        if (array_key_exists('messageids', $this->responseAttributes)) {
            return $this->responseAttributes['messageids'];
        }

        return [];
    }

    /**
     * Read the message response body string.
     *
     * @param $responseBody
     * @return array
     */
    private function readResponseBody($responseBody)
    {
        if (is_string($responseBody)) {
            $responseBody = \json_decode($responseBody, true);
        }

        $result = [];

        if (isset($responseBody['Code'])) {
            $status = (int) $responseBody['Code'];
            $this->message = $responseBody['Desc'] ?? null;
        } elseif (isset($responseBody['State'])) {
            $status = (int) $responseBody['State'];
            $this->message = $responseBody['StateDescription'] ?? null;
        } else {
            $status = 0;
            $result['messageids'] = $responseBody;
        }

        $result['success'] = !$status ? true : false;
        $result['statusCode'] = $status;

        return $result;
    }
}
