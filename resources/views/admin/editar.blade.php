@extends('layout.principal')

@section('titulo', 'Editar Usuário - Locar')

@section('conteudo')

    <h2>Editar Usuário</h2>

    <p>Altere os dados do usuário cadastrado.</p>

    <div class="form-login">

        @if($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.usuarios.atualizar', $usuario->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="name">Nome:</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name', $usuario->name) }}" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email', $usuario->email) }}" required>

            <label for="role">Nível de Acesso:</label>
            <select id="role" name="role" required>
                <option value="admin" @selected(old('role', $usuario->role->value) === 'admin')>
                    Administrador
                </option>

                <option value="gestor" @selected(old('role', $usuario->role->value) === 'gestor')>
                    Gestor
                </option>

                <option value="cliente" @selected(old('role', $usuario->role->value) === 'cliente')>
                    Cliente
                </option>
            </select>

            <button type="submit">Salvar Alterações</button>
        </form>

        <p>
            <a href="{{ route('admin.usuarios') }}">
                Voltar para Usuários
            </a>
        </p>

    </div>

@endsection