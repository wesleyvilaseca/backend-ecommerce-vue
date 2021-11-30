<?php

function validaCNPJ($cnpj)
{
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    $cnpj = (string)$cnpj;
    $cnpj_original = $cnpj;
    $primeiros_numeros_cnpj = substr($cnpj, 0, 12);

    if (!function_exists('multiplica_cnpj')) {
        function multiplica_cnpj($cnpj, $posicao = 5)
        {
            $calculo = 0;
            for ($i = 0; $i < strlen($cnpj); $i++) {
                $calculo = $calculo + ($cnpj[$i] * $posicao);
                $posicao--;
                if ($posicao < 2) {
                    $posicao = 9;
                }
            }
            return $calculo;
        }
    }

    $primeiro_calculo = multiplica_cnpj($primeiros_numeros_cnpj);
    $primeiro_digito = ($primeiro_calculo % 11) < 2 ? 0 :  11 - ($primeiro_calculo % 11);
    $primeiros_numeros_cnpj .= $primeiro_digito;
    $segundo_calculo = multiplica_cnpj($primeiros_numeros_cnpj, 6);
    $segundo_digito = ($segundo_calculo % 11) < 2 ? 0 :  11 - ($segundo_calculo % 11);

    $cnpj = $primeiros_numeros_cnpj . $segundo_digito;

    if ($cnpj === $cnpj_original) {
        return true;
    }
}

function validaCPF($cpf = null)
{

    if (empty($cpf)) {
        return false;
    }

    $cpf = preg_replace("/[^0-9]/", "", $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

    if (strlen($cpf) != 11) {
        return false;
    } else if (
        $cpf == '00000000000' ||
        $cpf == '11111111111' ||
        $cpf == '22222222222' ||
        $cpf == '33333333333' ||
        $cpf == '44444444444' ||
        $cpf == '55555555555' ||
        $cpf == '66666666666' ||
        $cpf == '77777777777' ||
        $cpf == '88888888888' ||
        $cpf == '99999999999'
    ) {
        return false;
    } else {

        for ($t = 9; $t < 11; $t++) {

            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}

function tofloat($num)
{
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    if (($dotPos > $commaPos) && $dotPos) {
        $sep = $dotPos;
    } elseif (($commaPos > $dotPos) && $commaPos) {
        $sep = $commaPos;
    } else {
        $sep = false;
    }

    if (!$sep) {
        return strval(floatval(preg_replace("/[^0-9]/", "", $num)));
    }
    return strval(floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep + 1, strlen($num)))
    ));
}

function numberFormat($number, $decimals = 2, $sep = ",", $k = "")
{
    $var = number_format($number, $decimals, $sep, $k);
    return  $var;
}

/*function getToken()
{
    $dia = date('d');
    $mes = date('m');
    $ano = date('Y');

    $hora = date('H');
    $minuto = date('i');
    $segundo = date('s');

    $token =  base64_encode($dia . $mes . $ano . $hora . $minuto . $segundo . ($dia + $hora) . ($mes + $minuto) . ($ano + $segundo));
    return $token;
}*/

function getToken(): string
{
    $dia = date('d');
    $mes = date('m');
    $ano = date('Y');

    $hora = date('H');
    $minuto = date('i');
    $segundo = date('s');

    $token =  base64_encode($dia . $mes . $ano . $hora . $minuto . $segundo . ($dia + $hora) . ($mes + $minuto) . ($ano + $segundo)) . "." . addDateToken();
    return $token;
}


function addDateToken(): string
{
    date_default_timezone_set('America/Belem');
    $now = date("d-m-Y H:i:s");
    $data_encondada = base64_encode($now);
    return $data_encondada;
}

function validBase64($string)
{
    $decoded = base64_decode($string, true);

    // Check if there is no invalid character in string
    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) return false;

    // Decode the string in strict mode and send the response
    if (!$decoded) return false;

    // Encode and compare it to original one
    if (base64_encode($decoded) != $string) return false;

    return true;
}

function redirectBack()
{
    $location = $_SERVER['HTTP_REFERER'];
    if (!isset($location)) {
        dd('redirectback');
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
    exit;
}


function geturl()
{
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    return $actual_link;
}

function b64encode($str)
{
    return strtr(base64_encode($str), '+/=', '._-');
}

function b64decode($str)
{
    return base64_decode(strtr($str, '._-', '+/='));
}