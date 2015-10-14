<?php

    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 14.10.2015
     * Time: 9:21
     */
    class Img
    {
        private $_uploadPath;
        private $_imagePath;
        private $_stamp;

        public function __construct($imgPath)
        {
            $this->_uploadPath = __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR;
            $this->_imagePath = $imgPath;
            $this->_stamp = 'stamp.png';
        }

        /**
         * @param $imgName
         *
         * @return bool
         */
        private function _checkOwn($imgName)
        {
            session_start();
            if (array_key_exists('files', $_SESSION) && in_array($imgName, $_SESSION['files'])) {
                return true;
            }

            return false;
        }

        public function getImage()
        {
            $name = basename($this->_imagePath);
            $path = $this->_uploadPath.$name;

            $this->_getHeaders($path);

            if ($this->_checkOwn($name)) {
                $image = $this->addWatermark($path);
            } else {
                $image = @file_get_contents($path);
            }

            if ($image === false) {
                echo "Ошибка получения изображения";
            }
            echo $image;
        }

        /**
         * @param $path
         */
        private function _getHeaders($path)
        {
            //http заголовки
            switch (exif_imagetype($path)) {
                case IMAGETYPE_JPEG:
                    header("Content-Type: image/jpeg");
                    break;
                case IMAGETYPE_PNG:
                    header("Content-Type: image/png");
                    break;
                case IMAGETYPE_GIF:
                    header("Content-Type: image/gif");
                    break;
                case IMAGETYPE_BMP:
                    header("Content-Type: image/bmp");
                    break;
                //...
            }
            header("Content-Length: ".(string)(filesize($path)));
        }

        /**
         * @param $path
         * @param null $pngStamp
         *
         * @return bool
         */
        public function addWatermark($path, $pngStamp = null)
        {
            if ($pngStamp === null) {
                $pngStamp = $this->_stamp;
            }
            $pngStamp = @imagecreatefrompng($pngStamp);
            if ($pngStamp === false) {
                return false;
            }

            //получаем расширение файла
            switch (exif_imagetype($path)) {
                case IMAGETYPE_BMP:
                    $image = @imagecreatefromwbmp($path);
                    break;
                case IMAGETYPE_JPEG:
                    $image = @imagecreatefromjpeg($path);
                    break;
                case IMAGETYPE_GIF:
                    $image = @imagecreatefromgif($path);
                    break;
                case IMAGETYPE_PNG:
                    $image = @imagecreatefrompng($path);
                    break;
                //..
                default:
                    $image = false;
                    break;
            }

            //в случае ошибок возвращаем false
            if ($image === false) {
                return false;
            }

            // Получение высоты и ширины ватермарка
            $marge_right = 50;
            $marge_bottom = 50;
            $sx = imagesx($pngStamp);
            $sy = imagesy($pngStamp);

            //добавляем ватермарк
            imagecopy(
                $image,
                $pngStamp,
                imagesx($image) - $sx - $marge_right,
                imagesy($image) - $sy - $marge_bottom,
                0,
                0,
                $sx,
                $sy
            );

            $img = @imagepng($image);
            //освобождаем память
            imagedestroy($image);

            return $img;
        }

    }