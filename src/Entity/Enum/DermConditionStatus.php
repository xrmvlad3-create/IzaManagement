<?php

namespace App\Entity\Enum;

enum DermConditionStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
