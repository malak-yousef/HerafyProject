<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageOptimizerService
{
    public const PROFILE_MAX_WIDTH = 600;

    public const MEDIA_MAX_WIDTH = 1600;

    public const PROFILE_QUALITY = 82;

    public const MEDIA_QUALITY = 85;

    private const WEBP_SOURCES = ['jpg', 'jpeg', 'png', 'webp'];

    public function supportsWebp(): bool
    {
        return extension_loaded('gd') && function_exists('imagewebp');
    }

    public function forProfile(UploadedFile $file): UploadedFile
    {
        return $this->optimize($file, self::PROFILE_MAX_WIDTH, self::PROFILE_QUALITY);
    }

    public function forMedia(UploadedFile $file): UploadedFile
    {
        return $this->optimize($file, self::MEDIA_MAX_WIDTH, self::MEDIA_QUALITY);
    }

    public function optimize(UploadedFile $file, int $maxWidth = self::MEDIA_MAX_WIDTH, int $quality = self::MEDIA_QUALITY): UploadedFile
    {
        $sourceExtension = strtolower($file->getClientOriginalExtension());

        if ($sourceExtension === 'gif') {
            return $file;
        }

        try {
            $image = (new ImageManager(new Driver()))->read($file->getRealPath());
        } catch (\Throwable) {
            return $file;
        }

        $image->scaleDown($maxWidth);

        $useWebp = $this->supportsWebp() && in_array($sourceExtension, self::WEBP_SOURCES, true);

        if ($useWebp) {
            $extension = 'webp';
            $mime = 'image/webp';
            $encoded = $image->toWebp($quality);
        } elseif ($sourceExtension === 'png') {
            $extension = 'png';
            $mime = 'image/png';
            $encoded = $image->toPng();
        } elseif ($sourceExtension === 'webp') {
            $extension = 'webp';
            $mime = 'image/webp';
            $encoded = $image->toWebp($quality);
        } elseif (in_array($sourceExtension, ['jpg', 'jpeg'], true)) {
            $extension = 'jpg';
            $mime = 'image/jpeg';
            $encoded = $image->toJpeg($quality);
        } else {
            return $file;
        }

        $path = tempnam(sys_get_temp_dir(), 'herafy_img_');
        if ($path === false) {
            return $file;
        }

        $path = $path.'.'.$extension;
        if (file_put_contents($path, (string) $encoded) === false) {
            @unlink($path);

            return $file;
        }

        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$extension;

        return new UploadedFile($path, $name, $mime, null, true);
    }
}