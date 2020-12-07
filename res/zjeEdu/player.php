<!DOCTYPE html>
<html>
<head>
    <title>视频新闻</title>
    <link href="video-js.min.css" rel="stylesheet" type="text/css">
    <script src="video.js"></script>
    <script>
        videojs.options.flash.swf = "video-js.swf";
    </script>
    <style>
    .vjs-default-skin .vjs-big-play-button {
        left: 250px;
        top: 200px;
    }
    </style>
</head>
<body style="background:#333333">
    <center>
        <img src="top.png" />
        <video width="640" height="512" id="example_video_1" class="video-js vjs-default-skin" controls preload="none" width="640" height="264" data-setup="{}">
            <source src="<?php echo $_GET['url'] ?>" type="rtmp/flv"/>
            <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
        </video>
        <font size="2" color="#222">由 xRetiaWebProxy 强力驱动</font>
    </center>
</body>
</html>
