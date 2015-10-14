<?php

    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 13.10.2015
     * Time: 20:26
     */
    class Uploader
    {
        private $_maxImageSize;
        private $_destination;
        private $_errors = array();

        public function __construct()
        {
            //���� ��� ��������
            $destination = __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR;
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            $this->_destination = $destination;

            //������������ ������ �������� � ������
            $this->_maxImageSize = 2 * 1024 * 1024;
        }

        /**
         * @param int $size
         */
        public function setMaxSize($size)
        {
            $this->_maxImageSize = (int)$size;
        }

        /**
         * @param $file
         *
         * @return bool|string
         */
        public function upload($file)
        {
            if ($this->_checkFile($file)) {
                //�������� ��������� ����
                $name = date('Ymd_His')."_".$file['name'];
                if (move_uploaded_file($file['tmp_name'], $this->_destination.$name)) {
                    chmod($this->_destination.$name, 0777);

                    return array(
                        "upload_path" => $this->_destination.$name,
                        "name"        => $name,
                    );
                }
            }

            return false;
        }

        /**
         * @param array $file
         *
         * @return bool
         */
        private function _checkFile($file)
        {
            //������ �� ����
            if (!is_uploaded_file($file['tmp_name'])) {
                $this->_errors[] = "���� �� ��������.";

                return false;
            }
            //��������� ��� �����. ��������� ������ �����������.
            $size = getimagesize($file['tmp_name']);
            if ($size === false) {
                $this->_errors[] = "�������� ��� �����. ��������� ������ �����������.";

                return false;
            }
            //�������� �� ������. �������� 1��
            if ($file['size'] > $this->_maxImageSize) {
                $this->_errors[] = "�������� ������ �����������. ����������� 1 ��.";

                return false;
            }

            return true;
        }

        /**
         * @return array
         */
        public function getErrors()
        {
            return $this->_errors;
        }

        /**
         * @return array
         */
        public function getListUploads()
        {
            if ($files = @scandir($this->_destination)) {
                $files = array_diff($files, array('.', '..'));
                $paths = array();
                foreach ($files as $file) {
                    $paths[$file] = "/uploads/".$file;
                }

                return $paths;
            }

            return array();
        }
    }