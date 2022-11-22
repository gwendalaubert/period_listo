<?php

namespace App\Entity;

use App\Exceptions\PeriodException;

class Period
{
    const LEAVE_TYPE        = "leave";
    const MEDICAL_TYPE      = "medical";

    const PERIOD_START      = "start";
    const PERIOD_END        = "end";

    /**
     * @var string
     */
    private string $type;

    /**
     * @var \DateTime start
     */
    private \DateTime $start;

    /**
     * @var \DateTime end
     */
    private \DateTime $end;

    /**
     * @param string $type
     * @param \DateTime $start
     * @param \DateTime $end
     * @throws PeriodException
     */
    public function __construct(string $type, \DateTime $start, \DateTime $end)
    {
        $this->type = $type;

        if ($start > $end) {
            throw new PeriodException("Start should always be lower than end");
        }

        $this->start = $this->handleHalfDay(self::PERIOD_START, $start);
        $this->end = $this->handleHalfDay(self::PERIOD_END, $end);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        $this->start = clone $this->start;
        $this->end = clone $this->end;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start): void
    {
        $this->start = $start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end): void
    {
        $this->end = $end;
    }

    /**
     * @param string $type
     * @param \DateTime $date
     * @return \DateTime
     * @throws PeriodException
     */
    private function handleHalfDay(string $type, \DateTime $date): \DateTime
    {
        $cloneDate = clone $date;
        if ($type === self::PERIOD_START && $date < $cloneDate->setTime(12, 0, 0)) {
            return $date->setTime(0, 0, 0);
        } elseif (($type === self::PERIOD_START && $date >= $cloneDate->setTime(12, 0, 0))
        || ($type === self::PERIOD_END && $date <= $cloneDate->setTime(12, 0, 0))) {
            return $date->setTime(12, 0, 0);
        } elseif ($type === self::PERIOD_END && $date > $cloneDate->setTime(12, 0, 0)) {
            return $date->setTime(23, 59, 59);
        } else {
            // Should not been thrown
            throw new PeriodException('Given ' . $type . ' date is offset');
        }
    }
}