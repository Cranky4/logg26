<?php

    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 15.10.2015
     * Time: 7:37
     */
    class User
    {
        private static $_tableName = "Users";
        private static $_connection;
        private static $_encryption;
        private static $_cryptKey = 'secret123';

        /**
         * DI
         *
         * @param \Connection $connection
         * @param \Encryption $encryption
         */
        public function __construct(PDO $connection, Encryption $encryption, $cryptKey = null)
        {
            self::$_connection = $connection;
            self::$_encryption = $encryption;
            if ($cryptKey) {
                self::$_cryptKey = $cryptKey;
            }
        }

        /**
         * @return string
         */
        public static function getSecretKey()
        {
            return self::$_cryptKey;
        }

        /**
         * @return string
         */
        public function getTableName()
        {
            return self::$_tableName;
        }

        /**
         * @param $email
         *
         * @return bool
         * @throws \Exception
         */
        public function addUser($email)
        {
            //минимальная валидация емейла
            $email = mb_strtolower($email);
            if (!preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/", $email)) {
                throw new Exception("Неверный формат почтового ящика");
            }

            $this->_userEncryptionSecret = self::_encryptEmail($email);

            //сохраняем в базу
            $tbl = self::$_tableName;
            $statement = self::$_connection->prepare("INSERT INTO $tbl SET email = :email");

            return $statement->execute(array(":email" => $this->_userEncryptionSecret));
        }

        /**
         * @param $email
         *
         * @return string
         * @throws \Exception
         */
        private static function _encryptEmail($email)
        {
            //минимальная валидация емейла
            if (!preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/", $email)) {
                throw new Exception("Неверный почтовый ящик");
            }
            //разбираем email на имя и домен для последующего сохранения и поиска по домену
            $emailPieces = explode('@', $email);
            $name = $emailPieces[0];
            $domain = $emailPieces[1];

            //устанавливаем шифратор
            $encryptedName = self::$_encryption->encrypt_data($name);
            $encryptedDomain = self::$_encryption->encrypt_data($domain);

            return $encryptedName."@".$encryptedDomain;
        }

        /**
         * @param $encryptedEmail
         *
         * @return string
         * @throws \Exception
         */
        private static function _decryptEmail($encryptedEmail)
        {
            //минимальная валидация
            if (!preg_match("/^([A-Za-z0-9_\.-=\/\+]+)@([A-Za-z0-9_\.-=\/\+]+)$/", $encryptedEmail)) {
                throw new Exception("Неверный почтовый ящик", 500);
            }
            //разбираем email на имя и домен для последующего сохранения и поиска по домену
            $emailPieces = explode('@', $encryptedEmail);
            $encryptedName = $emailPieces[0];
            $encryptedDomain = $emailPieces[1];

            //устанавливаем шифратор
            $name = self::$_encryption->decrypt_data($encryptedName);
            $domain = self::$_encryption->decrypt_data($encryptedDomain);

            return $name."@".$domain;
        }

        /**
         * @param null $domain
         *
         * @return array
         * @throws \Exception
         */
        public function getList($domain = null)
        {
            $tbl = self::$_tableName;

            if ($domain === null) {
                //получаем всех юзеров
                $encryptedUsers = self::$_connection->query(
                    "
                    SELECT uid, email
                    FROM $tbl
                    "
                )->fetchAll(PDO::FETCH_ASSOC);
            } else {
                //добавляем маску поиска
                $encryptedDomain = "%@".self::$_encryption->encrypt_data($domain);

                //готовим запрос
                $statement = self::$_connection->prepare(
                    "
                    SELECT uid, email
                    FROM $tbl
                    WHERE email LIKE :domain
                    "
                );
                $statement->bindValue(':domain', $encryptedDomain);
                //запускаем запрос
                $statement->execute();
                $encryptedUsers = $statement->fetchAll(PDO::FETCH_ASSOC);
            }

            //расшифровываем данные юзеров
            $users = array();
            foreach ($encryptedUsers as $encryptedUser) {
                $users[] = array(
                    'uid'   => $encryptedUser['uid'],
                    'email' => self::_decryptEmail($encryptedUser['email']),
                );
            }

            return $users;
        }


    }