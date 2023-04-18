<?php

namespace App\Helpers\Enum;

class Path
{
    const STORAGE = 'storage/';
    const STORAGE_PUBLIC = 'public/';
    const IMAGES = 'images/';
    const INVENTORY_IMAGES = 'inventory/';
    const DOCUMENTS = 'documents/';
    const TMP = 'tmp/';
    const FILES = self::DOCUMENTS.'files/';
    const PACKAGE_SIGNATURES = self::IMAGES.'request-packages-signatures/';
}