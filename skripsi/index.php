<?php

include_once 'database.php';
$app = new Database_Nodemcu();
$app->query_string = $_SERVER['QUERY_STRING'];

//var_dump($app->query_string);

if ($app->is_url_query('mode'))
{

    switch ($app->get_url_query_value('mode')) {

        default:
            $app->read_data();

            case 'save':
                if ( $app->is_url_query('light') && $app->is_url_query('carbon') && $app->is_url_query('soil') && $app->is_url_query('humidity') && $app->is_url_query('temperature'))
                {
                    $ldr = $app->get_url_query_value('light');
                    $mq135 = $app->get_url_query_value('carbon');
                    $soil = $app->get_url_query_value('soil');
                    $humid = $app->get_url_query_value('humidity');
                    $temp = $app->get_url_query_value('temperature');
                    $app->create_data($temp, $humid, $soil, $ldr, $mq135);
                } else {
                    $error = [
                        'light'=>'required',
                        'carbon'=>'required',
                        'soil'=>'required',
                        'temperature'=>'required',
                        'humidity'=> 'required',
                    ];
                    echo $app->error_handler($error);
                }
            break;

            case 'delete':
                if ($app->is_url_query('id'))
                {
                    $id = $app->get_url_query_value('id');
                    $app->delete_data($id);
                } else {
                    $error = [
                        'id'=>'required',
                    ];
                    echo $app->error_handler($error);
                }
            break;

          }
  }
  else {
          $app->read_data();
        }


 ?>
