<?php

namespace App\Enums;

enum EmailAuthMethods: string
{
    case API_KEY = 'API_KEY';
    case NONE = 'NONE';
}
