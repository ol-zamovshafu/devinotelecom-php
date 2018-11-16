<?php

namespace Zamovshafu\Devinotelecom\Http\Clients;

use GuzzleHttp\Client;
use Zamovshafu\Devinotelecom\Http\Responses\HttpResponse;
use Zamovshafu\Devinotelecom\Http\Responses\ResponseInterface;
use Zamovshafu\Devinotelecom\ShortMessage;

/**
 * Class HttpClient.
 */
class HttpClient implements ClientInterface
{
    /**
     * The Http client.
     *
     * @var Client
     */
    private $httpClient;

    /**
     * The xml request url.
     *
     * @var string
     */
    private $url;

    /**
     * The auth login.
     *
     * @var string
     */
    private $login;

    /**
     * The auth password.
     *
     * @var string
     */
    private $password;

    /**
     * The outbox name.
     *
     * @var string
     */
    private $outboxName;

    /**
     * The session ID for API
     *
     * @var string
     */
    private $sessionId;

    /**
     * HttpClient constructor.
     *
     * @param Client $client
     * @param string $url
     * @param string $login
     * @param string $password
     * @param string $outboxName
     */
    public function __construct(Client $client, $url, $login, $password, $outboxName)
    {
        $this->httpClient = $client;
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
        $this->outboxName = $outboxName;

        $this->sessionId = $this->getSessionId();
    }

    /**
     * Get SessioID for using the Devinotelecom services.
     *
     * @param  ShortMessage $shortMessage
     *
     * @return string
     */
    public function getSessionId()
    {
        if ($this->sessionId) {
            return $this->sessionId;
        }

        try {
            $guzzleResponse = $this->httpClient->request(
                'GET',
                $this->url . 'user/sessionid',
                ['query' => $this->getCredentials()]
            );
        } catch (Exception $e) {
            return;
        }

        return \json_decode((string) $guzzleResponse->getBody());
    }

    /**
     * Send a short message using the Devinotelecom services.
     *
     * @param  ShortMessage $shortMessage
     *
     * @return ResponseInterface
     */
    public function sendShortMessage(ShortMessage $shortMessage)
    {
        try {
            $guzzleResponse = $this->httpClient->request(
                'POST',
                $this->url . 'sms/sendbulk',
                [
                    'form_params' => array_merge(
                        $shortMessage->toArray(),
                        $this->getSendDate()
                    ),
                ]
            );
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return new HttpResponse((string) $e->getResponse()->getBody(true));
        }

        return new HttpResponse((string) $guzzleResponse->getBody());
    }

    /**
     * Get a message status using the Devinotelecom services.
     *
     * @param  ResponseInterface|array $messages
     *
     * @return ResponseInterface
     */
    public function getMessagesStatus($messages)
    {
        if ($messages instanceof ResponseInterface) {
            $messages = $messages->messageReportIdentifiers();
        }

        $result = [];
        foreach ($messages as $messageId) {
            $guzzleResponse = $this->httpClient->request(
                'GET',
                $this->url . 'sms/state',
                [
                    'query' => array_merge(
                        $this->getSendDate(),
                        ['messageId' => $messageId]
                    ),
                ]
            );

            $result[$messageId] = new HttpResponse((string) $guzzleResponse->getBody());
        }

        return $result;
    }

    /**
     * Get the send date of the contents.
     *
     * @return array
     */
    private function getSendDate()
    {
        return [
            'SourceAddress' => $this->outboxName,
            'SessionId' => $this->sessionId,
        ];
    }

    /**
     * Get the auth credentials array.
     *
     * @return array
     */
    private function getCredentials()
    {
        return [
            'login' => $this->login,
            'password' => $this->password,
        ];
    }
}
