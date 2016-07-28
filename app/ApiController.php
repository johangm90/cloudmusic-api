<?php

/**
* @Author: Johan Guerreros <johangm90@gmail.com>
* @License: Copyright (c) 2016. All Rights Reserved.
*/

class ApiController {

  protected $api;
  protected $app;

  function __construct() {
    $this->api = new MusicAPI();
    $this->app = Base::instance();
  }

  function search() {
    $s = $this->app->get('PARAMS.s');
    $type = $this->app->get('PARAMS.type');
    $limit = $this->app->get('PARAMS.limit');
    $result = $this->api->search($s, $type, $limit);
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }


}

  # Initialize


  # Get data
  //$result = $api->search('hello');
  // $result = $api->detail('35847388');
  // $result = $api->albums('3377030');
  // $result = $api->playlist('124394335');
  // $result = $api->url('35847388');
  // $result = $api->lyric('35847388');
   $result = $api->mv('501053');

  # return JSON, just use it
  echo $result;
?>
