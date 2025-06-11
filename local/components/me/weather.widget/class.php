<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Me\Weather\Service\Weather\WeatherBase;
use Me\Weather\Service\Weather\Factory;

class WeatherWidgetComponent extends CBitrixComponent
{
    private const PARENT_MODULE = 'weather.forecast';
    private const DEFAULT_CITY = 'Moscow';
    private const DEFAULT_MEASUREMENT = 'C';
    private const DEFAULT_FORMAT = 'list';
    private const TITLE_FORMAT = 'Weather in %s';

    private Main\ErrorCollection $errorCollection;

    public function __construct($component = null)
    {
        $this->initContext();
        parent::__construct($component);
    }

    public function onPrepareComponentParams($arParams): array
    {
        if (empty($arParams['WEATHER_SERVICE'])) {
            $this->errorCollection->setError(
                new Main\Error(
                    Loc::getMessage('WEATHER_FORECAST_SERVICE_NAME_ERROR')
                )
            );
        }
        if (empty($arParams['SERVICE_API_KEY'])) {
            $this->errorCollection->setError(
                new Main\Error(
                    Loc::getMessage('WEATHER_FORECAST_SERVICE_API_KEY_ERROR')
                )
            );
        }
        if (empty($arParams['CITY'])) {
            $arParams['CITY'] = self::DEFAULT_CITY;
        }
        if (empty($arParams['MEASUREMENT_UNITS'])) {
            $arParams['MEASUREMENT_UNITS'] = self::DEFAULT_MEASUREMENT;
        }
        if (empty($arParams['FORMAT'])) {
            $arParams['FORMAT'] = self::DEFAULT_FORMAT;
        }

        return parent::onPrepareComponentParams($arParams);
    }

    /**
     * @return void
     * @throws Main\SystemException
     */
    public function executeComponent(): void
    {
        if (!$this->errorCollection->isEmpty()) {
            foreach ($this->errorCollection->getValues() as $error) {
                ShowError($error->getMessage());
            }

            return;
        }
        $this->enrichArResult();
        if ($this->errorCollection->isEmpty()) {
            $this->includeComponentTemplate();
            return;
        }
        foreach ($this->errorCollection->getValues() as $error) {
            ShowError($error->getMessage());
        }
    }

    /**
     * @return void
     */
    private function enrichArResult(): void
    {
        try {
            $weatherService = $this->getService();
            $this->arResult['CURRENT_WEATHER'] = $weatherService->getCurrentWeather();
            $this->arResult['TOMORROW_WEATHER'] = $weatherService->getTomorrowWeather();
            $this->arResult['LAST_10_DAYS_WEATHER'] = $weatherService->getLast10Days();
            $this->arResult['LAST_YEAR_WEATHER'] = $weatherService->getLastYearWeekly();
        } catch (Main\SystemException $exception) {
            CEventLog::Add([
                'SEVERITY' => 'ERROR',
                'AUDIT_TYPE_ID' => '',
                'MODULE_ID' => 'weather.forecast',
                'ITEM_ID' => $exception->getCode(),
                'DESCRIPTION' => $exception->getMessage(),
            ]);
            $this->arResult['ERROR'] = true;
        }

        $this->arResult['TITLE'] = $this->constructTitle();
        $this->arResult['TEMP_UNITS'] = $this->getTempUnits();
    }

    private function initContext(): void
    {
        $this->errorCollection = new Main\ErrorCollection();
        try {
            Main\Loader::requireModule(self::PARENT_MODULE);
        } catch (Main\LoaderException $exception) {
            $this->errorCollection->setError(
                new Main\Error(
                    $exception->getMessage()
                )
            );
            CEventLog::Add([
                'SEVERITY' => 'ERROR',
                'AUDIT_TYPE_ID' => '',
                'MODULE_ID' => 'weather.forecast',
                'ITEM_ID' => $exception->getCode(),
                'DESCRIPTION' => $exception->getMessage(),
            ]);
        }
    }

    private function constructTitle(): string
    {
        return sprintf(self::TITLE_FORMAT, $this->arParams['CITY']);
    }

    /**
     * @return WeatherBase
     * @throws Main\SystemException
     */
    private function getService(): WeatherBase
    {
        return Factory::getService(
            $this->arParams['WEATHER_SERVICE'],
            $this->arParams['SERVICE_API_KEY'],
            $this->arParams['CITY'],
            $this->arParams['MEASUREMENT_UNITS']
        );
    }



    /**
     * @return string
     */
    private function getTempUnits(): string
    {
        return match ($this->arParams['MEASUREMENT_UNITS']) {
            'F' => '°F',
            'C' => '°C'
        };
    }
}