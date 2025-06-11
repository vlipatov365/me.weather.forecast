<?php

namespace Me\Weather\Service\Weather;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Me\Weather\Helper\MeasurementConverter;

/**
 * class OpenWeatherMap
 *
 * @author  Vyacheslav Lipatov
 * @package Me\Weather\Service\Weather
 */
class OpenWeatherMap extends WeatherBase
{
    private const SERVICE_NAME = 'OpenWeatherMap';
    private const UNITS = [
        'C' => 'metric',
        'F' => 'imperial',
    ];
    private const CURRENT_WEATHER = 1;
    private const TOMORROW_WEATHER = 2;
    private const LAST_10_DAYS_WEATHER = 3;
    private const LAST_YEAR_WEATHER_BY_WEEK = 4;

    /**
     * @throws SystemException
     */
    public function getCurrentWeather(): array
    {
        $this->cacheInstance = Cache::createInstance();
        if (
            $this->cacheInstance->initCache(
                self::HOUR_TTL,
                $this->getCacheKey(self::CURRENT_WEATHER),
                self::CACHE_DIR
            )
        ) {
            return $this->cacheInstance->getVars();
        }

        $responseData = $this->fetch(
            sprintf(
                'https://api.openweathermap.org/data/2.5/weather?q=%s&appid=%s&units=%s&lang=%s',
                $this->city,
                $this->apiKey,
                self::UNITS[$this->units],
                self::LANGUAGE
            )
        );

        if ($responseData['cod'] !== self::SUCCESS) {
            $this->constructException($responseData);
        }

        $adaptateData = $this->adaptateResponse($responseData);
        $this->writeCache($adaptateData);

        return $adaptateData;
    }

    /**
     * @throws SystemException
     */
    public function getTomorrowWeather(): array
    {
        $this->cacheInstance = Cache::createInstance();
        if (
            $this->cacheInstance->initCache(
                self::HOUR_TTL,
                $this->getCacheKey(self::TOMORROW_WEATHER),
                self::CACHE_DIR
            )
        ) {
            return $this->cacheInstance->getVars();
        }

        $responseData = $this->fetch(
            sprintf(
                'https://pro.openweathermap.org/data/2.5/forecast/daily?q=%s&appid=%s&units=%s&lang=%s&cnt=1',
                $this->city,
                $this->apiKey,
                self::UNITS[$this->units],
                self::LANGUAGE
            )
        );

        if (is_array($responseData) && $responseData['cod'] === self::SUCCESS) {
            $this->cacheInstance->startDataCache();
            $responseData = $this->adaptateResponse($responseData);
            $this->cacheInstance->endDataCache(
                $responseData
            );

            return $responseData;
        }

        $this->constructException($responseData);
    }

    /**
     * @return array
     * @throws SystemException
     */
    public function getLast10Days(): array
    {
        if ($this->cacheInstance->initCache(
            $this->getEndOfTheDayTTL(),
            $this->getCacheKey(self::LAST_10_DAYS_WEATHER),
            self::CACHE_DIR
        )) {
            return $this->cacheInstance->getVars();
        }

        $currentYear['result'] = $this->getYearHistory((int) date('Y'));

        if ($this->isFirstTo10YearDays()) {
            $lastYear['result'] = $this->getYearHistory((int) date('Y') - 1);
        }

        $weatherHistory = $this->getDailyFromYear($currentYear['result'], 10, (int) date('Y'));

        if (count($weatherHistory) < 10 && isset($lastYear)) {
            $weatherHistory = array_merge(
                $weatherHistory,
                $this->getDailyFromYear(
                    $lastYear['result'],
                    10 - count($weatherHistory),
                    (int) date('Y') - 1
                )
            );
        }

        $this->writeCache($weatherHistory);

        return $weatherHistory;
    }

    /**
     * @return array
     * @throws SystemException
     */
    public function getLastYearWeekly(): array
    {
        if (
            $this->cacheInstance->initCache(
                $this->getEndOfTheYearTTL(),
                $this->getCacheKey(self::LAST_YEAR_WEATHER_BY_WEEK),
                self::CACHE_DIR
            )
        ) {
            return $this->cacheInstance->getVars();
        }

        $lastYear = (int) date('Y') - 1;
        $lastYearWeekly = $this->getWeeklyHistory(
            $this->getYearHistory($lastYear),
            $lastYear
        );

        $this->writeCache($lastYearWeekly);

        return $lastYearWeekly;
    }

    /**
     * @return string
     */
    protected function getServiceName(): string
    {
        return self::SERVICE_NAME;
    }

    /**
     * @param array $responseData
     *
     * @return array
     */
    private function adaptateResponse(array $responseData): array
    {
        return [
            'TEMP' => $responseData['main']['temp'],
            'NIGHT_TEMP' => $responseData['main']['temp_min'],
            'HUMIDITY' => $responseData['main']['humidity'],
            'PRESSURE' => MeasurementConverter::fromHPatoMmHg($responseData['main']['pressure']),
        ];
    }

    /**
     * @param array $responseData
     * @param string $additionalMessage
     *
     * @return void
     * @throws SystemException
     */
    private function constructException(array $responseData, string $additionalMessage = ''): void
    {
        throw new SystemException(
            (!empty($additionalMessage))
                ? $additionalMessage
                : Loc::getMessage(
                'WEATHER_SERVICE_NO_MESSAGE_ERROR',
                [
                    '#MESSAGE#' => $responseData['message'],
                ]
            ),
            $responseData['code']
        );
    }

    /**
     * @return bool
     */
    private function isFirstTo10YearDays(): bool
    {
        return ((int) date('m') === 1 && (int) date('d') < 10);
    }

    /**
     * @param int $year
     *
     * @return array
     * @throws SystemException
     */
    private function getYearHistory(int $year): array
    {
        if (
            $this->cacheInstance->initCache(
                self::MONTH_TTL,
                $this->getCacheKey($year),
            )
        ) {
            return $this->cacheInstance->getVars();
        }

        $responseData = $this->fetch(
            sprintf(
                'https://history.openweathermap.org/data/2.5/aggregated/year?q=%s&appid=%s',
                $this->city,
                $year
            )
        );

        if ((int) $responseData['cod'] !== self::SUCCESS) {
            $this->constructException(
                $responseData,
                Loc::getMessage(
                    'WEATHER_SERVICE_ON_YEAR_RESPONSE_ERROR',
                    [
                        '#MESSAGE#' => $responseData['message'],
                    ]
                )
            );
        }

        $this->writeCache($responseData);

        return $responseData;
    }

    /**
     * @param array $yearData
     * @param int $days
     * @param int $year
     *
     * @return array
     */
    private function getDailyFromYear(array $yearData, int $days, int $year): array
    {
        $result = [];
        $days = array_reverse(
            array_slice($yearData, -$days, $days)
        );
        foreach ($days as $day) {
            $result[] = [
                'DATE' => DateTime::createFromTimestamp(mktime(0, 0, 0, $day['month'], $day['day'], $year)),
                'TEMP' => MeasurementConverter::fromKelvinTo($this->units, $day['temp']['median']),
                'NIGHT_TEMP' => MeasurementConverter::fromKelvinTo($this->units, $day['temp']['record_min']),
                'HUMIDITY' => $day['humidity']['median'],
                'PRESSURE' => MeasurementConverter::fromHPatoMmHg($day['pressure']['median']),
            ];
        }

        return $result;
    }

    /**
     * @param array $yearHistory
     * @param int $year
     *
     * @return array
     */
    private function getWeeklyHistory(array $yearHistory, int $year): array
    {
        $weeks = [];
        $weeks[] = $this->getFirstWeek($yearHistory, $year);
        while (count($yearHistory) != 0) {
            $weeks[] = array_splice($yearHistory, 0, 7);
        }
        $result = [];
        foreach ($weeks as $week) {
            $result[] = $this->getWeekMedian($week);
        }

        return $result;
    }

    /**
     * @param array $yearHistory
     * @param int $year
     *
     * @return array
     */
    private function getFirstWeek(array &$yearHistory, int $year): array
    {
        $firstDayOfYear = $yearHistory[0];
        $firstDay = DateTime::createFromTimestamp(
            mktime(0, 0, 0, $firstDayOfYear['month'], $firstDayOfYear['day'], $year)
        );

        return match ($firstDay->format('w')) {
            '0' => array_splice($yearHistory, 0, 1),
            '1' => array_splice($yearHistory, 0, 7),
            default => array_splice(
                $yearHistory,
                0,
                8 - (int) $firstDay->format('w')
            ),
        };
    }

    /**
     * @param array $week
     *
     * @return array
     */
    private function getWeekMedian(array $week): array
    {
        static $weekNumber;
        if (!isset($weekNumber)) {
            $weekNumber = 1;
        } else {
            $weekNumber++;
        }
        return [
            'WEEK_NUMBER' => $weekNumber,
            'TEMP' => $this->getWeekMedianTemperature($week),
            'HUMIDITY' => $this->getWeekMedianHumidity($week),
            'PRESSURE' => $this->getWeekMedianPressure($week),
        ];
    }

    private function getWeekMedianTemperature(array $week): float
    {
        $temperatures = array_map(fn($day) => $day['temp']['median'], $week);
        $weeklyMedian = array_sum($temperatures) / count($temperatures);

        return MeasurementConverter::fromKelvinTo($this->units, $weeklyMedian, 3);
    }

    /**
     * @param array $week
     *
     * @return float
     */
    private function getWeekMedianHumidity(array $week): float
    {
        $humidityWeekly = array_map(fn($day) => $day['humidity']['median'], $week);
        $weeklyMedian = array_sum($humidityWeekly) / count($humidityWeekly);

        return round($weeklyMedian, 3);
    }

    /**
     * @param array $week
     *
     * @return float
     */
    private function getWeekMedianPressure(array $week): float
    {
        $pressureWeekly = array_map(fn($day) => $day['temp']['median'], $week);
        $weeklyMedian = array_sum($pressureWeekly) / count($pressureWeekly);

        return MeasurementConverter::fromHPatoMmHg($weeklyMedian, 3);
    }
}
