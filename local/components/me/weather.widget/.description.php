<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME' => Loc::getMessage('WEATHER_FORECAST_COMPONENT_NAME'),
    'DESCRIPTION' => Loc::getMessage('WEATHER_FORECAST_COMPONENT_DESCRIPTION'),
    'PATH' => [
        'ID' => 'content',
        'CHILD' => [
            'ID' => 'vendor',
            'NAME' => Loc::getMessage('WEATHER_FORECAST_COMPONENT_VENDOR'),
        ],
    ],
    'CACHE_PATH' => 'Y',
    'COMPLEX' => 'N',
];