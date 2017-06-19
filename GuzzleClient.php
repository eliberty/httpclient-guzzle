<?php

namespace Payment\HttpClient;

use GuzzleHttp\Client;

/**
 * Class GuzzleClient
 * @package Payment\HttpClient
 */
class GuzzleClient implements HttpClientInterface
{
    /**
     * @var Client
     */
    protected $client;

    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if (null === $this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param null $content
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     * @throws HttpException
     */
    public function request($method, $url, $content = null, array $headers = array(), array $options = array())
    {
        try {
            $opts = [];
            if (null !== $content) {
                $opts['body'] = $content;
            }

            if (count($headers)) {
                $opts['headers'] = $headers;
            }

            $optsRequest = array_merge($opts, $options);

            $originalResponse = $this->getClient()->request($method, $url, $optsRequest);

            return new NullResponse(
                $originalResponse->getStatusCode(),
                $originalResponse->getHeader('Content-Type'),
                (string) $originalResponse->getBody(),
                $originalResponse->getHeaders()
            );
        } catch(\Exception $e) {
            throw new HttpException($e);
        }
    }
}
