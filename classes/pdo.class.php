<?php

class pdoData {

    private $dbconn   = null;
    private $errormsg = null;

    function __construct($dsn=NULL, $user=NULL, $pass=NULL) {

        if (!$dsn) {

            $dsn  = 'mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['PRIMARY_DATABASE'].';charset='.$_ENV['CHARSET_DEFAULT'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];

        }

        try {

            $this->dbconn = new PDO($dsn, $user, $pass);

            $this->dbconn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->dbconn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->connect();

        } catch (PDOException $e) {

            $this->dbconn = null;
            $this->errormsg = $e->getMessage();

            return $this->error();

        }

    }

    public function connect() {

        if (isset($this->dbconn)) {
            return $this->dbconn;
        }

        return NULL;
    }

    public function error() {

        if (isset($this->errormsg)) {
            return $this->errormsg;
        }

        return NULL;
    }

    public function close() {
        $this->dbconn = null;

    }


}