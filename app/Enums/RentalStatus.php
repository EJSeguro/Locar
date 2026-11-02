<?php

namespace App\Enums;

enum RentalStatus: string
{
    case Ativa = 'ativa';
    case Finalizada = 'finalizada';
    case Cancelada = 'cancelada';
}
