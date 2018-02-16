<?php

/**
* @Author: Johan Guerreros <johangm90@gmail.com>
* @License: Copyright (c) 2016. All Rights Reserved.
*/
use Metowolf\Meting;
class ApiController {

  protected $api;
  protected $app;

  public function __construct() {
    $this->api = new Meting('tencent');
    $this->app = Base::instance();
  }

  public function beforeRoute() {
    $this->setup();
  }

  public function setup() {
    $source = $this->app->get('PARAMS.source');
    if ($source) {
      $suppose = array('netease','tencent','xiami','kugou','baidu');
      $site = in_array($source, $suppose) ? $source : 'tencent';
      $this->api = new Meting($site);
      if ($site == 'netease') {
        $this->api->cookie('MUSIC_U=*****; buildver=1506310743; resolution=1920x1080; mobilename=MI5; osver=7.0.1; channel=coolapk; os=android; appver=4.2.0');
      }
      return $this->api;
    }
  }

  public function json($data) {
    header('content-type: application/json; charset=utf-8');
    echo $data;
  }

  public function search() {
    $keyword = $this->app->get('PARAMS.keyword');
    $page = $this->app->get('PARAMS.page');
    $limit = $this->app->get('PARAMS.limit');
    $result = $this->api->format(true)->search($keyword, $page, $limit);
    $this->json($result);
  }

  public function song() {
    $id = $this->app->get('PARAMS.id');
    $result = $this->api->format(true)->song($id);
    $this->json($result);
  }

  public function album() {
    $id = $this->app->get('PARAMS.id');
    $result = $this->api->format(true)->album($id);
    $this->json($result);
  }

  public function artist() {
    $id = $this->app->get('PARAMS.id');
    $result = $this->api->format(true)->artist($id);
    $this->json($result);
  }

  public function playlist() {
    $id = $this->app->get('PARAMS.id');
    $result = $this->api->format(true)->playlist($id);
    $this->json($result);
  }

  public function lyric() {
    $id = $this->app->get('PARAMS.id');
    $result = $this->api->format(true)->lyric($id);
    $this->json($result);
  }

  public function pic() {
    $id = $this->app->get('PARAMS.id');
    $result = $this->api->format(true)->pic($id);
    $data = json_decode($result, true);
    if($data['url']!=null) {
      $this->app->reroute($data['url']);
    }else {
      $this->app->error(404);
    }
  }

  public function url() {
    $id = $this->app->get('PARAMS.id');
    $br = $this->app->get('PARAMS.br');
    $result = $this->api->format(true)->url($id, $br);
    $this->json($result);
  }

  public function play() {
    $id = $this->app->get('PARAMS.id');
    $br = $this->app->get('PARAMS.br');
    $result = $this->api->format(true)->url($id, $br);
    $data = json_decode($result, true);
    if($data['url']!=null) {
      $this->app->reroute($data['url']);
    }else {
      $this->app->error(404);
    }
  }
}

?>
