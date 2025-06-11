<?php

use Bitrix\Main\Application;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

global $APPLICATION;

$APPLICATION->IncludeComponent(
	"me:weather.widget",
	".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"WEATHER_SERVICE" => "open_weather_map",
		"SERVICE_API_KEY" => "d119842235df8cddf0ec2d3b3e4ba2a4",
		"CITY" => "Moscow",
		"MEASUREMENT_UNITS" => "C",
		"FORMAT" => "table"
	),
	false
);


require Application::getDocumentRoot() . '/bitrix/footer.php';