<?php

namespace App\Services\WhatsApp\Contracts;

interface WhatsAppGateway
{
    /**
     * Indica se o gateway possui credenciais suficientes para enviar mensagens.
     */
    public function configurado(): bool;

    /**
     * Envia uma mensagem de texto. Deve lançar exceção em caso de falha.
     *
     * @param  string  $numero  Telefone do destinatário em formato internacional (somente dígitos).
     */
    public function enviarTexto(string $numero, string $texto): void;

    /**
     * Envia uma imagem (por URL), com legenda opcional. Deve lançar exceção em caso de falha.
     */
    public function enviarImagem(string $numero, string $url, string $legenda = ''): void;
}
