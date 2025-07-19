<?php
// =====================================================
// utils/Sanitizador.php - Sanitizar y Validar Datos
// =====================================================

class Sanitizador
{
    // ✅ Sanitizar cadena de texto
    public static function texto($string)
    {
        return htmlspecialchars(strip_tags(trim($string)));
    }

    // ✅ Sanitizar email
    public static function email($email)
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    // ✅ Sanitizar número entero
    public static function entero($numero)
    {
        return filter_var($numero, FILTER_SANITIZE_NUMBER_INT);
    }

    // ✅ Sanitizar número flotante
    public static function flotante($numero)
    {
        return filter_var($numero, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    // ✅ Validar email
    public static function validarEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // ✅ Validar longitud de texto
    public static function validarLongitud($string, $min = 1, $max = 255)
    {
        $len = mb_strlen($string);
        return $len >= $min && $len <= $max;
    }

    // ✅ Validar solo letras y espacios
    public static function soloLetras($string)
    {
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/u', $string);
    }

    // ✅ Validar entero positivo
    public static function enteroPositivo($numero)
    {
        return filter_var($numero, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    }
}
