<?php

namespace App\Helpers\Enum;

class Path
{
    const STORAGE = 'storage/';
    const STORAGE_PUBLIC = 'public/';
    const IMAGES = 'images/';
    const INVENTORY_IMAGES = 'inventory/';
    const DOCUMENTS = 'documents/';
    const PACKAGE_AUTHORIZATION_DOCUMENTS = self::DOCUMENTS.'packages/';
    const DRIVER_AUTHORIZATION_DOCUMENTS = self::DOCUMENTS.'driver-request/';
    const CAR_AUTHORIZATION_DOCUMENTS = self::DOCUMENTS.'car-request/';
    const CAR_RESPONSIVE_FILE = self::DOCUMENTS.'car-request/responsive/';
    const PACKAGE_SIGNATURES = self::IMAGES.'request-packages-signatures/';
    const REQUEST_CAR_IMAGES = self::IMAGES.'request-car-images/';
}