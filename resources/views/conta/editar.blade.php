@extends('layout.principal')

@section('titulo', 'Minha Conta - Locar')

@section('conteudo')

    <h2>Minha Conta</h2>

    @if(session('sucesso'))
        <p style="color: green;">{{ session('sucesso') }}</p>
    @endif

    <div class="form-login">

        <h3>Dados pessoais</h3>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}" required>
            @error('name') <p style="color: red;">{{ $message }}</p> @enderror

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
            @error('email') <p style="color: red;">{{ $message }}</p> @enderror

            <button type="submit">Salvar</button>
        </form>

        <h3>Alterar senha</h3>

        <form action="{{ route('user-password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <label for="current_password">Senha atual:</label>
            <input type="password" id="current_password" name="current_password" required>
            @error('current_password') <p style="color: red;">{{ $message }}</p> @enderror

            <label for="password">Nova senha:</label>
            <input type="password" id="password" name="password" required>
            @error('password') <p style="color: red;">{{ $message }}</p> @enderror

            <label for="password_confirmation">Confirmar nova senha:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>

            <button type="submit">Alterar senha</button>
        </form>

        <h3>Excluir conta</h3>

        @error('user') <p style="color: red;">{{ $message }}</p> @enderror

        <form action="{{ route('profile.destroy') }}" method="POST"
              onsubmit="return confirm('Tem certeza que deseja excluir sua conta?')">
            @csrf
            @method('DELETE')

            <label for="delete_password">Confirme sua senha:</label>
            <input type="password" id="delete_password" name="password" required>
            @error('password', 'excluir') <p style="color: red;">{{ $message }}</p> @enderror

            <button type="submit" class="botao-excluir">Excluir conta</button>
        </form>

    </div>

@endsection
