<?php
class ConnectInfo
{
    public $dbHost = "localhost";
    public $conn;
    public $params = array();
    public $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    public $connectionInfo = array("UID" =>"", "PWD" => "", "Database" => "AnalyzedData", 'CharacterSet' => "utf-8");
    function __construct(){
        $this->conn = sqlsrv_connect($this->dbHost, $this->connectionInfo);
        if ($this->conn == false) {
            echo "连接至服务器数据库失败";
            die(print_r(sqlsrv_errors(), true));
        }
        return $this->conn;
    }
    public function returnQuery($sql){
        return sqlsrv_query($this->conn, $sql, $this->params, $this->options);
    }
    public function returnRow($sql){
        $query=sqlsrv_query($this->conn, $sql, $this->params, $this->options);
        return sqlsrv_fetch_array($query);
    }
}
