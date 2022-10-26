<?php

namespace App\Helpers;

use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;

class File
{
    const INVENTORY_HEIGHT_IMAGE = 512;
    const IMAGE_NAME_LENGHT = 40;

    /**
     * @throws CustomErrorException
     */
    public static function uploadImage(UploadedFile $imageFile, string $customPath, int $sizeHeight): string
    {
        try {
            $imageName = Str::random(self::IMAGE_NAME_LENGHT) .
                self::getFileExtension($imageFile->getClientOriginalName());

            $pathUrl = self::getFilePublicPath($imageName, $customPath);

            Image::make($imageFile)
                ->resize(null, $sizeHeight, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($pathUrl);

            return $imageName;
        } catch (\Exception $e) {
            throw new CustomErrorException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public static function generatePDF(string $view, array $data, string $filename = 'download.pdf', bool $isLandscape = false)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView($view, $data);
        if ($isLandscape) {
            $pdf->setPaper('a4', 'landscape');
        }
        return $pdf->download($filename);
    }

    /**
     * @param string $filename
     * @param string $path
     * @return void
     */
    public static function deleteFile(string $filename, string $path)
    {
        Storage::delete(self::getFileStoragePath($filename, $path));
    }

    public static function getFileExtension(string $file): string
    {
        return '.' . pathinfo($file, PATHINFO_EXTENSION);
    }

    public static function getFilePublicPath(string $filename, string $path): string
    {
        return public_path(Path::STORAGE . $path . $filename);
    }

    public static function getFileStoragePath(string $filename, string $path): string
    {
        return Path::STORAGE_PUBLIC . $path . $filename;
    }

    public static function getExposedPath(string $filename, string $path): string
    {
        return Path::STORAGE . $path . $filename;
    }
}