<?php

    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 15.10.2015
     * Time: 9:47
     */
    class Encryption
    {
        private $_key;
        private $_iv;
        private $_cipher;
        private $_mcrypt_mode;

        public function __construct($key, $cipher = MCRYPT_RIJNDAEL_128, $mcrypt_mode = MCRYPT_MODE_ECB)
        {
            $this->_key = $key;
            $this->_cipher = $cipher;
            $this->_mcrypt_mode = $mcrypt_mode;

            $this->_iv = mcrypt_create_iv(mcrypt_get_iv_size($this->_cipher, $this->_mcrypt_mode));
        }

        /**
         * @param $text
         *
         * @return string
         */
        public function encrypt_data($text)
        {
            $encrypted_text = base64_encode(mcrypt_encrypt($this->_cipher, $this->_key, $text, $this->_mcrypt_mode, $this->_iv));

            return $encrypted_text;
        }

        /**
         * @param $text
         *
         * @return string
         */
        public function decrypt_data($text)
        {
            $decrypted_text = mcrypt_decrypt($this->_cipher, $this->_key, base64_decode($text), $this->_mcrypt_mode, $this->_iv);

            return $decrypted_text;
        }
    }