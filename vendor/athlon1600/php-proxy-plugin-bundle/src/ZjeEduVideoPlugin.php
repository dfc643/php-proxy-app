<?php

namespace Proxy\Plugin;

use Proxy\Plugin\AbstractPlugin;
use Proxy\Event\ProxyEvent;
use Proxy\Html;

class ZjeEduVideoPlugin extends AbstractPlugin {

	protected $url_pattern = 'pastwww.zje.net.cn';

	private function http_build_url($url_arr)
	{
		if (!empty($url_arr['scheme'])) {
			$new_url = $url_arr['scheme'] . "://" . $url_arr['host'];
		} else {
			$new_url = $url_arr['host'];
		}
		if (!empty($url_arr['port']))
			$new_url = $new_url . ":" . $url_arr['port'];
		$new_url = $new_url . $url_arr['path'];
		if (!empty($url_arr['query']))
			$new_url = $new_url . "?" . $url_arr['query'];
		if (!empty($url_arr['fragment']))
			$new_url = $new_url . "#" . $url_arr['fragment'];
		return $new_url;
	}
	
	public function onCompleted(ProxyEvent $event){
	
		$response = $event['response'];
		$html = $response->getContent();
		
		if(preg_match('@rtmp://[^"]*\.(mp4|f4v|flv|hls|m3u8|mpeg|mp3|m4v|m4a)@', $html, $matches)){
			
			$video_url = parse_url(rawurldecode($matches[0]));
			$video_url['host'] = str_replace(":10061", "", $_SERVER['HTTP_HOST']);
			$video_url = $this->http_build_url($video_url);
			
			$response->setStatusCode(302);
			$response->setContent("");
			return header("Location: /res/zjeEdu/player.php?url=".rawurlencode($video_url));
		}
		
		// remove useless scripts
		// $html = Html::remove_scripts($html);
		
		$response->setContent($html);
	}
}

?>