<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class weather_forecast extends CModule
{
    var $MODULE_ID = 'weather.forecast';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $MODULE_GROUP_RIGHTS = 'Y';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('WEATHER_FORECAST_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('WEATHER_FORECAST_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('WEATHER_FORECAST_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('WEATHER_FORECAST_PARTNER_URI');
    }

    public function InstallFiles()
    {
    }

    public function InstallDB()
    {
    }

    public function InstallEvents()
    {
    }

    public function InstallTasks()
    {
    }

    public function UnInstallTasks()
    {
    }

    public function UnInstallEvents()
    {
    }

    public function UnInstallDB()
    {
    }

    public function UnInstallFiles()
    {
    }

    public function GetModuleRightList()
    {
        return [
            'reference_id' => ['D', 'R', 'W'],
            'reference' => [
                Loc::getMessage('WEATHER_FORECAST_MODULE_ACCESS_DENIED'),
                Loc::getMessage('WEATHER_FORECAST_MODULE_ACCESS_OPENED'),
                Loc::getMessage('WEATHER_FORECAST_MODULE_ACCESS_FULL'),
            ],
        ];
    }

    public function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallTasks();

        ModuleManager::registerModule($this->MODULE_ID);

        return true;
    }

    public function DoUninstall()
    {
        $this->UnInstallTasks();
        $this->UnInstallEvents();
        $this->UnInstallDB();
        $this->UnInstallFiles();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}