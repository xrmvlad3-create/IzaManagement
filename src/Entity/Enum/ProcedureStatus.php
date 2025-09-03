<?php

namespace App\Entity\Enum;

enum ProcedureStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
