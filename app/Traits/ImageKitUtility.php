<?php

namespace App\Traits;

use Exception;
use Intervention\Image\Laravel\Facades\Image;
use ImageKit\ImageKit;

trait ImageKitUtility
{
    //Upload to imagekit
    protected function uploadToImageKit($payload, $fileName, $folder, $height, $width, $isPath = null)
    {
        //if $isPath is true image is fetched from local storage (public)
        //else image is fetched from form then converted to base 64
        $img = $isPath ? $payload : $this->toBase64($payload, $width, $height);

        $toImageKit = $this->init();
        return $toImageKit->upload([
            'file' => $img,
            'fileName' => $fileName,
            'folder' => $folder,
        ]);
    }

    //convert image to base64
    private function toBase64($file, $width, $height)
    {
        try {
            if ($width && $height) {
                // Process and resize image
                $image = Image::read($file)
                    ->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encodeByExtension('png', 100);
            } else {
                // Just process without resizing
                $image = Image::read($file)
                    ->encodeByExtension('png', 100);
            }

            // Convert to base64
            return 'data:image/png;base64,' . base64_encode($image);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Image conversion error: ' . $e->getMessage());
            throw $e;
        }
    }

    //Delete remote image
    protected function deleteImage($fileId)
    {
        try {
            $toImageKit = $this->init();
            return $toImageKit->deleteFile($fileId);
        } catch (Exception $e) {
            return $e;
        }
    }
    //initialize remote ImageKit bucket
    private function init()
    {
        return new ImageKit(
            env('IMAGE_KIT_PUBLIC'),
            env('IMAGE_KIT_PRIVATE'),
            env('IMAGE_KIT_URL')
        );
    }
}
