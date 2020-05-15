<?php

namespace XTAIN\DeutschePostPortokasse;

use XTAIN\DeutschePostPortokasse\Exception\InvalidResponseException;
use XTAIN\DeutschePostPortokasse\Model\JournalEntry;

class Journal implements \IteratorAggregate
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     * @var array
     */
    protected $query;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var \XTAIN\DeutschePostPortokasse\Model\Journal
     */
    protected $model;

    public function __construct(Client $client, $query)
    {
        $this->client = $client;
        $this->query = $query;
    }

    protected function load()
    {
        $newHash = \json_encode($this->query);
        if ($newHash !== $this->hash) {
            $this->hash = $newHash;
            $response = $this->client->journal($this->query);
            if ($response->getStatusCode() !== 200) {
                throw new InvalidResponseException();
            }

            if (!($response instanceof HttpJsonResponse)) {
                throw new InvalidResponseException();
            }

            try {
                $this->convertData($response->getData());
            } catch (\Throwable $e) {
                throw new \InvalidArgumentException('cannot convert data', 0, $e);
            }
        }
    }

    protected function convertData(array $data)
    {
        $entries = [];
        $startDate = \DateTimeImmutable::createFromFormat('d.m.Y', $data['startDate']);
        $endDate = \DateTimeImmutable::createFromFormat('d.m.Y', $data['endDate']);
        $newBalance = $data['newBalance'];
        $oldBalance = $data['oldBalance'];

        foreach ($data['journalEntries'] as $entry) {
            $accountingText = $entry['accountingText'];
            $amount = $entry['amount'];
            $channel = $entry['channel'];
            $date = new \DateTimeImmutable('@' . round($entry['date'] / 1000));
            $shopOrderId = $entry['shopOrderId'];
            $state = $entry['state'];
            $type = $entry['type'];
            $entries[] = new JournalEntry(
                $accountingText,
                $amount,
                $channel,
                $date,
                $shopOrderId,
                $state,
                $type
            );
        }

        $this->model = new \XTAIN\DeutschePostPortokasse\Model\Journal(
            $entries,
            $startDate,
            $endDate,
            $oldBalance,
            $newBalance
        );
    }

    public function getJournal() : \XTAIN\DeutschePostPortokasse\Model\Journal
    {
        $this->load();
        return $this->model;
    }

    public function getIterator() : \Generator
    {
        /** @var JournalEntry $firstEntry */
        $firstEntry = null;
        do {
            $this->load();

            if (empty($this->model->getEntries())) {
                break;
            }

            $entries = $this->model->getEntries();

            if ($firstEntry !== null && \json_encode($firstEntry) == \json_encode($entries[0])) {
                break;
            }

            $firstEntry = $entries[0];
            yield $this->model;

            $this->query['offset'] += $this->query['rows'];
        } while(true);

        yield from [];
    }
}
