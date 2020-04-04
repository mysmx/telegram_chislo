<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27.02.2018
 * Time: 22:39
 */
class Db
{
    public $config;
    public function Db(){
        $this->config = array(
            'mysql_server' => 'localhost',
            'mysql_user' => '',
            'mysql_password' => '',
            'mysql_db' => ''
        );
    }
    public function get($sql){
        $conn = new mysqli($this->config['mysql_server'], $this->config['mysql_user'], $this->config['mysql_password'], $this->config['mysql_db']);
        mysqli_set_charset($conn, "utf8");
        $conn->query("SET NAMES 'utf8'");
        $conn->query("SET CHARACTER SET 'utf8'");
        $conn->query("SET SESSION collation_connection = 'utf8_general_ci'");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $result = $conn->query($sql);
        $rows = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        $conn->close();
        return $rows;
    }
    public function set($sql){
        $conn = new mysqli($this->config['mysql_server'], $this->config['mysql_user'], $this->config['mysql_password'], $this->config['mysql_db']);
        mysqli_set_charset($conn, "utf8");
        $conn->query("SET NAMES 'utf8'");
        $conn->query("SET CHARACTER SET 'utf8'");
        $conn->query("SET SESSION collation_connection = 'utf8_general_ci'");

        $result = true;

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        if ($conn->query($sql) !== TRUE) {
            $result = false;
        }

        if(isset($conn->insert_id)) $result = $conn->insert_id;

        $conn->close();
        return $result;
    }
}
$db = new Db();
