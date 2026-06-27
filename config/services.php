<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Confrapix (gateway de pagamento PIX)
    |--------------------------------------------------------------------------
    |
    | Credenciais e endpoints do gateway Confrapix. O token de acesso é lido de
    | CONFRAPIX_TOKEN. Os caminhos de endpoint ficam isolados aqui para que
    | possam ser ajustados conforme a documentação oficial sem alterar o código.
    | Docs: https://doc.confrapix.com.br/
    |
    */
    /*
    |--------------------------------------------------------------------------
    | Evolution API (gateway de WhatsApp)
    |--------------------------------------------------------------------------
    |
    | Usado para o envio automático das mensagens de impulsionamento a partir do
    | número do gestor (conectado via QR Code na Evolution API). Quando as
    | credenciais não estão definidas, o sistema cai no envio manual (wa.me).
    | Docs: https://doc.evolution-api.com/
    |
    */
    'evolution' => [
        'base_url' => env('EVOLUTION_API_URL'),
        'api_key' => env('EVOLUTION_API_KEY'),
        'instance' => env('EVOLUTION_INSTANCE'),
        'timeout' => (int) env('EVOLUTION_TIMEOUT', 30),
    ],

    'confrapix' => [
        'token' => env('CONFRAPIX_TOKEN'),
        'base_url' => env('CONFRAPIX_BASE_URL', 'https://api.confrapix.com.br'),
        'timeout' => (int) env('CONFRAPIX_TIMEOUT', 30),
        'endpoints' => [
            'create_pix' => env('CONFRAPIX_ENDPOINT_CREATE_PIX', '/api/transaction-ec/store'),
            'get_charge' => env('CONFRAPIX_ENDPOINT_GET_CHARGE', '/api/transaction-ec/{id}'),
            'show_charge' => env('CONFRAPIX_ENDPOINT_SHOW_CHARGE', '/api/transaction-ec/show/{id}'),
            'list_charges' => env('CONFRAPIX_ENDPOINT_LIST_CHARGES', '/api/transaction-ec/index'),
        ],
    ],

];
