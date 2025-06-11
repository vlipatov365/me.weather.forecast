<?php

namespace Me\Weather\Trait;

trait CacheTrait
{
    protected const CACHE_DIR = '/weather';
    protected const SHORT_TTL = 60;
    protected const LONG_TTL = 1800;
    protected const HOUR_TTL = 3600;
    protected const DAY_TTL = 86400;
    protected const MONTH_TTL = 2592000;

    /**
     * @return int
     */
    protected function getEndOfTheDayTTL(): int
    {
        return strtotime("today 23:59:59") - time();
    }

    /**
     * @return int
     */
    protected function getEndOfTheWeekTTL(): int
    {
        return strtotime("next Sunday 23:59:59") - time();
    }

    /**
     * @return int
     */
    protected function getEndOfTheYearTTL(): int
    {
        return strtotime("31 December 23:59:59") - time();
    }
}