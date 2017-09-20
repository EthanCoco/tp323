<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!doctype html>
<html lang="en" class="error-404">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>系统发生错误</title>
    <!--<link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">-->
    <!--<link rel="icon" type="image/png" sizes="192x192" href="favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">-->
    <link rel="stylesheet" type="text/css" href="__HOMESTYLE__/Manage/stylesheets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="__HOMESTYLE__/Manage/stylesheets/css/font-awesome.min.css">
    <link rel="stylesheet" href="__HOMESTYLE__/Manage/vendor/animate.css/animate.css">
    <link rel="stylesheet" href="__HOMESTYLE__/Manage/stylesheets/css/style.css">
</head>

<body>
<div class="wrap">
    <div class="page-body">
        <div class="row animated bounce ">
            <div class="col-sm-8 col-sm-offset-2">
                <div class="panel mt-xlg">
                    <div class="panel-content">
                        <h1 class="error-number">404</h1>
                        <h2 class="error-name"> Page not found</h2>
                        <p class="error-text">Sorry, the page you are looking for cannot be found.
                            <br/>Please check the url for errors or try one of the options below</p>
                        <div class="row mt-xlg">
                            <div class="col-sm-6  col-sm-offset-3">
                            	<p><a title="官方网站" href="http://www.thinkphp.cn">ThinkPHP</a><sup><?php echo THINK_VERSION ?></sup> { Fast & Simple OOP PHP Framework } -- [ WE CAN DO IT JUST THINK ]</p>
                                <!--<button class="btn btn-sm btn-darker-2 btn-block" onClick="history.back();">Previous page</button>
                                <a href="http://myiideveloper.com/helsinki/helsinki-green/dashboard.html" class="btn btn-sm btn-primary btn-block">Dashboard</a>
                                <a href="pages_faq.html" class="btn btn-sm btn-lighter-2 btn-block mb-xlg">FAQ</a>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="__HOMESTYLE__/Manage/javascripts/jquery.min.js"></script>
<script src="__HOMESTYLE__/Manage/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="__HOMESTYLE__/Manage/vendor/nano-scroller/nano-scroller.js"></script>
<script src="__HOMESTYLE__/Manage/javascripts/template-script.min.js"></script>
<script src="__HOMESTYLE__/Manage/javascripts/template-init.min.js"></script>
</body>
</html>
