<?php
//PlugJQuery::jquery();
PlugJQuery::fancybox();
AdminPlugAlert::init();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>
            <?php echo SFAdminCore::menuCurItem('name') ? SFAdminCore::menuCurItem('name') . ' |' : '' ?>
            <?php echo SFAdminCore::siteParam('site_name') ?> |
            Simplex Admin 2.0
        </title>
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=cyrillic,latin" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/global/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/global/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/global/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/global/uniform.default.css" rel="stylesheet" type="text/css"/>
        <!-- END GLOBAL MANDATORY STYLES -->

        <!-- BEGIN THEME STYLES -->
        <link href="/admin/theme/css/conquer/style-conquer.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/conquer/style.css" rel="stylesheet" type="text/css"/>
        <link href="/admin/theme/css/conquer/style-responsive.css" rel="stylesheet" type="text/css"/>
        <!--<link href="/admin/theme/css/conquer/plugins.css" rel="stylesheet" type="text/css"/>-->
        <link href="/admin/theme/css/conquer/default.css" rel="stylesheet" type="text/css" id="style_color"/>
        <!-- END THEME STYLES -->

        <!-- BEGIN CORE PLUGINS -->
        <script src="/admin/theme/js/conquer/jquery-1.11.0.min.js" type="text/javascript"></script>
        <script src="/admin/theme/js/conquer/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
        <!--<script src="/admin/theme/js/conquer/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>-->
        <script src="/admin/theme/js/conquer/bootstrap.min.js" type="text/javascript"></script>
        <script src="/admin/theme/js/conquer/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <!--<script src="/admin/theme/js/conquer/jquery.slimscroll.min.js" type="text/javascript"></script>-->
        <!--<script src="/admin/theme/js/conquer/jquery.blockui.min.js" type="text/javascript"></script>-->
        <script src="/admin/theme/js/conquer/jquery.uniform.min.js" type="text/javascript"></script>
        <!--<script src="/admin/theme/js/conquer/jquery.knob.js"></script>-->
        <!--<script src="/admin/theme/js/conquer/ui-knob.js"></script>-->
        <script src="/admin/theme/js/conquer/app.js" type="text/javascript"></script>
        <script src="/admin/theme/js/conquer/form-components.js" type="text/javascript"></script>

        <?php SFAdminPage::meta() ?>

        <script type="text/javascript" src="/theme/default/js/cookie.js"></script>
        <script type="text/javascript" src="/admin/theme/js/default.js"></script>
        <link type="text/css" rel="stylesheet" href="/admin/theme/css/default.css" />

        <?php PlugFrontEnd::output(); ?>
    </head>


    <body class="page-header-fixed">


        <div class="header navbar navbar-fixed-top">
            <div class="header-inner">
                <div class="page-logo">
                    <a href="/admin/">
                        <span>Simplex</span>&nbsp;Admin
                    </a>
                </div>

                <a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <img src="/admin/theme/img/menu-toggler.png" alt=""/>
                </a>

                <ul class="nav navbar-nav pull-right">

                    <?php // SFAdminPage::notifications() ?>

                    <!-- END NOTIFICATION DROPDOWN -->
                    <?php /*
                      <!-- BEGIN TODO DROPDOWN -->
                      <li class="dropdown" id="header_task_bar">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                      <i class="icon-calendar"></i>
                      <span class="badge badge-warning">
                      5 </span>
                      </a>
                      <ul class="dropdown-menu extended tasks">
                      <li>
                      <p>
                      You have 12 pending tasks
                      </p>
                      </li>
                      <li>
                      <ul class="dropdown-menu-list scroller" style="height: 250px;">
                      <li>
                      <a href="#">
                      <span class="task">
                      <span class="desc">
                      New release v1.2 </span>
                      <span class="percent">
                      30% </span>
                      </span>
                      <span class="progress">
                      <span style="width: 40%;" class="progress-bar progress-bar-success" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">
                      40% Complete </span>
                      </span>
                      </span>
                      </a>
                      </li>
                      <li>
                      <a href="#">
                      <span class="task">
                      <span class="desc">
                      Application deployment </span>
                      <span class="percent">
                      65% </span>
                      </span>
                      <span class="progress progress-striped">
                      <span style="width: 65%;" class="progress-bar progress-bar-danger" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">
                      65% Complete </span>
                      </span>
                      </span>
                      </a>
                      </li>
                      <li>
                      <a href="#">
                      <span class="task">
                      <span class="desc">
                      Mobile app release </span>
                      <span class="percent">
                      98% </span>
                      </span>
                      <span class="progress">
                      <span style="width: 98%;" class="progress-bar progress-bar-success" aria-valuenow="98" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">
                      98% Complete </span>
                      </span>
                      </span>
                      </a>
                      </li>
                      <li>
                      <a href="#">
                      <span class="task">
                      <span class="desc">
                      Database migration </span>
                      <span class="percent">
                      10% </span>
                      </span>
                      <span class="progress progress-striped">
                      <span style="width: 10%;" class="progress-bar progress-bar-warning" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">
                      10% Complete </span>
                      </span>
                      </span>
                      </a>
                      </li>
                      <li>
                      <a href="#">
                      <span class="task">
                      <span class="desc">
                      Web server upgrade </span>
                      <span class="percent">
                      58% </span>
                      </span>
                      <span class="progress progress-striped">
                      <span style="width: 58%;" class="progress-bar progress-bar-info" aria-valuenow="58" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">
                      58% Complete </span>
                      </span>
                      </span>
                      </a>
                      </li>
                      <li>
                      <a href="#">
                      <span class="task">
                      <span class="desc">
                      Mobile development </span>
                      <span class="percent">
                      85% </span>
                      </span>
                      <span class="progress progress-striped">
                      <span style="width: 85%;" class="progress-bar progress-bar-success" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">
                      85% Complete </span>
                      </span>
                      </span>
                      </a>
                      </li>
                      <li>
                      <a href="#">
                      <span class="task">
                      <span class="desc">
                      New UI release </span>
                      <span class="percent">
                      18% </span>
                      </span>
                      <span class="progress progress-striped">
                      <span style="width: 18%;" class="progress-bar progress-bar-important" aria-valuenow="18" aria-valuemin="0" aria-valuemax="100">
                      <span class="sr-only">
                      18% Complete </span>
                      </span>
                      </span>
                      </a>
                      </li>
                      </ul>
                      </li>
                      <li class="external">
                      <a href="#">See all tasks <i class="fa fa-angle-right"></i></a>
                      </li>
                      </ul>
                      </li>
                      <!-- END TODO DROPDOWN -->
                     */ ?>
                    <li class="devider">
                        &nbsp;
                    </li>
                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <li class="dropdown user">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <span class="username"> <?php echo SFUser::$login ?> </span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="/admin/account/"><i class="fa fa-user"></i> Аккаунт</a> 
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="?logout"><i class="fa fa-key"></i> Выйти</a>
                            </li>
                        </ul>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                </ul>
                <!-- END TOP NAVIGATION MENU -->
            </div>  
            <!-- END TOP NAVIGATION BAR -->
        </div>
        <!-- END HEADER -->
        <div class="clearfix">
        </div>
        <!-- BEGIN CONTAINER -->
        <div class="page-container">

            <?php SFAdminPage::position('menu') ?>

            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <div class="page-content">

                    <!--                    <h3 class="page-title">
                    <?php echo SFAdminCore::menuCurItem('name') ?> <small></small>
                                        </h3>-->
                    <div class="page-bar">
                        <?php SFAdminPage::position('breadcrumbs') ?>
                    </div>

                    <?php AdminPlugAlert::output() ?>

                    <?php SFAdminPage::position('content-before') ?>
                    <?php SFAdminPage::content() ?>

                </div>
            </div>

            <!-- END CONTENT -->
        </div>
        <!-- END CONTAINER -->
        <!-- BEGIN FOOTER -->
        <div class="footer">
            <div class="footer-inner">
                2015 &copy; Simplex Admin 2.0
            </div>
            <div class="footer-tools">
                <span class="go-top">
                    <i class="fa fa-angle-up"></i>
                </span>
            </div>
        </div>



        <?php SFAdminPage::position('absolute') ?>



        <?php // SFDB::debug($GLOBALS['time_start']);  ?>
    </body>
</html>