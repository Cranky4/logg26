<?php

    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 15.10.2015
     * Time: 7:07
     */
    class Connection
    {
        protected static $_connection;

        private function __construct()
        {
            try {
                self::$_connection = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
                self::$_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        /**
         * @return \PDO
         */
        public static function getInstance()
        {
            if (self::$_connection === null) {
                new self();
            }

            return self::$_connection;
        }

    }