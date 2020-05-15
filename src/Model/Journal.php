<?php

namespace XTAIN\DeutschePostPortokasse\Model;

use Traversable;

class Journal implements \IteratorAggregate
{
    /**
     * @var JournalEntry[]
     */
    protected $entries;

    /**
     * @var \DateTimeImmutable
     */
    protected $startDate;

    /**
     * @var \DateTimeImmutable
     */
    protected $endDate;

    /**
     * @var int
     */
    protected $oldBalance;

    /**
     * @var int
     */
    protected $newBalance;

    /**
     * Journal constructor.
     * @param JournalEntry[] $entries
     * @param \DateTimeImmutable $startDate
     * @param \DateTimeImmutable $endDate
     * @param int $oldBalance
     * @param int $newBalance
     */
    public function __construct(
        array $entries,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
        int $oldBalance,
        int $newBalance
    ) {
        $this->entries = $entries;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->oldBalance = $oldBalance;
        $this->newBalance = $newBalance;
    }

    public function getIterator()
    {
        yield from $this->getEntries();
    }

    /**
     * @return JournalEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * @return int
     */
    public function getOldBalance(): int
    {
        return $this->oldBalance;
    }

    /**
     * @return int
     */
    public function getNewBalance(): int
    {
        return $this->newBalance;
    }
}
