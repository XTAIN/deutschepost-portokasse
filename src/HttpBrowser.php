<?php

namespace XTAIN\DeutschePostPortokasse;

class HttpBrowser
{
    /**
     * @var string
     */
    protected $server = 'https://portokasse.deutschepost.de/portokasse';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * @var \GuzzleHttp\Cookie\CookieJar
     */
    protected $jar;

    public function __construct($server = null)
    {
        if ($server !== null) {
            $this->server = $server;
        }

        $this->jar = new \GuzzleHttp\Cookie\CookieJar();
        $this->guzzle = new \GuzzleHttp\Client([
            'timeout'  => 30.0,
            'http_errors' => false,
            'cookies' => $this->jar,
            //'debug' => true
        ]);
    }

    protected function request($method, $url, array $parameters = []) : HttpResponse
    {
        $response = $this->guzzle->request($method, $url, $parameters);

        $content = $response->getBody()->getContents();

        if (substr($content, 0, 1) == '{') {
            return new HttpJsonResponse($content, $response->getStatusCode(), $response->getHeaders());
        }

        return new HttpResponse($content, $response->getStatusCode(), $response->getHeaders());
    }

    public function get($url) : HttpResponse
    {
        $parameters = [
            'headers' => [
                'X-CSRF-TOKEN' => $this->getCSRF()
            ]
        ];
        return $this->request('GET', $this->server . $url, $parameters);
    }

    public function post($url, array $data, string $type = 'json') : HttpResponse
    {
        $type = $type === 'json' ? 'json' : 'form_params';

        $parameters = [
            'headers' => [
                'X-CSRF-TOKEN' => $this->getCSRF()
            ],
            $type => $data
        ];

        return $this->request('POST', $this->server . $url, $parameters);
    }

    protected function getCookieValue(string $name) : ?string
    {
        $cookie = $this->jar->getCookieByName($name);

        if (isset($cookie)) {
            return $cookie->getValue();
        }

        return null;
    }

    protected function getCSRF() : ?string
    {
        $csrf = $this->getCookieValue('CSRF-TOKEN');
        if ($csrf === null) {
            $this->request('GET', $this->server . '/');
        }
        return $this->getCookieValue('CSRF-TOKEN');
    }
}
