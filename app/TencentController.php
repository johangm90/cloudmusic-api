<?php

/**
* @Author: Johan Guerreros <johangm90@gmail.com>
* @License: Copyright (c) 2016. All Rights Reserved.
*/

class TencentController {

  protected $api;
  protected $app;

  function __construct() {
    $this->api = new TencentMusicAPI();
    $this->app = Base::instance();
  }

  //$result = $api->search('hello');
  //$result = $api->artist('003CoxJh1zFPpx');
  //$result = $api->detail('001icUif3vTGcO');
  //$result = $api->album('002rBshp4WPAut');
  //$result = $api->playlist('801491460');
  //$result = $api->url('001icUif3vTGcO');
  //$result = $api->lyric('001icUif3vTGcO');

  function search() {
    $s = $this->app->get('PARAMS.s');
    $result = $this->api->search($s);
    header("Access-Control-Allow-Origin: *");
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }

  function url() {
    $song_id = $this->app->get('PARAMS.song_id');
    $result = $this->api->url($song_id);
    header("Access-Control-Allow-Origin: *");
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }

  function play() {
    $song_id = $this->app->get('PARAMS.song_id');
    $result = $this->api->url($song_id);
    $data = json_decode($result, true);
    if($data['320mp3']!=null){
      $this->app->reroute($data['320mp3']);
    }else {
      $this->app->error(404);
    }
  }

  function detail() {
    $song_id = $this->app->get('PARAMS.song_id');
    $result = $this->api->detail($song_id);
    header("Access-Control-Allow-Origin: *");
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }

  function lyric() {
    $song_id = $this->app->get('PARAMS.song_id');
    $result = $this->api->lyric($song_id);
    header("Access-Control-Allow-Origin: *");
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }
}

?>
