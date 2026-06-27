<?php

namespace App\Support;

class WhatsApp
{
    /**
     * Normaliza um telefone para o formato internacional (somente dígitos, com DDI).
     * Assume Brasil (55) quando o número não traz o código do país e corrige o 9º
     * dígito de celulares antigos (8 dígitos após o DDD).
     */
    public static function normalizar(?string $telefone): ?string
    {
        $digitos = preg_replace('/\D/', '', (string) $telefone);

        if ($digitos === '') {
            return null;
        }

        // Remove zero à esquerda de discagem (ex.: 011...).
        $digitos = ltrim($digitos, '0');

        if (str_starts_with($digitos, '55')) {
            $digitos = self::corrigirNonoDigitoBrasil(substr($digitos, 2));
            $digitos = '55'.$digitos;
        } elseif (strlen($digitos) <= 11) {
            $digitos = self::corrigirNonoDigitoBrasil($digitos);
            $digitos = '55'.$digitos;
        }

        return strlen($digitos) >= 12 ? $digitos : null;
    }

    /**
     * Formato exigido pela Evolution API: dígitos + @s.whatsapp.net
     */
    public static function jid(?string $telefone): ?string
    {
        $numero = self::normalizar($telefone);

        return $numero !== null ? $numero.'@s.whatsapp.net' : null;
    }

    /**
     * Celular BR sem o 9 após o DDD (10 dígitos nacionais) => insere o 9.
     */
    private static function corrigirNonoDigitoBrasil(string $nacional): string
    {
        if (strlen($nacional) === 10 && $nacional[2] !== '9') {
            return substr($nacional, 0, 2).'9'.substr($nacional, 2);
        }

        return $nacional;
    }

    /**
     * Gera um link click-to-chat (wa.me) com o texto pré-preenchido.
     */
    public static function link(?string $telefone, string $texto = ''): ?string
    {
        $numero = self::normalizar($telefone);

        if ($numero === null) {
            return null;
        }

        $url = 'https://wa.me/'.$numero;

        if ($texto !== '') {
            $url .= '?text='.rawurlencode($texto);
        }

        return $url;
    }
}
