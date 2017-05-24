<?php

/**
* @Author: Johan Guerreros <johangm90@gmail.com>
* @License: Copyright (c) 2016. All Rights Reserved.
*/
use Metowolf\Meting;
class ApiController {

  protected $api;
  protected $app;
  protected $meting;

  public function __construct() {
    $this->api = new NeteaseMusicAPI();
    $this->meting = new Meting('netease');
    $this->app = Base::instance();
  }

  //$result = $api->search('hello');
  //$result = $api->detail('35847388');
  //$result = $api->albums('3377030');
  //$result = $api->playlist('124394335');
  //$result = $api->url('35847388');
  //$result = $api->lyric('35847388');
  //$result = $api->mv('501053');

  public function search() {
    $s = $this->app->get('PARAMS.s');
    $type = $this->app->get('PARAMS.type');
    $limit = $this->app->get('PARAMS.limit');
    $result = $this->api->search($s, $limit, 0, $type);
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }

  public function url() {
    //hMusic = 320000
    //mMusic = 160000
    //lMusic = 96000
    $song_id = $this->app->get('PARAMS.song_id');
    //$br = $this->app->get('PARAMS.br');
    $br = 320;
    //$result = $this->api->url($song_id, $br);
    $result = $this->meting->url($song_id, $br);
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }

  public function play() {
    $song_id = $this->app->get('PARAMS.song_id');
    //$br = $this->app->get('PARAMS.br');
    $br = 320;
    //$result = $this->api->url($song_id, $br);
    $result = $this->meting->url($song_id, $br);
    $data = json_decode($result, true);
    if($data['data'][0]['url']!=null){
      $this->app->reroute($data['data'][0]['url']);
    }else {
      //$this->app->error(404);
      //$this->app->reroute('http://vulgry.com/rapsody/api.php?go=' . $song_id .'&quality=lMusic');
      $this->app->reroute('@mp3(@song_id={$song_id},@br={$br}');
    }
  }

  public function download() {
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

  /*public function detail() {
    $song_id = $this->app->get('PARAMS.song_id');
    $result = $this->api->detail($song_id);
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }*/

  public function detail() {
    $song_id = $this->app->get('PARAMS.song_id');
    $result = $this->meting->song($song_id);
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }

  public function mp3() {
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
    $fid = $this->decrypt_id($sid);
    //$url = 'http://219.138.27.38/m2.music.126.net/'.$fid.'/'.$sid.'.mp3';
    $url = 'http://p3.music.126.net/'.$fid.'/'.$sid.'.mp3';
    //echo $url;
    $this->app->reroute($url);
  }

  public function lyric() {
    $song_id = $this->app->get('PARAMS.song_id');
    $result = $this->api->lyric($song_id);
    $result = json_decode($result, true);
    $lyric = $result['lrc']['lyric'];
    echo $lyric;
  }

  public function mv() {
    $mv_id = $this->app->get('PARAMS.mv_id');
    $result = $this->api->mv($mv_id);
    header('content-type: application/json; charset=utf-8');
    echo $result;
  }

  public function img() {
    $song_id = $this->app->get('PARAMS.song_id');
    $result = $this->api->detail($song_id);
    $result = json_decode($result, true);
    $pic_id = $result['songs'][0]['al']['pic'];
    $pic = 'http://p3.music.126.net/'.$this->pickey($pic_id).'/'.$pic_id.'.jpg';
    $this->app->reroute($pic);
  }

  public function pickey($id) {
      $magic=str_split('3go8&$8*3*3h0k(2)2');
      $song_id=str_split($id);
      for ($i=0;$i<count($song_id);$i++) {
          $song_id[$i]=chr(ord($song_id[$i])^ord($magic[$i%count($magic)]));
      }
      $result=base64_encode(md5(implode('', $song_id), 1));
      $result=str_replace(array('/','+'), array('_','-'), $result);
      return $result;
  }

  public function decrypt_id($id) {
    $id = (string)$id;
    $byte1 = unpack('C*', '3go8&$8*3*3h0k(2)2');
    $byte2 = unpack('C*', $id);
    $byte1_len = count($byte1);
    for($i=1; $i<=count($byte2); $i++){
      $byte2[$i] = $byte2[$i]^$byte1[$i%$byte1_len];
    }
    $string = implode(array_map("chr", $byte2));
    $m = md5($string, true);
    $result = base64_encode($m);
    $result = str_replace('/', '_', $result);
    $result = str_replace('+', '-', $result);
    return $result;
  }
}

?>
