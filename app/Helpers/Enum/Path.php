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
    const REQUEST_CAR = 'car-request/';
    const PACKAGE_AUTHORIZATION_DOCUMENTS = self::DOCUMENTS.'packages/';
    const DRIVER_AUTHORIZATION_DOCUMENTS = self::DOCUMENTS.'driver-request/';
    const REQUEST_CAR_BILL_ZIP = self::DOCUMENTS.self::REQUEST_CAR.'bill-zip/';
    const PACKAGE_SIGNATURES = self::IMAGES.'request-packages-signatures/';
    const REQUEST_CAR_IMAGES = self::IMAGES.'request-car-images/';
}