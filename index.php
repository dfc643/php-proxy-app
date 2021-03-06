<?php

define('PROXY_START', microtime(true));

require("vendor/autoload.php");

use Proxy\Config;
use Proxy\Http\Request;
use Proxy\Proxy;

// start the session
session_start();

// load config...
Config::load('./config.php');

// custom config file to be written to by a bash script or something
Config::load('./custom_config.php');

if (!Config::get('app_key')) {
    die("app_key inside config.php cannot be empty!");
}

if (!function_exists('curl_version')) {
    die("cURL extension is not loaded!");
}

// how are our URLs be generated from this point? this must be set here so the proxify_url function below can make use of it
if (Config::get('url_mode') == 2) {
    Config::set('encryption_key', md5(Config::get('app_key') . $_SERVER['REMOTE_ADDR']));
} elseif (Config::get('url_mode') == 3) {
    Config::set('encryption_key', md5(Config::get('app_key') . session_id()));
}

// very important!!! otherwise requests are queued while waiting for session file to be unlocked
session_write_close();

// form submit in progress...
if (isset($_POST['url'])) {

    $url = $_POST['url'];
    $url = add_http($url);

    header("HTTP/1.1 302 Found");
    header('Location: ' . proxify_url($url));
    exit;

} elseif (!isset($_GET['q'])) {

    // must be at homepage - should we redirect somewhere else?
    if (Config::get('index_redirect')) {

        // redirect to...
        header("HTTP/1.1 302 Found");
        header("Location: " . Config::get('index_redirect'));

    } else {
        switch(Config::get('home_mode'))
        {
            case 1: 
                echo render_template("./templates/404.php", array('version' => Proxy::VERSION));
            break;

            default:
                echo render_template("./templates/main.php", array('version' => Proxy::VERSION));
        }
    }

    exit;
}

// decode q parameter to get the real URL
// FIXED: missing url parameters when  [urlmode = 0]
$url = url_decrypt($_GET['q']);
parse_str($_SERVER['QUERY_STRING'], $qs_array);
foreach ( $qs_array as $k => $v ) {
    if ( $k == "q" ) continue;
    $url .= "&$k=$v"; 
}

// FIXED: urlencode when has Chinese characters.
$pregstr = "/[\x{4e00}-\x{9fa5}]+/u"; //UTF-8中文正则
if (preg_match_all($pregstr, $url, $matchArray)) { //匹配中文，返回数组
    foreach ($matchArray[0] as $key => $val) {
        $url = str_replace($val, urlencode($val), $url); //将转译替换中文
    }
    if (strpos($url, ' ')) { //若存在空格
        $url = str_replace(' ', '%20', $url);
    }
}

// IGNORE_LIST
$iglst_host = parse_url($url)['host'];
foreach(file("config/ignore.list") as $line) {
    if(strtolower($iglst_host) === trim(strtolower($line))) {
        header("HTTP/1.1 302 Found");
        header("Location: " . $url);
        exit;
    }
}

$proxy = new Proxy();

// load plugins
foreach (Config::get('plugins', array()) as $plugin) {

    $plugin_class = $plugin . 'Plugin';

    if (file_exists('./plugins/' . $plugin_class . '.php')) {

        // use user plugin from /plugins/
        require_once('./plugins/' . $plugin_class . '.php');

    } elseif (class_exists('\\Proxy\\Plugin\\' . $plugin_class)) {

        // does the native plugin from php-proxy package with such name exist?
        $plugin_class = '\\Proxy\\Plugin\\' . $plugin_class;
    }

    // otherwise plugin_class better be loaded already through composer.json and match namespace exactly \\Vendor\\Plugin\\SuperPlugin
    // $proxy->getEventDispatcher()->addSubscriber(new $plugin_class());

    $proxy->addSubscriber(new $plugin_class());
}

try {

    // request sent to index.php
    $request = Request::createFromGlobals();

    // remove all GET parameters such as ?q=
    $request->get->clear();

    // forward it to some other URL
    $response = $proxy->forward($request, $url);

    // if that was a streaming response, then everything was already sent and script will be killed before it even reaches this line
    $response->send();

} catch (Exception $ex) {

    // if the site is on server2.proxy.com then you may wish to redirect it back to proxy.com
    if (Config::get("error_redirect")) {

        $url = render_string(Config::get("error_redirect"), array(
            'error_msg' => rawurlencode($ex->getMessage())
        ));

        // Cannot modify header information - headers already sent
        header("HTTP/1.1 302 Found");
        header("Location: {$url}");

    } else {

        switch(Config::get('home_mode'))
        {
            case 1: 
                echo render_template("./templates/404.php", array(
                    'url' => $url,
                    'error_msg' => explode(") ", $ex->getMessage())[0],
                    'version' => Proxy::VERSION
                ));
            break;

            default:
                echo render_template("./templates/main.php", array(
                    'url' => $url,
                    'error_msg' => $ex->getMessage(),
                    'version' => Proxy::VERSION
                ));
        }
    }
}
