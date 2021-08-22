<?php
/**
 *   File name:  pdo.class.php
 *   Author:     Charles Seale
 *   Created:    Aug. 2021
 *   Copyright:  2021, Charles Seale 
 * 
*/

class pdoData {

    private $pdo        = null;
    private $stmt       = null;
    private $errormsg   = null;
    private $stmtType   = null;
    private $fetchType  = null;  // PDO::FETCH_OBJ is the default

    function __construct($dsn=NULL, $user=NULL, $pass=NULL) {

        /**
         *   If a dns string is passed in, the class will attempt to use it. 
         *   If no dns string is included, no other parameters are checked. The
         *   class will then instantiate using it's default database configuration 
         *   whose values are found in the environment variables.
        */

        if (!$dsn) {

            $dsn  = 'mysql:host='.$_ENV['DB_HOST'].';dbname='.
                    $_ENV['PRIMARY_DATABASE'].';charset='.
                    $_ENV['CHARSET_DEFAULT'];
            
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];

        }

        try {

            $this->pdo = new PDO($dsn, $user, $pass);

            // By default, return data in object format
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); 

            // Set emulation to false so that we can specify placefolder for "LIMIT" in SELECT statements
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // Give us useful PDO error messages when exceptions occur
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return true;

        } catch (PDOException $e) {

            $this->pdo = null;
            $this->errormsg = $e->getMessage();

            return $this->error();

        }

    }

    public function query($sql, $binds=NULL, $fetchType=NULL) {

        $this->clear();

        $this->fetchType = $fetchType;

        /* 
            If query type is not permitted, an error message will be returned.
            Supported query types are INSERT, SELECT, UPDATE, DELETE, CALL. 
        */

        $this->setSqlType($sql);

        if($this->errormsg) {
            return $this->error();
        }

        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($binds);
        
        return $this->data();

    }

    public function data() {

        $fetchMode = null;

        switch($this->fetchType) {

            case 'idx_array':
                $fetchMode = PDO::FETCH_NUM;
                break;

            case 'assoc_array':
                $fetchMode = PDO::FETCH_ASSOC;
                break;

            case 'object':
                $fetchMode = PDO::FETCH_OBJ;
                break;

            default:
                $fetchMode = null;

        }

        $data = $this->stmt->fetchall($fetchMode);

        switch($this->stmtType) {

            case 'SELECT':
                $retObj = [
                    'rowCount' => $this->stmt->rowCount(),
                    'data'     => $data,
                ];
                break;

            case 'INSERT':
                $retObj = [
                    'rowsInserted' => $this->stmt->rowCount(),
                    'lastInsertId' => $this->pdo->lastInsertId() ? $this->pdo->lastInsertId() : null
                ];
                break;

            case 'UPDATE':
                $retObj = [
                    'rowsUpdated' => $this->stmt->rowCount()
                ];
                break;

            case 'DELETE':
                $retObj = [
                    'rowsDeleted' => $this->stmt->rowCount()
                ];
                break;

            case 'PROC_CALL':
                $retObj = [
                    'data' => $data,
                ];
                break;   

        }

        return $retObj;

    }

    public function error() {

        if (isset($this->errormsg)) {
            return [
                'error_msg' => $this->errormsg
            ];
        }

        return NULL;
    }

    public function close() {
        $this->pdo = null;
    }

    private function clear() {
        $this->stmt       = null;
        $this->errormsg   = null;
        $this->stmtType   = null;
        $this->fetchType  = null;
    }

    private function setSqlType($sql) {

        $this->errormsg = null;

        // C.R.U.D. operations or Stored Procedure calls only
        switch(strtolower(substr($sql, 0, 4))) {

            case 'sele':
                $this->stmtType = 'SELECT';
                break;

            case 'inse':
                $this->stmtType = 'INSERT';
                break;

            case 'upda':
                $this->stmtType = 'UPDATE';
                break;

            case 'dele':
                $this->stmtType = 'DELETE';
                break;

            case 'call':
                $this->stmtType = 'PROC_CALL';
                break;

            default:
                $this->stmtType =  null; 
                $this->errormsg = 'SQL statement error: '. substr($sql, 0, 6) .' operation is not permitted';
        }

        return;
    }


}