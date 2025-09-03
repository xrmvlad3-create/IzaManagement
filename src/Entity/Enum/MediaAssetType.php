<?php

namespace App\Entity\Enum;

enum MediaAssetType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case PDF = 'pdf';
}
