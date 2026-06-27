<?php

use App\Models\Cesta;
use App\Models\Distribuicao;
use App\Models\Familia;
use App\Models\Pagamento;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    [$this->empresa, $this->user] = criarFirmaComUsuario();
    $this->actingAs($this->user);
});

it('exibe comprovante para pagamento confirmado', function () {
    $familia = Familia::create([
        'empresa_id' => $this->empresa->id,
        'nome_responsavel' => 'Maria Silva',
        'cpf' => '123.456.789-00',
        'num_membros' => 4,
        'telefone' => '(11) 98765-4321',
        'bairro' => 'Centro',
        'ativo' => true,
    ]);

    $produto = Produto::create([
        'empresa_id' => $this->empresa->id,
        'nome' => 'Arroz 5kg',
        'preco' => 25.90,
        'estoque' => 10,
    ]);

    $cesta = Cesta::create(['empresa_id' => $this->empresa->id, 'nome' => 'Cesta Básica', 'ativo' => true]);
    $cesta->produtos()->attach($produto->id, ['quantidade' => 2]);

    $distribuicao = Distribuicao::create([
        'empresa_id' => $this->empresa->id,
        'familia_id' => $familia->id,
        'cesta_id' => $cesta->id,
        'data_entrega' => now()->toDateString(),
        'responsavel' => 'Admin',
        'status' => Distribuicao::STATUS_PAGO,
    ]);

    $pagamento = Pagamento::create([
        'empresa_id' => $this->empresa->id,
        'distribuicao_id' => $distribuicao->id,
        'referencia' => (string) str()->uuid(),
        'metodo' => 'pix',
        'status' => Pagamento::STATUS_PAGO,
        'valor_centavos' => 5180,
        'pagador_nome' => 'Maria Silva',
        'pagador_cpf' => '123.456.789-00',
        'charge_id' => 'charge-123',
        'payload_gateway' => [
            'transaction' => [
                'payed_in' => now()->toIso8601String(),
            ],
        ],
    ]);

    $response = $this->get(route('pagamentos.comprovante', $pagamento));

    $response->assertOk();
    $response->assertSee('Comprovante de pagamento');
    $response->assertSee('Pagamento confirmado');
    $response->assertSee($pagamento->numeroComprovante());
    $response->assertSee('Maria Silva');
    $response->assertSee('Cesta Básica');
    $response->assertSee('R$ 51,80');
});

it('redireciona comprovante quando pagamento ainda está pendente', function () {
    $pagamento = Pagamento::create([
        'empresa_id' => $this->empresa->id,
        'referencia' => (string) str()->uuid(),
        'metodo' => 'pix',
        'status' => Pagamento::STATUS_PENDENTE,
        'valor_centavos' => 1000,
    ]);

    $response = $this->get(route('pagamentos.comprovante', $pagamento));

    $response->assertRedirect(route('pagamentos.pix', $pagamento));
    $response->assertSessionHas('error');
});

it('redireciona para a tela de sucesso após confirmação do pix', function () {
    $pagamento = Pagamento::create([
        'empresa_id' => $this->empresa->id,
        'referencia' => (string) str()->uuid(),
        'metodo' => 'pix',
        'status' => Pagamento::STATUS_PAGO,
        'valor_centavos' => 1000,
    ]);

    $response = $this->get(route('pagamentos.pix', $pagamento));

    $response->assertRedirect(route('pagamentos.sucesso', $pagamento));
});
