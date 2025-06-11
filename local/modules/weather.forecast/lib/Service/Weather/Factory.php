<?php

namespace Me\Weather\Service\Weather;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;

/**
 * class WeatherFactory
 *
 * @author  Vyacheslav Lipatov
 * @package Me\Weather\Service\Weather
 */
class Factory
{
    /**
     * @param string $service
     * @param string $apiKey
     * @param string $city
     * @param string $units
     *
     * @return OpenWeatherMap
     * @throws SystemException
     */
    public static function getService(string $service, string $apiKey, string $city, string $units): WeatherBase
    {
        return match ($service) {
            'open_weather_map' => new OpenWeatherMap($apiKey, $city, $units),
            'default' => throw new SystemException(
                Loc::getMessage('WEATHER_FACTOR_ERR_WEATHER_NOT_FOUND'),
            )
        };
    }
}
