<?php

namespace App\Entity\Enum;

enum CourseStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
