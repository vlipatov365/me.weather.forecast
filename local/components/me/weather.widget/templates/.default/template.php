<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Web\Json;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}


/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @var array $arResult
 * @var array $arParams
 */


$APPLICATION->setTitle($arResult['TITLE']);
if ($arResult['ERROR']) {
    ShowError(Loc::getMessage('WEATHER_WIDGET_ERROR'));
    return;
}
?>
<script>
    BX.message(<?=Json::encode(Loc::loadLanguageFile(__FILE__));?>)
</script>
<div class="weather-widget__wrapper __flex-column">
    <div class="weather-widget__current_weather __flex-column" id="currentWeather">
        <div class="current_weather weather-title">
            Сегодня (<?=date('d.m.Y')?>):
        </div>
        <div class="current_weather temperature">
            <?= Loc::getMessage('WEATHER_WIDGET_TEMPERATURE') ?>:
            <?= $arResult['CURRENT_WEATHER']['TEMP'].$arResult['TEMP_UNITS'] ?>
                                    (<?= $arResult['CURRENT_WEATHER']['NIGHT_TEMP'].$arResult['TEMP_UNITS'] ?>)
        </div>
        <div class="current_weather humidity">
            <?= Loc::getMessage('WEATHER_WIDGET_HUMIDITY') ?>:
            <?= $arResult['CURRENT_WEATHER']['HUMIDITY'] ?>%
        </div>
        <div class="current_weather pressure">
            <?= Loc::getMessage('WEATHER_WIDGET_PRESSURE') ?>:
            <?= $arResult['CURRENT_WEATHER']['PRESSURE'] . ' ' . Loc::getMessage('WEATHER_WIDGET_MM_HG') ?>
        </div>
    </div>
    <div class="weather-widget__tomorrow_weather __flex-column" id="currentWeather">
        <div class="tomorrow_weather weather-title">
            Завтра (<?=date('d.m.Y', strtotime('tomorrow'))?>):
        </div>
        <div class="tomorrow_weather temperature" id="tomorrow_weather">
            <?= Loc::getMessage('WEATHER_WIDGET_TEMPERATURE') ?>:
            <?= $arResult['TOMORROW_WEATHER']['TEMP'].$arResult['TEMP_UNITS'] ?>
                                    (<?= $arResult['TOMORROW_WEATHER']['NIGHT_TEMP'].$arResult['TEMP_UNITS'] ?>)
        </div>
        <div class="tomorrow_weather humidity">
            <?= Loc::getMessage('WEATHER_WIDGET_HUMIDITY') ?>:
            <?= $arResult['TOMORROW_WEATHER']['HUMIDITY'] ?>%
        </div>
        <div class="tomorrow_weather pressure">
            <?= Loc::getMessage('WEATHER_WIDGET_PRESSURE') ?>:
            <?= $arResult['TOMORROW_WEATHER']['PRESSURE'] . ' ' . Loc::getMessage('WEATHER_WIDGET_MM_HG') ?>
        </div>
    </div>
    <div class="weather-widget__last_10_days table" id="last10days">
        <table class="last10days_table">
            <thead>
                <tr>
                    <th><?= Loc::getMessage('WEATHER_WIDGET_DATE') ?></th>
                    <th>
                        <div class="temperature">
                            <?=Loc::getMessage('WEATHER_WIDGET_TEMPERATURE')?>
                        </div>
                    </th>
                    <th>
                        <div class="humidity">
                            <?=Loc::getMessage('WEATHER_WIDGET_HUMIDITY')?>
                        </div>
                    </th>
                    <th>
                        <div class="pressure">
                            <?=Loc::getMessage('WEATHER_WIDGET_PRESSURE')?>
                        </div>
                    </th>
                </tr>
            </thead>
            <?php foreach ($arResult['LAST_10_DAYS_WEATHER'] as $week): ?>
                <tr>
                    <td>
                        <?=
                        /**
                         * @var Bitrix\Main\Type\DateTime $week['DATE']
                         */
                            $week['DATE']->format('d.m.Y')
                        ?>
                    </td>
                    <td>
                        <?=$week['TEMP'].$arResult['TEMP_UNITS']?>(<?=$week['NIGHT_TEMP'].$arResult['TEMP_UNITS']?>)
                    </td>
                    <td>
                        <?=$week['HUMIDITY']?>%
                    </td>
                    <td>
                        <?=$week['PRESSURE']?> <?=Loc::getMessage('WEATHER_WIDGET_MM_HG')?>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    </div>
    <div class="weather-widget__last_year table" id="lastYear">
    <?php if ($arParams['FORMAT'] === 'table'): ?>
        <table class="last_year_table">
            <thead>
            <tr>
                <th><?= Loc::getMessage('WEATHER_WIDGET_DATE') ?></th>
                <th>
                    <div class="temperature">
                        <?=Loc::getMessage('WEATHER_WIDGET_TEMPERATURE')?>
                    </div>
                </th>
                <th>
                    <div class="humidity">
                        <?=Loc::getMessage('WEATHER_WIDGET_HUMIDITY')?>
                    </div>
                </th>
                <th>
                    <div class="pressure">
                        <?=Loc::getMessage('WEATHER_WIDGET_PRESSURE')?>
                    </div>
                </th>
            </tr>
            </thead>
            <?php foreach ($arResult['LAST_YEAR_WEATHER'] as $week): ?>
                <tr>
                    <td>
                        <?= $week['WEEK_NUMBER'] ?>
                    </td>
                    <td>
                        <?=$week['TEMP'].$arResult['TEMP_UNITS']?>(<?=$week['NIGHT_TEMP'].$arResult['TEMP_UNITS']?>)
                    </td>
                    <td>
                        <?=$week['HUMIDITY']?>%
                    </td>
                    <td>
                        <?=$week['PRESSURE']?> <?=Loc::getMessage('WEATHER_WIDGET_MM_HG')?>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php else: ?>
        <?php
            Asset::getInstance()->addString('<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>')
        ?>
        <div>
            <canvas id="lastYearChartTemperature"></canvas>
            <canvas id="lastYearChartHumidity"></canvas>
            <canvas id="lastYearChartPressure"></canvas>
        </div>

        <script>
            const YearChart = new BX.Weather.Widget.YearCharts(
				<?=Json::encode(array_column($arResult['LAST_YEAR_WEATHER'], 'WEEK_NUMBER'))?>,
                <?=Json::encode(array_column($arResult['LAST_YEAR_WEATHER'], 'TEMP'))?>,
                <?=Json::encode(array_column($arResult['LAST_YEAR_WEATHER'], 'HUMIDITY'))?>,
                <?=Json::encode(array_column($arResult['LAST_YEAR_WEATHER'], 'PRESSURE'))?>,
            );

            YearChart.renderTemperatureChart('lastYearChartTemperature');
			YearChart.renderHumidityChart('lastYearChartHumidity');
			YearChart.renderPressureChart('lastYearChartPressure');
        </script>
    <?php endif; ?>
    </div>
</div>