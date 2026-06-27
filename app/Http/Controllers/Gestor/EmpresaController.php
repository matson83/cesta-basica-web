<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use App\Notifications\BoasVindasDefinirSenha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class EmpresaController extends Controller
{
    public function index(Request $request): View
    {
        $empresas = Empresa::query()
            ->withCount(['produtos', 'users'])
            ->when($request->string('busca')->toString(), function ($query, string $busca) {
                $query->where('nome_fantasia', 'like', "%{$busca}%")
                    ->orWhere('razao_social', 'like', "%{$busca}%")
                    ->orWhere('documento', 'like', "%{$busca}%");
            })
            ->orderBy('nome_fantasia')
            ->paginate(10)
            ->withQueryString();

        return view('pages.gestor.empresas.index', compact('empresas'));
    }

    public function create(): View
    {
        return view('pages.gestor.empresas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        [$empresa, $user, $token] = DB::transaction(function () use ($data, $request) {
            $empresa = Empresa::create([
                'nome_fantasia' => $data['nome_fantasia'],
                'razao_social' => $data['razao_social'] ?? null,
                'tipo_documento' => $data['tipo_documento'],
                'documento' => $data['documento'] ?? null,
                'email' => $data['email'],
                'telefone' => $data['telefone'] ?? null,
                'bairro' => $data['bairro'] ?? null,
                'cidade' => $data['cidade'] ?? null,
                'uf' => $data['uf'] ?? null,
                'endereco' => $data['endereco'] ?? null,
                'confrapix_token' => $data['confrapix_token'] ?? null,
                'confrapix_base_url' => $data['confrapix_base_url'] ?? null,
                'ativo' => $request->boolean('ativo', true),
            ]);

            $user = User::create([
                'name' => $empresa->nome_fantasia,
                'email' => $empresa->email,
                'password' => Str::password(32),
                'role' => User::ROLE_EMPRESA,
                'empresa_id' => $empresa->id,
            ]);

            // Gera o token uma única vez para reaproveitar no e-mail e na tela de confirmação.
            $token = Password::createToken($user);

            return [$empresa, $user, $token];
        });

        $emailBoasVindasEnviado = true;

        try {
            $user->notify(new BoasVindasDefinirSenha($empresa->nome_fantasia, $token));
        } catch (Throwable $e) {
            $emailBoasVindasEnviado = false;

            Log::warning('Falha ao enviar e-mail de boas-vindas para firma.', [
                'empresa_id' => $empresa->id,
                'email' => $user->email,
                'erro' => $e->getMessage(),
            ]);
        }

        $linkDefinirSenha = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        return redirect()
            ->route('gestor.empresas.criada', $empresa)
            ->with('link_definir_senha', $linkDefinirSenha)
            ->with('email_boas_vindas_enviado', $emailBoasVindasEnviado);
    }

    public function criada(Empresa $empresa): View|RedirectResponse
    {
        $link = session('link_definir_senha');

        // Acesso direto à URL (sem ter acabado de cadastrar) volta para os detalhes.
        if (! $link) {
            return redirect()->route('gestor.empresas.show', $empresa);
        }

        return view('pages.gestor.empresas.criada', [
            'empresa' => $empresa,
            'linkDefinirSenha' => $link,
            'emailBoasVindasEnviado' => (bool) session('email_boas_vindas_enviado', true),
        ]);
    }

    public function show(Empresa $empresa): View
    {
        $empresa->loadCount(['produtos', 'cestas', 'distribuicoes', 'users']);

        return view('pages.gestor.empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa): View
    {
        return view('pages.gestor.empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa): RedirectResponse
    {
        $data = $this->validateData($request, $empresa);

        $payload = [
            'nome_fantasia' => $data['nome_fantasia'],
            'razao_social' => $data['razao_social'] ?? null,
            'tipo_documento' => $data['tipo_documento'],
            'documento' => $data['documento'] ?? null,
            'email' => $data['email'],
            'telefone' => $data['telefone'] ?? null,
            'bairro' => $data['bairro'] ?? null,
            'cidade' => $data['cidade'] ?? null,
            'uf' => $data['uf'] ?? null,
            'endereco' => $data['endereco'] ?? null,
            'confrapix_base_url' => $data['confrapix_base_url'] ?? null,
            'ativo' => $request->boolean('ativo', true),
        ];

        // Só atualiza o token quando um novo valor é informado (evita apagar o existente).
        if (filled($data['confrapix_token'] ?? null)) {
            $payload['confrapix_token'] = $data['confrapix_token'];
        }

        $empresa->update($payload);

        // Mantém o login da firma alinhado ao cadastro.
        $empresa->users()->update([
            'name' => $empresa->nome_fantasia,
            'email' => $empresa->email,
        ]);

        return redirect()
            ->route('gestor.empresas.index')
            ->with('status', "Firma \"{$empresa->nome_fantasia}\" atualizada com sucesso.");
    }

    public function destroy(Empresa $empresa): RedirectResponse
    {
        $empresa->delete();

        return redirect()
            ->route('gestor.empresas.index')
            ->with('status', 'Firma removida com sucesso.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request, ?Empresa $empresa = null): array
    {
        $tipoDocumento = $request->input('tipo_documento', Empresa::TIPO_CNPJ);

        return $request->validate([
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'razao_social' => ['nullable', 'string', 'max:255'],
            'tipo_documento' => ['required', Rule::in(array_keys(Empresa::tiposDocumento()))],
            'documento' => [
                'nullable',
                'string',
                'max:18',
                function (string $attribute, mixed $value, \Closure $fail) use ($tipoDocumento) {
                    $digitos = preg_replace('/\D/', '', (string) $value);
                    $esperado = $tipoDocumento === Empresa::TIPO_CPF ? 11 : 14;

                    if (strlen($digitos) !== $esperado) {
                        $rotulo = $tipoDocumento === Empresa::TIPO_CPF ? 'CPF' : 'CNPJ';
                        $fail("O {$rotulo} informado é inválido.");
                    }
                },
                Rule::unique('empresas', 'documento')->ignore($empresa?->id),
            ],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($empresa?->users()->value('id'), 'id')],
            'telefone' => ['nullable', 'string', 'max:20'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'uf' => ['nullable', 'string', 'size:2'],
            'endereco' => ['nullable', 'string'],
            'confrapix_token' => [$empresa ? 'nullable' : 'required', 'string', 'max:255'],
            'confrapix_base_url' => ['nullable', 'url', 'max:255'],
            'ativo' => ['boolean'],
        ]);
    }
}
