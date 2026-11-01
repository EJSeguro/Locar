
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Locar')</title>
    <link rel="stylesheet" href="{{ asset('css/estilo.css') }}">
</head>
<body>
    <header>
        <h1>LOCAR</h1>
        <p>Sistema de Locação de Veículos</p>

        <nav>
            <ul>
                <a href="{{url('/teste-blade') }}">Início</a>
                <a href="{{ route('veiculos') }}">Veículos</a>
                <a href="{{ url('/teste-locacoes') }}">Minhas Locações</a>
                <a href="{{ url('/teste-login') }}">Login</a>
                <a href="{{ url('/teste-cadastro') }}">Cadastro</a>
            </ul>
        </nav>
    </header>

    <main>
        @yield('conteudo')
    </main>

    <footer>
        <p>Locar - Sistema de Locação de Veículos</p>
    </footer>

</body>
</html>
