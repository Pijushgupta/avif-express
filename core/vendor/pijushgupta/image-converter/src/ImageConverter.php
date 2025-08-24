<?php

namespace PijushGupta\ImageConverter;


class ImageConverter
{

    private ?string $source = null;
    private ?string $destination = null;
    private int $quality = 70;
    private int $speed = 6;
    private string $toFormat = 'avif';
    private string $driver = 'imagick';

    public function __construct(string $source = '', string $destination = '')
    {
        if (!empty($source)) $this->setSource($source);
        if (!empty($destination)) $this->setDestination($destination);
    }

    public function setSource(string $source = ''):self
    {
        if (!is_file($source)) {
            throw new \InvalidArgumentException("Source file '{$source}' does not exist.");
        }
        if (!$this->isImage($source)) {
            throw new \InvalidArgumentException("Source file '{$source}' is not a supported image file.");
        }

        $this->source = $source;
        return $this;
    }

    public function setDestination(string $destination = ''):self
    {
        if ($destination == '') {
            throw new \InvalidArgumentException('Destination is not provided');
        }

        $this->validateDestinationDirectory($destination);


        $this->destination = $destination;
        return $this;
    }

    public function setQuality(int $quality = 70):self
    {
        if ($quality < 0 || $quality > 100) {
            throw new \InvalidArgumentException('Quality must be between 0 - 100');
        }

        $this->quality = $quality;
        return $this;
    }


    public function setSpeed(int $speed = 6):self
    {
        if ($speed < 0 || $speed > 10) {
            throw new \InvalidArgumentException('Speed must be between 0 - 9');
        }

        $this->speed = $speed;
        return $this;
    }

    public function setFormat(string $toFormat = 'avif'):self
    {
        if (!in_array(strtolower($toFormat), ['avif', 'webp'])) {
            throw new \InvalidArgumentException('Format must be "avif" or "webp"');
        }
        $this->toFormat = $toFormat;
        return $this;
    }

    public function setDriver(string $driver = 'imagick'):self
    {
        if (!in_array(strtolower($driver), ['imagick', 'gd'])) {
            throw new \InvalidArgumentException('Driver must be "gd" or "imagick"');
        }

        $this->driver = $driver;
        return $this;
    }


    public function convert():bool
    {
        if(is_null($this->source)) return false;
        
        if ($this->driver == 'gd') {
            return $this->convertGD();
        }

        if ($this->driver == 'imagick') {
            return $this->convertImagick();
        }

        return false;
    }

    private function convertImagick():bool
    {
        if (!extension_loaded('imagick')) {
            throw new \RuntimeException('Imagick extension is not loaded.');
        }

        $imagick = new \Imagick();
        $formats = $imagick->queryFormats();
        if (!in_array(strtoupper($this->toFormat), $formats)) return false;
        try {
            $imagick->readImage($this->source);
            $imagick->setImageFormat(strtolower($this->toFormat));
            $imagick->setImageCompressionQuality($this->quality);
            $imagick->writeImage($this->getDestination());
        } catch (\ImagickException $e) {
            throw new \RuntimeException("Imagick conversion failed: " . $e->getMessage());
        }
        return true;
    }


    private function convertGD():bool
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('GD extension is not loaded.');
        }
        if ($this->toFormat === 'webp' && !function_exists('imagewebp')) {
            throw new \RuntimeException('GD library is not compiled with WebP support.');
        }
        if ($this->toFormat === 'avif' && !function_exists('imageavif')) {
            throw new \RuntimeException('GD library is not compiled with AVIF support.');
        }

        $fileType = getimagesize($this->source);
        if (!$fileType) {
            throw new \RuntimeException('Unable to get image size from source file.');
        }

        $fileType = $fileType['mime'];

        switch ($fileType) {
            case 'image/jpeg':
            case 'image/jpg':
                $sourceGDImg = imagecreatefromjpeg($this->source);
                break;
            case 'image/png':
                $sourceGDImg = imagecreatefrompng($this->source);
                break;
            case 'image/gif':
                $sourceGDImg = imagecreatefromgif($this->source);
                break;
            case 'image/webp':
                $sourceGDImg = imagecreatefromwebp($this->source);
                break;
            default:
                throw new \RuntimeException('Unsupported image type for GD conversion: ' . $fileType);
        }


        if ($sourceGDImg === false) {
            throw new \RuntimeException('Failed to create GD image resource');
        }
        $destination = $this->getDestination();
        $success = false;
        if ($this->toFormat === 'avif') {
            $success = imageavif($sourceGDImg, $destination, $this->quality, $this->speed);
        } elseif ($this->toFormat === 'webp') {
            $success = imagewebp($sourceGDImg, $destination, $this->quality);
        }
        
        //its a hack, for know gd bug - to be removed in future  
        if (filesize($this->destination) % 2 == 1) {
            file_put_contents($this->destination, "\0", FILE_APPEND);
        }
        //end of hack

        imagedestroy($sourceGDImg);
        if (!$success) {
            throw new \RuntimeException("Failed to convert image using GD to '{$this->toFormat}' format.");
        }
        return true;
    }

    private function getDestination():string
    {
        if (isset($this->destination)) {
            return $this->destination;
        }

        if(!isset($this->source)){
            throw new \RuntimeException('Cannot generate a destination path: The source file has not been set.');
        }

        $pathInfo = pathinfo($this->source);
        $this->destination = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.' . $this->toFormat;
        return $this->destination;
    }

    private function  isImage(string $file): bool
    {
        if (!is_file($file)) {
            return false;
        }

        if (extension_loaded('fileinfo')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo === false) {
                return (bool) getimagesize($file);
            }
            $mime  = finfo_file($finfo, $file);
            finfo_close($finfo);
            return str_starts_with($mime, 'image/');
        }

        // fallback
        return (bool) getimagesize($file);
    }

    private function validateDestinationDirectory(string $path): void
    {
        $dir = dirname($path);

        // Check if the directory exists and if not, try to create it.
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new \RuntimeException("Could not create destination directory '{$dir}'.");
            }
        }

        // After ensuring the directory exists, check if it's writable.
        if (!is_writable($dir)) {
            throw new \RuntimeException("Destination directory '{$dir}' is not writable.");
        }
    }
}
