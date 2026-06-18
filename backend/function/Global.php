<?php

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

define('ACTION_CREATE', 'getCreate');
define('ACTION_UPDATE', 'getUpdate');
define('ACTION_DELETE', 'getDelete');
define('ACTION_EMPTY', 'empty');
define('ACTION_TABLE', 'getTable');
define('ACTION_PRINT', 'getPrint');
define('ACTION_EXPORT', 'getExport');
define('ERROR_PERMISION', 'This action is unauthorized');
define('TOAST_SUCCESS', 'Data berhasil di proses !');
define('TOAST_FAILED', 'Proses Error !');

function formatDate($value, $datetime = false)
{
    if (empty($value)) {
        return null;
    }

    if ($datetime === false) {
        $format = 'd/m/Y';
    } elseif ($datetime === true) {
        $format = 'd/m/Y H:i:s';
    } else {
        $format = $datetime;
    }

    if ($value instanceof Carbon) {
        $value = $value->format($format);
    } elseif ($value instanceof CarbonImmutable) {
        $value = Carbon::parse($value)->format($format);
    } elseif (is_string($value)) {
        $value = Carbon::parse($value)->format($format);
    }

    return $value ?: null;
}

function formatAngka(int $value, $simbol = null)
{
    return $simbol.number_format($value, 0, ',', '.');
}

function formatLabel($value)
{
    $label = Str::of($value);
    if ($label->contains('_')) {
        $label = $label = $label->explode('_')->last();
    } else {
        $label = $label->replace('[]', '');
    }

    return ucfirst($label);
}

function unic_string($length)
{
    $chars = array_merge(range('a', 'z'), range('A', 'Z'));
    $length = intval($length) > 0 ? intval($length) : 16;
    $max = count($chars) - 1;
    $str = '';

    while ($length--) {
        shuffle($chars);
        $rand = mt_rand(0, $max);
        $str .= $chars[$rand];
    }

    return strtoupper($str);
}

function unic_number($length)
{
    $length = intval($length) > 0 ? intval($length) : 6;
    $min = (int) str_pad('1', $length, '0');
    $max = (int) str_pad('9', $length, '9');

    return random_int($min, $max);
}

function module($action = null)
{
    $module = request()->route()->getAction('name');

    if ($action) {
        return $module.'.'.$action;
    }

    return $module;
}

function moduleRoute($action = null, $params = [])
{
    $route = route(module($action), $params);

    return $route;
}

function nominalQRIS($qris_data, $amount)
{
    $amountStr = number_format($amount, 2, '.', '');
    $amountLength = strlen($amountStr);
    $amountField = '54'.str_pad($amountLength, 2, '0', STR_PAD_LEFT).$amountStr;

    // Parse QRIS TLV: hapus field 54 (nominal) yang lama dengan benar
    $new_qris = '';
    $i = 0;
    $len = strlen($qris_data);
    while ($i < $len - 4) {
        $tag = substr($qris_data, $i, 2);
        $valueLen = (int) substr($qris_data, $i + 2, 2);
        $totalLen = 4 + $valueLen;

        if ($tag === '54') {
            // Skip field 54 (nominal lama)
            $i += $totalLen;
            continue;
        }

        // Simpan field lainnya
        $new_qris .= substr($qris_data, $i, $totalLen);
        $i += $totalLen;
    }

    // Sisa string (CRC lama)
    if ($i < $len) {
        $new_qris .= substr($qris_data, $i);
    }

    // Hilangkan CRC lama (tag 63)
    $new_qris = preg_replace('/6304.{4}$/', '', $new_qris);

    // Tambah nominal baru + CRC
    $new_qris = $new_qris.$amountField.'6304';
    $crc = strtoupper(dechex(crc16($new_qris)));
    $crc = str_pad($crc, 4, '0', STR_PAD_LEFT);

    return $new_qris.$crc;
}

function crc16($data)
{
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($data); $i++) {
        $crc ^= ord($data[$i]) << 8;
        for ($j = 0; $j < 8; $j++) {
            if ($crc & 0x8000) {
                $crc = ($crc << 1) ^ 0x1021;
            } else {
                $crc = $crc << 1;
            }
            $crc &= 0xFFFF;
        }
    }

    return $crc;
}
