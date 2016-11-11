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
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }

  function url() {
    $song_id = $this->app->get('PARAMS.song_id');
    $result = $this->api->url($song_id);
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

  function download() {
    $song_id = $this->app->get('PARAMS.song_id');
    $br = $this->app->get('PARAMS.br');
    $result = $this->api->url($song_id, $br);
    $data = json_decode($result, true);
    if($data['data'][0]['url']!=null){
      echo $data['data'][0]['url'];
    }else {
      //$this->app->error(404);
      $url = file_get_contents('http://vulgry.com/rapsody/api.php?download=' . $song_id .'&quality=lMusic');
      echo $url;
    }
  }

  function detail() {
    $song_id = $this->app->get('PARAMS.song_id');
    $result = $this->api->detail($song_id);
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }

  function mp3() {
    $song_id = $this->app->get('PARAMS.song_id');
    $br = $this->app->get('PARAMS.br');
    $result = $this->api->detail($song_id);
    $data = json_decode($result, true);
    switch ($br) {
      case 96000:
        $quality = 'l';
        break;
      case 160000:
        $quality = 'm';
        break;
      case 320000:
        $quality = 'h';
        break;
      default:
        $quality = 'l';
        break;
    }
    $sid = $data['songs'][0][$quality]['fid'];
    $fid = $this->api->decrypt_id($sid);
    //$url = 'http://219.138.27.38/m2.music.126.net/'.$fid.'/'.$sid.'.mp3';
    $url = 'http://p3.music.126.net/'.$fid.'/'.$sid.'.mp3';
    //echo $url;
    $this->app->reroute($url);
  }
}

?>
