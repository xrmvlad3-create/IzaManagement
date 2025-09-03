<?php

namespace App\Entity\Enum;

enum ProtocolStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
