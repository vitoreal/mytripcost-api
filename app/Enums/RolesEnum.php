<?php

namespace App\Enums;

enum RolesEnum:String {
    case USER_ROOT = 'USER_ROOT';
    case USER_ADMIN = 'USER_ADMIN';
    case USER_BASICO = 'USER_BASICO';
    case USER_AVANCADO = 'USER_AVANCADO';
}
