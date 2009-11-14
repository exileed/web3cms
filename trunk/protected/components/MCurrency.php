<?php
/**
 * MCurrency class file.
 * Manage currency.
 */
class MCurrency
{
    /**
     * Formats a number using the currency format defined in the locale.
     * @param mixed the number to be formatted
     * @param string 3-letter ISO 4217 code. For example, the code "USD" represents the US Dollar and "EUR" represents the Euro currency.
     * The currency placeholder in the pattern will be replaced with the currency symbol.
     * @return string the formatting result.
     */
    public function format($value,$currency='USD')
    {
        return Yii::app()->numberFormatter->formatCurrency($value,$currency);
    }
}