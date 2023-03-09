<?php

namespace App\Helpers;

use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Box\Spout\Writer\Style\StyleBuilder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class File
{
    const INVENTORY_HEIGHT_IMAGE = 512;
    const SIGNATURE_HEIGHT_IMAGE = 200;
    const IMAGE_NAME_LENGHT = 30;
    const FILE_NAME_LENGHT = 30;

    /**
     * @throws CustomErrorException
     */
    public static function uploadImage(UploadedFile $imageFile, string $customPath, int $sizeHeight): string
    {
        try {
            $imageName = self::getFilename(self::IMAGE_NAME_LENGHT) .
                self::getFileExtension($imageFile->getClientOriginalName());

            $pathUrl = self::getFilePublicPath($customPath, $imageName);

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

    public static function uploadFile(UploadedFile $file, string $customPath): string
    {
        $filename = self::getFilename(self::FILE_NAME_LENGHT).self::getFileExtension($file->getClientOriginalName());
        $pathUrl = self::getFilePublicPath($customPath);
        $file->move($pathUrl, $filename);
        return $filename;
    }

    public static function generatePDF(string $view, array $data, string $filename = 'download', bool $isLandscape = false)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView($view, $data);
        if ($isLandscape) {
            $pdf->setPaper('a4', 'landscape');
        }
        return $pdf->download("$filename.pdf");
    }

    /**
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     * @throws InvalidArgumentException
     * @throws IOException
     */
    public static function generateExcel($data, string $filename = 'download'): StreamedResponse
    {
        $headerStyle = (new StyleBuilder())
            ->setFontSize(14)
            ->setFontBold()
            ->build();

        return (new FastExcel($data))
            ->headerStyle($headerStyle)
            ->download("$filename.xlsx");
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

    public static function getFilePublicPath(string $path, string $filename = null): string
    {
        if (is_null($filename)) {
            return public_path(Path::STORAGE . $path);
        }
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

    public static function getFilename(int $lenght): string
    {
        $random = Str::random($lenght);
        $timestamp = now()->getTimestamp();
        return "{$random}_$timestamp";
    }
}