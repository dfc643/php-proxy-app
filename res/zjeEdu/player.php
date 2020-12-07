
<!--
/*
 * (C) Copyright 2012 Adobe Systems Incorporated. All Rights Reserved.
 *
 * NOTICE:  Adobe permits you to use, modify, and distribute this file in accordance with the 
 * terms of the Adobe license agreement accompanying it.  If you have received this file from a 
 * source other than Adobe, then your use, modification, or distribution of it requires the prior 
 * written permission of Adobe. 
 * THIS CODE AND INFORMATION IS PROVIDED "AS-IS" WITHOUT WARRANTY OF
 * ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 *
 *  THIS CODE IS NOT SUPPORTED BY Adobe Systems Incorporated.
 *
 */
-->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0014)about:internet -->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>视频新闻</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body style="background:#333333">
    <center>
    <img src="top.png"/></br>
    <object type="application/x-shockwave-flash" 
    id="SampleMediaPlayback" 
    name="SampleMediaPlayback" 
    data="SampleMediaPlayback.swf" 
    width="640" height="512" align="middle">
        <param name="quality" value="high">
        <param name="bgcolor" value="#000000">
        <param name="allowscriptaccess" value="sameDomain">
        <param name="allowfullscreen" value="true">
        <param name="flashvars" value="src=<?php echo $_GET['url'] ?>&amp;streamType=recorded&amp;autoPlay=true&amp;controlBarAutoHide=true&amp;controlBarPosition=bottom">
    </object>
    </center>
</body>
</html>

