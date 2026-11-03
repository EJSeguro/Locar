@extends('layout.principal')

@section('titulo', 'Redefinir senha - Locar')

@section('conteudo')

    <h2>Redefinir senha</h2>

    <div class="form-login">

        @if($errors->any())
            <ul style="color: red;">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required>

            <label for="password">Nova senha:</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirmation">Confirmar nova senha:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>

            <button type="submit">Redefinir senha</button>
        </form>

    </div>

@endsection
