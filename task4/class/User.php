<?php

    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 15.10.2015
     * Time: 7:37
     */
    //����� ���������
    require_once "Encryption.php";

    // ������������ � ����
    require_once "Connection.php";

    class User
    {
        public $uid;
        public $email;
        public $encryptedEmail;

        private static $_userEncryptionSecret = "secret123";
        private static $_tableName = "Users";

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
        public function __construct($email)
        {
            //����������� ��������� ������
            $email = mb_strtolower($email);
            if (!preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/", $email)) {
                throw new Exception("�������� �������� ����");
            }
            $this->email = $email;

            $this->_userEncryptionSecret = self::_encryptEmail($email);

            //��������� � ����
            $db = Connection::getInstance();
            $tbl = self::$_tableName;
            $statement = $db->prepare("INSERT INTO $tbl SET email = :email");

            $statement->execute(array(":email" => $this->_userEncryptionSecret));
            $this->uid = $db->lastInsertId($tbl);
        }

        /**
         * @param $email
         *
         * @return string
         * @throws \Exception
         */
        private static function _encryptEmail($email)
        {
            //����������� ��������� ������
            if (!preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/", $email)) {
                throw new Exception("�������� �������� ����");
            }
            //��������� email �� ��� � ����� ��� ������������ ���������� � ������ �� ������
            $emailPieces = explode('@', $email);
            $name = $emailPieces[0];
            $domain = $emailPieces[1];

            //������������� ��������
            $encryption = new Encryption(self::$_userEncryptionSecret);
            $encryptedName = $encryption->encrypt_data($name);
            $encryptedDomain = $encryption->encrypt_data($domain);

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
            //����������� ���������
            if (!preg_match("/^([A-Za-z0-9_\.-=\/\+]+)@([A-Za-z0-9_\.-=\/\+]+)$/", $encryptedEmail)) {
                throw new Exception("�������� �������� ����", 500);
            }
            //��������� email �� ��� � ����� ��� ������������ ���������� � ������ �� ������
            $emailPieces = explode('@', $encryptedEmail);
            $encryptedName = $emailPieces[0];
            $encryptedDomain = $emailPieces[1];

            //������������� ��������
            $encryption = new Encryption(self::$_userEncryptionSecret);
            $name = $encryption->decrypt_data($encryptedName);
            $domain = $encryption->decrypt_data($encryptedDomain);

            return $name."@".$domain;
        }

        /**
         * @param null $domain
         *
         * @return array
         * @throws \Exception
         */
        public static function getList($domain = null)
        {
            $db = Connection::getInstance();
            $tbl = self::$_tableName;

            if ($domain === null) {
                //�������� ���� ������
                $encryptedUsers = $db->query(
                    "
                    SELECT uid, email
                    FROM $tbl
                    "
                )->fetchAll(PDO::FETCH_ASSOC);
            } else {
                //������������� ��������
                $encryption = new Encryption(self::$_userEncryptionSecret);

                //��������� ����� ������
                $encryptedDomain = "%@".$encryption->encrypt_data($domain);

                //������� ������
                $statement = $db->prepare(
                    "
                    SELECT uid, email
                    FROM $tbl
                    WHERE email LIKE :domain
                    "
                );
                $statement->bindValue(':domain', $encryptedDomain);
                //��������� ������
                $statement->execute();
                $encryptedUsers = $statement->fetchAll(PDO::FETCH_ASSOC);
            }

            //�������������� ������ ������
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