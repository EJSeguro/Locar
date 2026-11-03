@extends('layout.principal')

@section('titulo', 'Esqueci minha senha - Locar')

@section('conteudo')

    <h2>Esqueci minha senha</h2>

    <p>Informe seu e-mail para receber o link de redefinição de senha.</p>

    <div class="form-login">

        @if(session('status'))
            <p style="color: green;">{{ session('status') }}</p>
        @endif

        @error('email')
            <p style="color: red;">{{ $message }}</p>
        @enderror

        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>

            <button type="submit">Enviar link</button>
        </form>

        <p><a href="{{ route('login') }}">Voltar para o login</a></p>

    </div>

@endsection
