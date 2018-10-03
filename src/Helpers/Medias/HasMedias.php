<?php
namespace Piclou\Piclommerce\Helpers\Medias;

use Ramsey\Uuid\Uuid;

trait HasMedias
{
    /**
     * @param string $key
     * @return array|null|string
     */
    public function getMedias(string $key)
    {
        $medias = json_decode($this->getAttributes()[$key] ?? '' ?: '{}', true) ?: [];
        return $medias;

    }

    /**
     * @param string $key
     * @param bool $width
     * @param bool $height
     * @param string $direction
     * @return array|null|string
     */
    public function resizeImage(string $key, $width = false, $height = false, string $direction = 'center')
    {
        $medias = $this->getMedias($key);
        if(!$medias) {
            return [
                "uuid" => Uuid::uuid4()->toString(),
                "target_path" => '/'. config('piclommerce.imageNotFound'),
                "file_name" => "",
                "file_type" => "",
                "alt" => "",
                "description" => ""
            ];
        }
        $img = $medias['target_path'];
        $dir = config('piclommerce.imageCacheFolder');
        $infos = pathinfo($img);
        $fileName = $infos['filename'];
        $extension = $infos['extension'];
        $dir .= '/' . $infos['dirname'];

        if (!file_exists($infos['dirname']. '/' . $fileName . "." .  $infos['extension'])) {
            return [
                "uuid" => Uuid::uuid4()->toString(),
                "target_path" => '/'. config('piclommerce.imageNotFound'),
                "file_name" => "",
                "file_type" => "",
                "alt" => "",
                "description" => ""
            ];
        }
        if(!file_exists($dir)){
            if(!mkdir($dir,0770, true)){
                dd(__("piclommerce::admin.directory_create_error") . $dir);
            }
        }

        if ($width && $height) {
            $cacheResize = "_".$width."_".$height;
        } elseif ($width && !$height) {
            $cacheResize = "_".$width;
        } else {
            $cacheResize = "_".$height;
        }

        if (file_exists(public_path() . "/" . $dir . "/" . $fileName.$cacheResize.".".$extension)) {
            $medias['target_path'] = "/" . $dir. "/" . $fileName.$cacheResize.".".$extension;
        } else {
            $manager = new \Intervention\Image\ImageManager(['drive' => 'gd']);
            $image = $manager->make($img);
            if ($width && $height) {
                $image->fit($width, $height, function () {
                }, $direction);
            } elseif ($width && !$height) {
                $image->fit($width, null, function () {
                }, $direction);
            } else {
                $image->resize(null, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            $image->save(
                $dir . "/" . $fileName.$cacheResize.".".$extension,
                config('ikCommerce.imageQuality')
            );
            $medias['target_path'] = "/" . $dir. "/" . $fileName.$cacheResize.".".$extension;
        }

        return $medias;
    }

    /**
     * @param string $key
     * @param string $directory
     * @param $file
     * @return self
     */
    public function uploadImage(string $key, string $directory, $file): self
    {
        $directory = str_replace('\\', '/',$directory);

        $dir = config('piclommerce.fileUploadFolder') . DIRECTORY_SEPARATOR .$directory;
        if(!file_exists($dir)){
            if(!mkdir($dir,0770, true)){
                dd(__("piclommerce::admin.directory_create_error") .$dir);
            }
        }
        $fileName = $file->getClientOriginalName();
        $extension = $this->getExtension($fileName);

        $fileNewName = time().str_slug(str_replace(".".$extension,"",$fileName)).".".strtolower($extension);
        $file->move($dir,$fileNewName);
        $targetPath = str_replace("\\", "/",$dir. "/" . $fileNewName);

        $imageManager =  new \Intervention\Image\ImageManager();
        $img = $imageManager->make($targetPath);
        $width = $img->width();
        if ($width > config('piclommerce.imageMaxWidth')) {
            $img->resize( config('piclommerce.imageMaxWidth'), null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($targetPath, config('piclommerce.imageQuality'));
        }
        $media = [
            "uuid" => Uuid::uuid4()->toString(),
            "target_path" => $targetPath,
            "file_name" => $fileNewName,
            "file_type" => $extension,
            "alt" => str_replace("." . $extension, "", $fileName),
            "description" => '',
        ];

        $this->attributes[$key] = json_encode($media);

        return $this;
    }

    public function uploadMultipleImages(string $key, string $directory, $files): self
    {
        $directory = str_replace('\\', '/',$directory);

        $dir = config('piclommerce.fileUploadFolder') . DIRECTORY_SEPARATOR .$directory;
        if(!file_exists($dir)){
            if(!mkdir($dir,0770, true)){
                dd(__("piclommerce::admin.directory_create_error") .$dir);
            }
        }
        $medias = json_decode($this->getAttributes()[$key] ?? '' ?: '{}', true) ?: [];
        foreach($files as $file) {
            $fileName = $file->getClientOriginalName();
            $extension = $this->getExtension($fileName);

            $fileNewName = time().str_slug(str_replace(".".$extension,"",$fileName)).".".strtolower($extension);
            $file->move($dir,$fileNewName);
            $targetPath = str_replace("\\", "/",$dir. "/" . $fileNewName);

            $imageManager =  new \Intervention\Image\ImageManager();
            $img = $imageManager->make($targetPath);
            $width = $img->width();
            if ($width > config('piclommerce.imageMaxWidth')) {
                $img->resize( config('piclommerce.imageMaxWidth'), null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($targetPath, config('piclommerce.imageQuality'));
            }
            $medias[] = [
                "uuid" => Uuid::uuid4()->toString(),
                "target_path" => $targetPath,
                "file_name" => $fileNewName,
                "file_type" => $extension,
                "alt" => str_replace("." . $extension, "", $fileName),
                "description" => '',
            ];
        }
        $this->attributes[$key] = json_encode($medias);

        return $this;

    }

    /**
     * @param string $key
     * @param string $directory
     * @param $file
     * @return self
     */
    public function uploadFile(string $key, string $directory, $file): self
    {
        $directory = str_replace("\\","/",$directory);
        $file = str_replace("\\","/",$file);
        $dir = config('piclommerce.fileUploadFolder') . "/" .$directory;
        if(!file_exists($dir)){
            if(!mkdir($dir,0770, true)){
                dd(__("piclommerce::admin.directory_create_error") .$dir);
            }
        }
        $fileName = $file->getClientOriginalName();
        $extension = getExtension($fileName);

        $fileNewName = time().str_slug(str_replace(".".$extension,"",$fileName)).".".strtolower($extension);
        $file->move($dir,$fileNewName);
        $targetPath = str_replace("\\", "/",$dir. "/" . $fileNewName);

        $media = [
            "uuid" => Uuid::uuid4()->toString(),
            "target_path" => $targetPath,
            "file_name" => $fileNewName,
            "file_type" => $extension,
            "alt" => str_replace("." . $extension, "", $fileName),
            "description" => '',
        ];

        $this->attributes[$key] = json_encode($media);

        return $this;
    }

    public function deleteFile(string $key)
    {
        $medias = $this->getMedias($key);
        if($medias) {
            if(file_exists($medias['target_path'])){
                unlink($medias['target_path']);
            }
        }
        return null;
    }

    /**
     * Return extension of file
     * @param string $str
     * @return string
     */
    private function getExtension(string $str): string
    {
        $i = strrpos($str, ".");
        if(!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        return substr($str, $i+1, $l);
    }
}