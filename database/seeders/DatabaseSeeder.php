<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Gestor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gestor (LA) único — criado apenas via seed.
        $gestorUser = User::updateOrCreate(
            ['email' => env('GESTOR_EMAIL', 'gestor@cestabasica.test')],
            [
                'name' => 'Gestor',
                'password' => env('GESTOR_PASSWORD', 'password'),
                'role' => User::ROLE_GESTOR,
                'empresa_id' => null,
            ]
        );

        // Cadastro do gestor (CPF e telefone remetente das mensagens de impulsionamento).
        Gestor::updateOrCreate(
            ['user_id' => $gestorUser->id],
            [
                'nome' => $gestorUser->name,
                'cpf' => env('GESTOR_CPF', '000.000.000-00'),
                'telefone' => env('GESTOR_TELEFONE', '(11) 90000-0000'),
            ]
        );

        // Firma de demonstração (EC) com seu acesso e token de pagamento.
        $empresa = Empresa::firstOrCreate(
            ['email' => 'firma.demo@cestabasica.test'],
            [
                'nome_fantasia' => 'Firma Demonstração',
                'razao_social' => 'Firma Demonstração LTDA',
                'tipo_documento' => Empresa::TIPO_CNPJ,
                'documento' => '00.000.000/0001-00',
                'telefone' => '(11) 4000-0000',
                'cidade' => 'São Paulo',
                'uf' => 'SP',
                'confrapix_token' => env('CONFRAPIX_TOKEN'),
                'ativo' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => $empresa->email],
            [
                'name' => $empresa->nome_fantasia,
                'password' => 'password',
                'role' => User::ROLE_EMPRESA,
                'empresa_id' => $empresa->id,
            ]
        );

        $this->call(CestaBasicaSeeder::class);
    }
}
