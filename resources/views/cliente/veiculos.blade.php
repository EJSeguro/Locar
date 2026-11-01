@extends('layout.principal')

@section('titulo', 'Veículos - Locar')

@section('conteudo')

    <h2>Veículos disponíveis</h2>

    <p>Confira os veículos disponíveis para locação.</p>

    <div class="lista-veiculos">

        <div class="veiculo">
            <h3>Chevrolet Onix</h3>
            <p>Ano: 2024</p>
            <p>Categoria: Econômico</p>
            <p>Diária: R$ 120,00</p>
            <button>Ver detalhes</button>
        </div>

        <div class="veiculo">
            <h3>Hyundai HB20</h3>
            <p>Ano: 2023</p>
            <p>Categoria: Econômico</p>
            <p>Diária: R$ 130,00</p>
            <button>Ver detalhes</button>
        </div>

        <div class="veiculo">
            <h3>Jeep Compass</h3>
            <p>Ano: 2024</p>
            <p>Categoria: SUV</p>
            <p>Diária: R$ 250,00</p>
            <button>Ver detalhes</button>
        </div>

    </div>

@endsection
