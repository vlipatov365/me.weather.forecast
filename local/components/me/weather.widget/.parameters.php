<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = [
    'GROUPS' => [
        'MAIN' => [
            'NAME' => Loc::getMessage('WEATHER_FORECAST_GROUP_MAIN'),
            'SORT' => 100,
        ],
    ],
    'PARAMETERS' => [
        'WEATHER_SERVICE' => [
            'PARENT' => 'MAIN',
            'NAME' => Loc::getMessage('WEATHER_FORECAST_SERVICE_NAME'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'open_weather_map',
            'REFRESH' => 'Y',
            'VALUES' => [
                'open_weather_map' => 'OpenWeatherMap',
            ]
        ],
        'SERVICE_API_KEY' => [
            'PARENT' => 'MAIN',
            'NAME' => Loc::getMessage('WEATHER_FORECAST_SERVICE_API_KEY'),
        ],
        'CITY' => [
            'PARENT' => 'MAIN',
            'NAME' => Loc::getMessage('WEATHER_FORECAST_CITY'),
            'TYPE' => 'string',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'Moscow',
        ],
        'MEASUREMENT_UNITS' => [
            'PARENT' => 'MAIN',
            'NAME' => Loc::getMessage('WEATHER_FORECAST_MEASUREMENT_UNITS'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'C',
            'VALUES' => [
                'C' => 'Цельсий',
                'F' => 'Фаренгейт'
            ]
        ],
        'FORMAT' => [
            'PARENT' => 'MAIN',
            'NAME' => Loc::getMessage('WEATHER_FORECAST_FORMAT'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'N',
            'VALUES' => [
                'table' => Loc::getMessage('WEATHER_FORECAST_FORMAT_TABLE'),
                'chart' => Loc::getMessage('WEATHER_FORECAST_FORMAT_CHART')
            ]
        ],
        'CACHE_TIME' => [],
    ],
];