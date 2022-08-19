<?php

    class Config
    {
        private const DBHOST = 'localhost';
        private const DBUSER = 'root';
        private const DBPASS = '';
        private const DBNAME = 'ecommerce';

        private $dsn = 'mysql:host=' . self::DBHOST . ';dbname=' . self::DBNAME . '';

        protected $conn = null;

        public function __construct()
        {
            try
            {
                $this->conn = new PDO($this->dsn, self::DBUSER, self::DBPASS);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
            catch (PDOException $exception)
            {
                echo json_encode(['message' => 'Connection failed.  There is some issue in database', 'error' => true]);
            }
            return $this->conn;
        }

        public function test_input($data) 
        {
            $data = strip_tags($data);
            $data = htmlspecialchars($data);
            $data = stripslashes($data);
            $data = trim($data);
            return $data;
        }

        public function message($content, $status, $data = null)
        {
            if ($data)
            {
                echo json_encode(['message' => $content, 'error' => $status, 'data' => $data]);
            }
            else
            {
                echo json_encode(['message' => $content, 'error' => $status]);
            }
        }
    }

?>