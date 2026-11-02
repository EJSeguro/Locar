@extends('layout.principal')

@section('titulo', 'Cadastro - Locar')

@section('conteudo')

    <h2>Cadastro de Cliente</h2>

    <p>Preencha os dados abaixo para criar sua conta.</p>

    <div class="form-cadastro">
        
            <form action="{{ route('cadastro.salvar') }}" method="POST">
                @csrf

                <label for="name">Nome completo:</label>
                <input type="text" id="name" name="name"
                    placeholder="Digite seu nome" required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email"
                    placeholder="Digite seu e-mail" required>

                <label for="password">Senha:</label>
                <input type="password" id="password" name="password"
                    placeholder="Digite sua senha" required>

                <label for="password_confirmation">Confirmar senha:</label>
                <input type="password" id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Confirme sua senha" required>

                <button type="submit">Cadastrar</button>
            </form>


        <p>Já possui uma conta? <a href="{{ route('login') }}">Faça login</a></p>
    </div>

@endsection
