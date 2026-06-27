<?php

use App\Models\Empresa;
use App\Models\Produto;
use App\Models\User;
use App\Notifications\BoasVindasDefinirSenha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('o gestor cria uma firma, gera o acesso e dispara o e-mail de boas-vindas', function () {
    Notification::fake();

    $gestor = criarGestor();

    $response = $this->actingAs($gestor)->post(route('gestor.empresas.store'), [
        'nome_fantasia' => 'Mercado do Bairro',
        'tipo_documento' => 'cnpj',
        'documento' => '12.345.678/0001-90',
        'email' => 'contato@mercado.test',
        'confrapix_token' => 'token-abc-123',
        'ativo' => '1',
    ]);

    $empresa = Empresa::where('email', 'contato@mercado.test')->firstOrFail();
    $user = User::where('email', 'contato@mercado.test')->firstOrFail();

    $response->assertRedirect(route('gestor.empresas.criada', $empresa));
    $response->assertSessionHas('link_definir_senha');

    expect($user->role)->toBe(User::ROLE_EMPRESA)
        ->and($user->empresa_id)->toBe($empresa->id)
        ->and($empresa->confrapix_token)->toBe('token-abc-123')
        ->and($empresa->tipo_documento)->toBe('cnpj')
        ->and($empresa->documento)->toBe('12.345.678/0001-90');

    Notification::assertSentTo($user, BoasVindasDefinirSenha::class);
});

it('aceita firma cadastrada com CPF', function () {
    Notification::fake();

    $gestor = criarGestor();

    $response = $this->actingAs($gestor)->post(route('gestor.empresas.store'), [
        'nome_fantasia' => 'Quitanda da Maria',
        'tipo_documento' => 'cpf',
        'documento' => '123.456.789-09',
        'email' => 'maria@quitanda.test',
        'confrapix_token' => 'token-cpf-1',
        'ativo' => '1',
    ]);

    $empresa = Empresa::where('email', 'maria@quitanda.test')->firstOrFail();

    $response->assertRedirect(route('gestor.empresas.criada', $empresa));

    expect($empresa->tipo_documento)->toBe('cpf')
        ->and($empresa->documento)->toBe('123.456.789-09');
});

it('rejeita documento com quantidade de dígitos incompatível com o tipo', function () {
    $gestor = criarGestor();

    $response = $this->actingAs($gestor)->from(route('gestor.empresas.create'))->post(route('gestor.empresas.store'), [
        'nome_fantasia' => 'Documento Inválido',
        'tipo_documento' => 'cpf',
        'documento' => '12.345.678/0001-90',
        'email' => 'invalido@firma.test',
        'confrapix_token' => 'token-x',
        'ativo' => '1',
    ]);

    $response->assertRedirect(route('gestor.empresas.create'));
    $response->assertSessionHasErrors('documento');
    expect(Empresa::where('email', 'invalido@firma.test')->exists())->toBeFalse();
});

it('exibe a tela de confirmação com o link de definição de senha', function () {
    [$empresa] = criarFirmaComUsuario();
    $gestor = criarGestor();
    $link = 'http://localhost/reset-password/abc?email=teste%40firma.test';

    $response = $this->actingAs($gestor)
        ->withSession(['link_definir_senha' => $link])
        ->get(route('gestor.empresas.criada', $empresa));

    $response->assertOk();
    $response->assertSee('Firma cadastrada com sucesso!');
    $response->assertSee($link, escape: false);
});

it('acessar a confirmação sem o fluxo de criação redireciona para os detalhes', function () {
    [$empresa] = criarFirmaComUsuario();
    $gestor = criarGestor();

    $this->actingAs($gestor)
        ->get(route('gestor.empresas.criada', $empresa))
        ->assertRedirect(route('gestor.empresas.show', $empresa));
});

it('isola produtos por firma (tenant)', function () {
    [$empresaA, $userA] = criarFirmaComUsuario(['nome_fantasia' => 'Firma A']);
    [$empresaB] = criarFirmaComUsuario(['nome_fantasia' => 'Firma B']);

    Produto::create(['empresa_id' => $empresaA->id, 'nome' => 'Arroz da A', 'unidade' => 'pacote', 'estoque' => 1, 'quantidade_por_cesta' => 1, 'preco' => 10]);
    Produto::create(['empresa_id' => $empresaB->id, 'nome' => 'Feijao da B', 'unidade' => 'pacote', 'estoque' => 1, 'quantidade_por_cesta' => 1, 'preco' => 10]);

    $response = $this->actingAs($userA)->get(route('produtos.index'));

    $response->assertOk();
    $response->assertSee('Arroz da A');
    $response->assertDontSee('Feijao da B');
});

it('impede que uma firma altere produto de outra firma', function () {
    [$empresaA, $userA] = criarFirmaComUsuario();
    [$empresaB] = criarFirmaComUsuario();

    $produtoB = Produto::create(['empresa_id' => $empresaB->id, 'nome' => 'Produto B', 'unidade' => 'unidade', 'estoque' => 1, 'quantidade_por_cesta' => 1, 'preco' => 5]);

    $response = $this->actingAs($userA)->delete(route('produtos.destroy', $produtoB));

    $response->assertForbidden();
    expect(Produto::whereKey($produtoB->id)->exists())->toBeTrue();
});

it('bloqueia firma na área do gestor e vice-versa', function () {
    [, $userEmpresa] = criarFirmaComUsuario();
    $gestor = criarGestor();

    $this->actingAs($userEmpresa)->get(route('gestor.empresas.index'))->assertForbidden();
    $this->actingAs($gestor)->get(route('produtos.index'))->assertForbidden();
});

it('exige autenticação para acessar áreas internas', function () {
    $this->get(route('produtos.index'))->assertRedirect(route('login'));
    $this->get(route('gestor.empresas.index'))->assertRedirect(route('login'));
});
