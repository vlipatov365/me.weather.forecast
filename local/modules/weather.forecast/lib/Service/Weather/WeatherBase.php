<?php

namespace Me\Weather\Service\Weather;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\HttpClient;
use Me\Weather\Trait\CacheTrait;
use Me\Weather\Trait\ResponseCodeTrait;

/**
 * class WeatherBase
 *
 * @author  Vyacheslav Lipatov
 * @package Me\Weather\Service\Weather
 */
abstract class WeatherBase
{
    use CacheTrait;
    use ResponseCodeTrait;

    private const AVAILABLE_UNITS = ['C', 'F'];
    protected const LANGUAGE = 'ru';
    protected Cache $cacheInstance;

    /**
     * @param string $apiKey
     * @param string $city
     * @param string $units
     *
     * @throws SystemException
     */
    public function __construct(protected string $apiKey, protected string $city, protected string $units)
    {
        if (mb_strlen($this->apiKey) === 0) {
            throw new SystemException(Loc::getMessage('WEATHER_FORECAST_SERVICE_API_KEY_LENGTH_ERROR'));
        }
        if (mb_strlen($this->city) === 0) {
            throw new SystemException(Loc::getMessage('WEATHER_FORECAST_SERVICE_CITY_LENGTH_ERROR'));
        }

        if (mb_strlen($this->units) === 0 || !in_array($units, self::AVAILABLE_UNITS)) {
            $this->units = self::AVAILABLE_UNITS[0];
        }

        $this->cacheInstance = Cache::createInstance();
    }

    /**
     * @param string $url
     * @param array|null $options
     *
     * @return mixed
     */
    protected function fetch(string $url, null|array $options = null): mixed
    {
        static $httpClient;

        if ($httpClient === null) {
            $httpClient = new HttpClient($options);
        }
        $response = $httpClient->get($url);

        return json_decode($response, true);
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getCacheKey(string $method): string
    {
        return $this->getServiceName() . '_' . $method . '_' . $this->city . '_' . $this->units . '_' . $this->apiKey;
    }

    /**
     * @param array $data
     * @param false|int $ttl
     * @param false|string $uniqueString
     * @param false|string $initDir
     * @param array $vars
     * @param string $baseDir
     *
     * @return void
     */
    protected function writeCache(
        array $data,
        false|int $ttl = false,
        false|string $uniqueString = false,
        false|string $initDir = false,
        array $vars = [],
        string $baseDir = 'cache'
    ): void {
        $this->cacheInstance->startDataCache(
            $ttl,
            $uniqueString,
            $initDir,
            $vars,
            $baseDir
        );
        $this->cacheInstance->endDataCache($data);
    }

    abstract protected function getServiceName(): string;

    /**
     * @return array{
     *     TEMP:int,
     *     NIGHT_TEMP:int,
     *     HUMIDITY:int,
     *     PRESSURE:int
     * }
     * @throws SystemException
     */
    abstract public function getCurrentWeather(): array;

    /**
     * @return array{
     *     TEMP:int,
     *     NIGHT_TEMP:int,
     *     HUMIDITY:int,
     *     PRESSURE:int
     * }
     * @throws SystemException
     */
    abstract public function getTomorrowWeather(): array;

    /**
     * @return array<int, array{
     *     DATE: DateTime,
     *     TEMP: int,
     *     NIGHT_TEMP: int,
     *     HUMIDITY: int,
     *     PRESSURE: int
     * }>
     * @throws SystemException
     */

    abstract public function getLast10Days(): array;

    /**
     * @return array<int, array{
     *     WEEK_NUMBER: int,
     *     TEMP: int,
     *     NIGHT_TEMP: int,
     *     HUMIDITY: int,
     *     PRESSURE: int
     * }>
     * @throws SystemException
     */
    abstract public function getLastYearWeekly(): array;
}
