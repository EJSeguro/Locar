<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Gestor = 'gestor';
    case Cliente = 'cliente';
}
