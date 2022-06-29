<?php

class Database_Nodemcu
{

    private $connection;
    public $query_string;

    function __construct()
    {
        $this->cors();
        $this->connection = $this->open_connection();
    }

    function read_data()
    {
        $sql_query = "SELECT * FROM dashboard ORDER BY timestamp DESC";
        // $sql_query2 = "SELECT data_relay FROM tbl_relay WHERE id_perintah = 1"
        echo $this->execute_query($sql_query, [], true);

    }

    function create_data($temp, $humid, $soil, $ldr, $mq135)
    {
        date_default_timezone_set("Asia/Jakarta");
        $date = date("Y/m/d") ;
        $time = date("H:i:s");
        $sql_query = "INSERT INTO dashboard (temperature,humidity,soil,ldr,mq135,times,date) VALUES
        ('".$temp."','".$humid."','".$soil."','".$ldr."','".$mq135."','".$time."','".$date."')";
        echo $this->execute_query($sql_query);
    }

    function delete_data($id)
    {
        $sql_query = "DELETE FROM dashboard WHERE idDashboard = ".$id."";
        echo $this->execute_query($sql_query);
    }

    function error_handler($params = [])
    {
        $data = [];
        foreach($params as $param => $rules) {
            $data[$param] = $rules;
        }
        $data['status'] = false;
        $data['message'] = 'error on operation';
        return json_encode($data);
    }

    function is_url_query($string_value)
    {
        $query = array();
        parse_str($this->query_string, $query);
        if (array_key_exists($string_value, $query)) {
            return true;
        }
        return false;
    }

    function get_url_query_value($string_value)
    {
        $query = array();
        parse_str($this->query_string, $query);
        return $query[$string_value];
    }

    private function open_connection()
    {
        $servername = "192.168.100.129";
        $username = "plantfactory";
        $password = "sepeda265";
        $dbname = "pfactory";
        $conn = new mysqli($servername, $username, $password, $dbname) or die("Failed connect: %s\n" . $conn->error);
        return $conn;
    }

    private function close_connection()
    {
        $this->connection->close();
    }

    private function execute_query($sql, $data = [], $is_read = null)
    {
        // var_dump($sql);
        $executed = $this->connection->query($sql);
        //var_dump($executed);


        if ($executed == TRUE)
        {

            #sql connection tbl_relay
            $query2 = "SELECT manualrelay,autorelay,state_auto FROM tbl_relay WHERE id_perintah = 1";
            $executed2 = $this->connection->query($query2);

            $data['status'] = true;
            $data['message'] = 'data operation success';
            $data['state'] = '';
            $data['manual']  = '';
            $data['automatis'] = '';


            //datap[siram manual = ture]


              $data['data'] = [];
              if($executed2->num_rows != 0)
              {
                  while($row = $executed2->fetch_assoc())
                  {

                      if ($row["state_auto"] == '1')
                      {
                        $data['state'] = '#_automatisz!#';
                      }

                      if ($row["autorelay"] == '1')
                      {
                            $data['automatis'] = '#a_hidup1#';
                      }

                      if ($row["autorelay"] == '0')
                      {
                            $data['automatis'] = '#a_mati0#';
                      }


                      if ($row["state_auto"] == '0')
                      {
                        $data['state'] = '#_manualz!#';
                      }
                      if ($row["manualrelay"] == '1')
                      {
                          $data['manual'] = '#m_hidup1#';
                      }
                      if ($row["manualrelay"] == '0')
                      {
                          $data['manual'] = '#m_mati0#';
                      }

                  }
              }


              if (!is_null($is_read) && $is_read)
              {
                  $data['data'] = [];
                  if($executed->num_rows != 0)
                  {
                      while($row = $executed->fetch_assoc())
                      {
                          $data['data'][] = $row;
                      }
                  }
              }
              header('Content-Type: application/json');
              return json_encode($data);
          }

        $data['status'] = false;
        $data['message'] = 'data operation failed';
        header('Content-Type: application/json');
        return json_encode($data);
    }


    private function cors() {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }
}


 ?>
