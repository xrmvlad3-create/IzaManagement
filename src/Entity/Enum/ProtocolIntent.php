<?php

namespace App\Entity\Enum;

enum ProtocolIntent: string
{
    case DIAGNOSTIC = 'diagnostic';
    case THERAPEUTIC = 'therapeutic';
}
