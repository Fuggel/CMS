<?php session_start(); ?>
<?php require_once('admin/functions.php'); ?>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">CMS</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">

                <?php if (isLoggedIn()) : ?>

                    <li><a href="admin/index.php">Admin-Panel</a></li>

                <?php else : ?>
                    <li><a href="login.php">Login</a></li>

                <?php endif; ?>

                <li class="<?php echo $registration_class; ?>"><a href="registration.php">Registration</a></li>

                <?php

                if (isset($_SESSION["user_role"])) {

                    if (isset($_GET["p_id"])) {
                        $the_post_id = $_GET["p_id"];
                        echo "<li><a href='admin/posts.php?source=edit_post&p_id={$the_post_id}'>Edit Post</a></li>";
                    }
                }

                ?>

            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>