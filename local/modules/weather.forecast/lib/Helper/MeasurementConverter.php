<?php

namespace Me\Weather\Helper;

/**
 * class MeasurementConverter
 *
 * @author  Vyacheslav Lipatov
 * @package Me\Weather\Helper
 */
class MeasurementConverter
{
    private const CELSIUS = 'C';
    private const FAHRENHEIT = 'F';

    /**
     * Условный конвертер из Кельвинов в заданную систему измерения.
     * @param string $units
     * @param float $data
     * @param int $precision
     *
     * @return float
     */
    public static function fromKelvinTo(string $units, float $data, int $precision = 1): float
    {
        return match ($units) {
            'C' => self::fromKelvinToCelsius($data, $precision),
            'F' => self::fromKelvinToFahrenheit($data, $precision),
        };
    }

    /**
     * Конвертер из Кельвинов в градусы Цельсия.
     * @param float $data
     * @param int $precision
     *
     * @return float
     */
    public static function fromKelvinToCelsius(float $data, int $precision = 1): float
    {
        return round($data - 273.15, $precision);
    }

    /**
     * Конвертер из Кельвинов в градусы Фаренгейта.
     * @param float $data
     * @param int $precision
     *
     * @return float
     */
    public static function fromKelvinToFahrenheit(float $data, int $precision = 1): float
    {
        return round(($data - 273.15) * 9/5 + 32, $precision);
    }

    /**
     * Конвертер из гектопаскалей в мм ртутного столба.
     * @param float $data
     * @param int $precision
     *
     * @return float
     */
    public static function fromHPatoMmHg(float $data, int $precision = 1): float
    {
        return round($data * 0.7500615, $precision);
    }
}
