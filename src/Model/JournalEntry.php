<?php

namespace XTAIN\DeutschePostPortokasse\Model;

class JournalEntry implements \JsonSerializable
{
    const TYPE_TRANSFER = 'TRANSFER';
    const TYPE_PAYMENT = 'PAYMENT';

    const STATE_PREPARED = 'PREPARED';
    const STATE_EXECUTED = 'EXECUTED';

    /**
     * @var string
     */
    protected $accountingText;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var string
     */
    protected $channel;

    /**
     * @var \DateTimeImmutable
     */
    protected $date;

    /**
     * @var string
     */
    protected $shopOrderId;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $type;

    /**
     * JournalEntry constructor.
     * @param string $accountingText
     * @param int $amount
     * @param string $channel
     * @param \DateTimeImmutable $date
     * @param string $shopOrderId
     * @param string $state
     * @param string $type
     */
    public function __construct(
        string $accountingText,
        int $amount,
        string $channel,
        \DateTimeImmutable $date,
        string $shopOrderId,
        string $state,
        string $type
    ) {
        $this->accountingText = $accountingText;
        $this->amount = $amount;
        $this->channel = $channel;
        $this->date = $date;
        $this->shopOrderId = $shopOrderId;
        $this->state = $state;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getAccountingText(): string
    {
        return $this->accountingText;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getShopOrderId(): string
    {
        return $this->shopOrderId;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
