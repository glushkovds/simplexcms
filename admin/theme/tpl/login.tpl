<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Simplex Admin 2.0</title>

        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/global/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/global/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/global/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/global/uniform.default.css" rel="stylesheet" type="text/css"/>
        <!-- END GLOBAL MANDATORY STYLES -->

        <!-- BEGIN THEME STYLES -->
        <link href="/admin/theme/css/conquer/style-conquer.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/conquer/style.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/conquer/style-responsive.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/conquer/plugins.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/conquer/default.css" rel="stylesheet" type="text/css" id="style_color"/>
        <link href="/admin/theme/css/conquer/login.css" rel="stylesheet" type="text/css"/>
        <!-- END THEME STYLES -->

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link type="text/css" rel="stylesheet" href="/admin/theme/css/default.css" />
        <script type="text/javascript" src="/admin/theme/js/jquery-1.6.1.min.js"></script>
        <script type="text/javascript" src="/admin/theme/js/default.js"></script>
    </head>


    <body class="login">

        <div class="logo">
            <a href="/admin/">
                <span>Simplex</span> Admin
            </a>
        </div>

        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            <form method="post" action="/admin/login.php?r=<?php echo urlencode($back) ?>" class="login-form" style="display: block;">
                <h3 class="form-title">Вход в панель управления</h3>
                <div class="alert alert-danger display-hide">
                    <button data-close="alert" class="close"></button>
                    <span>
                        Enter any username and password. </span>
                </div>
                <div class="form-group">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">Логин</label>
                    <div class="input-icon">
                        <i class="fa fa-user"></i>
                        <input type="text" name="login[login]" placeholder="Логин" autocomplete="off" class="form-control placeholder-no-fix" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Пароль</label>
                    <div class="input-icon">
                        <i class="fa fa-lock"></i>
                        <input type="password" name="login[password]" placeholder="Пароль" autocomplete="off" class="form-control placeholder-no-fix" />
                    </div>
                </div>
                <div class="form-actions">
                    <label class="checkbox">
                        <input type="checkbox" value="1" name="login[remember]" /> Запомнить меня
                    </label>
                    <button class="btn btn-info pull-right" type="submit" name="login[submit]"> Вход </button>
                </div>
                <div class="forget-password hidden">
                    <h4>Забыли пароль?</h4>
                    <p>
                        Не беспокойтесть, нажмите <a id="forget-password" href="javascript:;">здесь</a>
                        чтобы восстановить свой пароль.
                    </p>
                </div>
            </form>
            <!-- END LOGIN FORM -->
            
            <!-- BEGIN FORGOT PASSWORD FORM -->
            <form method="post" action="index.html" class="forget-form" novalidate="novalidate" style="display: none;">
                <h3>Forget Password ?</h3>
                <p>
                    Enter your e-mail address below to reset your password.
                </p>
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fa fa-envelope"></i>
                        <input type="text" name="email" placeholder="Email" autocomplete="off" class="form-control placeholder-no-fix">
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn btn-default" id="back-btn" type="button">
                        <i class="m-icon-swapleft"></i> Back </button>
                    <button class="btn btn-info pull-right" type="submit">
                        Submit </button>
                </div>
            </form>
            <!-- END FORGOT PASSWORD FORM -->
        </div>

        <div class="copyright">
            2015 - <?php echo date('Y') ?> &copy; Simplex Admin 2.0
        </div>

    </body>
</html>