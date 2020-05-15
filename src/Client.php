<?php

namespace XTAIN\DeutschePostPortokasse;

use XTAIN\DeutschePostPortokasse\Exception\InvalidPaymentAmountException;
use XTAIN\DeutschePostPortokasse\Exception\InvalidResponseException;
use XTAIN\DeutschePostPortokasse\Exception\LoginFailedException;
use XTAIN\DeutschePostPortokasse\Exception\PaymentFailedException;

class Client
{
    const PAYMENT_METHOD_DIRECTDEBIT = 'DIRECTDEBIT';

    /**
     * @var HttpBrowser
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var bool
     */
    protected $loggedIn = false;

    public function __construct($username, $password, $server = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->httpClient = new HttpBrowser($server);
    }

    /**
     * @throws InvalidResponseException
     * @throws LoginFailedException
     */
    protected function login($username, $password) : void
    {
        $response = $this->httpClient->post('/login', [
            'username' => $username,
            'password' => $password
        ], 'form');

        if (!($response instanceof HttpJsonResponse)) {
            throw new InvalidResponseException('no json response');
        }

        if ($response->getStatusCode() != 200) {
            $data = $response->getData();
            throw new LoginFailedException(isset($data['reason']) ? $data['reason'] : '');
        }

        $this->loggedIn = true;
    }

    /**
     * @throws InvalidResponseException
     * @throws LoginFailedException
     */
    public function getBalance() : int
    {
        if (!$this->loggedIn) {
            $this->login($this->username, $this->password);
        }

        $response = $this->httpClient->get('/api/v1/wallet-overviews/me');

        if (!($response instanceof HttpJsonResponse) || $response->getStatusCode() !== 200) {
            throw new InvalidResponseException();
        }

        $data = $response->getData();

        if (!isset($data['balance']) || !is_int($data['balance'])) {
            throw new InvalidResponseException();
        }

        return $data['balance'];
    }

    public function getJournal(\DateTimeInterface $start = null, \DateTimeInterface $end = null) : Journal
    {
        if ($end === null) {
            $end = new \DateTimeImmutable();
        }

        if ($start === null) {
            $start = $end->sub(new \DateInterval('P365D'));
        }

        $query = [
            'offset' => 0,
            'rows' => 10,
            'selectionDays' => 10,
            'selectionEnd' => $end->format('d.m.Y'),
            'selectionStart' => $start->format('d.m.Y'),
            'selectionType' => 'RANGE'
        ];

        return new Journal($this, $query);
    }

    /**
     * @throws InvalidResponseException
     * @throws LoginFailedException
     * @internal
     */
    public function journal(array $query)
    {
        if (!$this->loggedIn) {
            $this->login($this->username, $this->password);
        }

        return $this->httpClient->get('/api/v1/journals?' . http_build_query($query));
    }

    /**
     * @throws LoginFailedException
     * @throws InvalidResponseException
     * @throws PaymentFailedException
     */
    public function loadMoneyDirectDebit(int $amount) : void
    {
        $this->loadMoney($amount, self::PAYMENT_METHOD_DIRECTDEBIT);
    }

    private function loadMoney(int $amount, string $paymentMethod = self::PAYMENT_METHOD_DIRECTDEBIT) : void
    {
        if (!$this->loggedIn) {
            $this->login($this->username, $this->password);
        }

        $response = $this->httpClient->post('/api/v1/payments', [
            'amount' => $amount,
            'bic' => null,
            'paymentMethod' => $paymentMethod
        ]);

        if (!($response instanceof HttpJsonResponse)) {
            throw new InvalidResponseException('no json response');
        }

        $data = $response->getData();

        if (!isset($data['code'])) {
            throw new PaymentFailedException();
        }

        if ($response->getStatusCode() != 200 || $data['code'] !== 'OK') {
            if ($data['code'] === 'InvalidPaymentAmount') {
                throw new InvalidPaymentAmountException();
            }
            throw new PaymentFailedException();
        }
    }
}
