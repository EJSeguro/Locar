@extends('layout.principal')

@section('titulo', 'Painel Administrativo - Locar')

@section('conteudo')

    <h2>Painel do Administrador</h2>

    <p>Bem-vindo à área administrativa do Locar.</p>

    <div class="painel-admin">

        <div class="card-admin">
            <h3>Usuários</h3>
            <p>Gerencie os usuários cadastrados no sistema.</p>
            <a href="{{ url('/teste-admin-usuarios') }}" class="botao-cadastrar">Gerenciar Usuários</a>
        </div>

        <div class="card-admin">
            <h3>Veículos</h3>
            <p>Consulte os veículos cadastrados no sistema.</p>
            <a href="{{ url('/teste-gestor-veiculos') }}" class="botao-cadastrar">
                Ver Veículos
            </a>
        </div>

        <div class="card-admin">
            <h3>Locações</h3>
            <p>Acompanhe as locações realizadas.</p>
            <a href="{{ url('/teste-gestor-locacoes') }}" class="botao-cadastrar">
                Ver Locações
            </a>
        </div>

    </div>

@endsection
