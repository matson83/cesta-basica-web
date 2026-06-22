<?php

namespace Database\Seeders;

use App\Models\Cesta;
use App\Models\Distribuicao;
use App\Models\Familia;
use App\Models\Produto;
use Illuminate\Database\Seeder;

class CestaBasicaSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = collect([
            ['nome' => 'Arroz branco 5kg', 'categoria' => 'Grãos e cereais', 'unidade' => 'pacote', 'estoque' => 85, 'quantidade_por_cesta' => 1, 'preco' => 25.00],
            ['nome' => 'Feijão carioca 1kg', 'categoria' => 'Grãos e cereais', 'unidade' => 'pacote', 'estoque' => 120, 'quantidade_por_cesta' => 2, 'preco' => 8.50],
            ['nome' => 'Óleo de soja 900ml', 'categoria' => 'Alimentos', 'unidade' => 'unidade', 'estoque' => 12, 'quantidade_por_cesta' => 1, 'preco' => 6.00],
            ['nome' => 'Açúcar cristal 1kg', 'categoria' => 'Alimentos', 'unidade' => 'pacote', 'estoque' => 64, 'quantidade_por_cesta' => 1, 'preco' => 4.50],
            ['nome' => 'Macarrão espaguete 500g', 'categoria' => 'Grãos e cereais', 'unidade' => 'pacote', 'estoque' => 98, 'quantidade_por_cesta' => 2, 'preco' => 3.75],
            ['nome' => 'Café torrado 500g', 'categoria' => 'Bebidas', 'unidade' => 'pacote', 'estoque' => 8, 'quantidade_por_cesta' => 1, 'preco' => 12.00],
            ['nome' => 'Sabão em barra', 'categoria' => 'Higiene', 'unidade' => 'unidade', 'estoque' => 200, 'quantidade_por_cesta' => 4, 'preco' => 2.20],
            ['nome' => 'Leite em pó 400g', 'categoria' => 'Alimentos', 'unidade' => 'lata', 'estoque' => 18, 'quantidade_por_cesta' => 1, 'preco' => 14.90],
        ])->map(fn (array $dados) => Produto::create($dados));

        $cestaBasica = Cesta::create([
            'nome' => 'Cesta Família Básica',
            'descricao' => 'Composição padrão para famílias de 4 pessoas.',
            'categoria' => 'Padronizadas',
            'ativo' => true,
        ]);
        $cestaBasica->produtos()->attach([
            $produtos[0]->id => ['quantidade' => 1],
            $produtos[1]->id => ['quantidade' => 2],
            $produtos[2]->id => ['quantidade' => 1],
            $produtos[3]->id => ['quantidade' => 1],
            $produtos[4]->id => ['quantidade' => 2],
        ]);

        $cestaProtecao = Cesta::create([
            'nome' => 'Cesta Proteção',
            'descricao' => 'Itens essenciais de alimentação e higiene.',
            'categoria' => 'Especiais',
            'ativo' => true,
        ]);
        $cestaProtecao->produtos()->attach([
            $produtos[0]->id => ['quantidade' => 1],
            $produtos[1]->id => ['quantidade' => 1],
            $produtos[6]->id => ['quantidade' => 4],
        ]);

        Cesta::create([
            'nome' => 'Cesta Emergencial',
            'descricao' => 'Distribuição rápida em situações de urgência.',
            'categoria' => 'Especiais',
            'ativo' => false,
        ]);

        $familias = collect([
            ['nome_responsavel' => 'Maria Silva', 'cpf' => '123.456.789-00', 'num_membros' => 4, 'telefone' => '(11) 98765-4321', 'bairro' => 'Centro', 'endereco' => 'Rua das Flores, 100', 'ativo' => true],
            ['nome_responsavel' => 'João Santos', 'cpf' => '987.654.321-00', 'num_membros' => 3, 'telefone' => '(11) 91234-5678', 'bairro' => 'Jardim Primavera', 'endereco' => 'Av. Brasil, 250', 'ativo' => true],
            ['nome_responsavel' => 'Ana Oliveira', 'cpf' => '456.789.123-00', 'num_membros' => 5, 'telefone' => '(11) 99876-5432', 'bairro' => 'Vila Nova', 'endereco' => 'Rua do Sol, 45', 'ativo' => true],
            ['nome_responsavel' => 'Carlos Pereira', 'cpf' => '321.654.987-00', 'num_membros' => 2, 'telefone' => '(11) 97654-3210', 'bairro' => 'Parque das Águas', 'endereco' => 'Travessa Azul, 12', 'ativo' => false],
            ['nome_responsavel' => 'Fernanda Costa', 'cpf' => '654.321.789-00', 'num_membros' => 6, 'telefone' => '(11) 96543-2109', 'bairro' => 'Centro', 'endereco' => 'Rua Verde, 88', 'ativo' => true],
        ])->map(fn (array $dados) => Familia::create($dados));

        Distribuicao::create(['familia_id' => $familias[0]->id, 'cesta_id' => $cestaBasica->id, 'data_entrega' => now()->subDays(3), 'responsavel' => 'Admin', 'status' => Distribuicao::STATUS_PAGO]);
        Distribuicao::create(['familia_id' => $familias[1]->id, 'cesta_id' => $cestaBasica->id, 'data_entrega' => now()->subDays(4), 'responsavel' => 'Admin', 'status' => Distribuicao::STATUS_PENDENTE]);
        Distribuicao::create(['familia_id' => $familias[2]->id, 'cesta_id' => $cestaProtecao->id, 'data_entrega' => now()->subDays(5), 'responsavel' => 'Admin', 'status' => Distribuicao::STATUS_PAGO]);
        Distribuicao::create(['familia_id' => $familias[3]->id, 'cesta_id' => $cestaBasica->id, 'data_entrega' => now()->subDays(6), 'responsavel' => 'Admin', 'status' => Distribuicao::STATUS_PAGO]);
        Distribuicao::create(['familia_id' => $familias[4]->id, 'cesta_id' => $cestaBasica->id, 'data_entrega' => now()->subDays(7), 'responsavel' => 'Admin', 'status' => Distribuicao::STATUS_CANCELADO]);
    }
}
