<?php

use App\Models\Cesta;
use App\Models\Distribuicao;
use App\Models\Familia;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    [$this->empresa, $this->user] = criarFirmaComUsuario();
    $this->actingAs($this->user);
});

it('atualiza uma distribuição existente', function () {
    $familia = Familia::create([
        'empresa_id' => $this->empresa->id,
        'nome_responsavel' => 'Maria Silva',
        'cpf' => '123.456.789-00',
        'num_membros' => 4,
        'ativo' => true,
    ]);

    $cesta = Cesta::create([
        'empresa_id' => $this->empresa->id,
        'nome' => 'Cesta Básica',
        'ativo' => true,
    ]);

    $distribuicao = Distribuicao::create([
        'empresa_id' => $this->empresa->id,
        'familia_id' => $familia->id,
        'cesta_id' => $cesta->id,
        'data_entrega' => now()->toDateString(),
        'responsavel' => 'Admin',
        'status' => Distribuicao::STATUS_PENDENTE,
    ]);

    $response = $this->put(route('distribuicoes.update', $distribuicao), [
        'form_context' => 'distribuicao-update-'.$distribuicao->id,
        'familia_id' => $familia->id,
        'cesta_id' => $cesta->id,
        'data_entrega' => now()->addDay()->toDateString(),
        'responsavel' => 'Coordenador',
        'status' => Distribuicao::STATUS_PAGO,
        'observacoes' => 'Entrega confirmada',
    ]);

    $response->assertRedirect(route('distribuicoes.index'));
    $response->assertSessionHas('status');

    $distribuicao->refresh();

    expect($distribuicao->responsavel)->toBe('Coordenador')
        ->and($distribuicao->status)->toBe(Distribuicao::STATUS_PAGO)
        ->and($distribuicao->observacoes)->toBe('Entrega confirmada');
});

it('aceita cesta vazia na atualização', function () {
    $familia = Familia::create([
        'empresa_id' => $this->empresa->id,
        'nome_responsavel' => 'João Santos',
        'cpf' => '987.654.321-00',
        'num_membros' => 3,
        'ativo' => true,
    ]);

    $distribuicao = Distribuicao::create([
        'empresa_id' => $this->empresa->id,
        'familia_id' => $familia->id,
        'cesta_id' => null,
        'data_entrega' => now()->toDateString(),
        'responsavel' => 'Admin',
        'status' => Distribuicao::STATUS_PENDENTE,
    ]);

    $response = $this->put(route('distribuicoes.update', $distribuicao), [
        'form_context' => 'distribuicao-update-'.$distribuicao->id,
        'familia_id' => $familia->id,
        'cesta_id' => '',
        'data_entrega' => now()->toDateString(),
        'responsavel' => 'Novo responsável',
        'status' => Distribuicao::STATUS_PENDENTE,
        'observacoes' => null,
    ]);

    $response->assertRedirect(route('distribuicoes.index'));
    $response->assertSessionHasNoErrors();

    expect($distribuicao->fresh()->responsavel)->toBe('Novo responsável');
});
