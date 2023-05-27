<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="<?php echo APP_LANGUAGE_SHORT_CODE ?>" dir="<?php echo APP_LANGUAGE_TEXT_DIRECTION ?>" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<meta name = "robots" content = "noindex,nofollow">    
<title><?php echo $app_title ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1, user-scalable=no" name="viewport"/>
<meta content="" name="description"/>
<?php echo app_author_text() ?>
<meta name="MobileOptimized" content="320">
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="template/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="template/plugins/line-awesome/css/line-awesome.min.css?v=1.3.0" rel="stylesheet" type="text/css"/>
<link href="template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="template/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="template/plugins/select2/select2_conquer.css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="template/css/style-conquer.css" rel="stylesheet" type="text/css"/>
<link href="template/css/style.css" rel="stylesheet" type="text/css"/>
<link href="template/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="template/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="css/skins/<?php echo $app_skin ?>" rel="stylesheet" type="text/css" />

<script src="<?= CFG_PATH_TO_JQUERY ?>" type="text/javascript"></script>

<script type="text/javascript" src="<?= CFG_PATH_TO_JQUERY_VALIDATION ?>"></script>
<script type="text/javascript" src="<?= CFG_PATH_TO_JQUERY_VALIDATION_METHODS ?>"></script>
<?php require('js/validation/validator_messages.php'); ?> 

<script type="text/javascript" src="js/main.js?v=<?php echo PROJECT_VERSION ?>"></script>

<script>
        var app_cfg_drop_down_menu_on_hover = <?php echo CFG_DROP_DOWN_MENU_ON_HOVER ?>;
	function keep_session()
	{
	  $.ajax({url: '<?php echo url_for("dashboard/","action=keep_session") ?>'});
	}
	
	$(function(){
	   setInterval("keep_session()",600000);                                                                   
	}); 
</script>

<link rel="stylesheet" type="text/css" href="css/default.css"/>
<?php echo app_include_custom_css() ?>

<?php echo render_login_page_background() ?>
<?php echo app_recaptcha::render_js() ?>

<!-- END THEME STYLES -->
<?php echo app_favicon() ?>
</head>
<!-- BEGIN BODY -->
<body class="login">

<div class="login-fade-in"></div>

<!-- BEGIN LOGO -->
<div class="login-page-logo">

<?php
  if(is_file(DIR_FS_UPLOADS  . '/' . CFG_APP_LOGO))
  {
    if(is_image(DIR_FS_UPLOADS  . '/' . CFG_APP_LOGO))
    {
      $html = '<img src="uploads/' . CFG_APP_LOGO .  '" border="0" title="' . CFG_APP_NAME . '">';
      
      if(strlen(CFG_APP_LOGO_URL)>0)
      {
        $html = '<a href="' . CFG_APP_LOGO_URL . '" target="_new">' . $html . '</a>';
      }
      
      echo $html;
    }
  }
  else
  {
    echo CFG_APP_NAME;
  }
?>
	
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content <?php echo 'content-' . $app_action ?>" >

<?php 
  //output alerts if they exists.
  echo $alerts->output(); 
        
//include module views    
  if(is_file($path = 'modules/' . $app_module . '/views/' . $app_action . '.php'))
  {    
    require($path);
  }   
?>

</div>
<!-- END LOGIN -->


<!-- BEGIN COPYRIGHT -->
<div class="copyright">
	 <?php echo ((!is_null(CFG_APP_COPYRIGHT_NAME) and strlen(CFG_APP_COPYRIGHT_NAME))>0 ? '&copy; ' . CFG_APP_COPYRIGHT_NAME . ' ' . date('Y') . '<br>': '') ?>
     <?php echo app_powered_by_text() ?>
</div>
<!-- END COPYRIGHT -->

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="template/plugins/respond.min.js"></script>
<script src="template/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="template/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="template/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="template/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.js?v=2.2.2" type="text/javascript"></script>
<script src="template/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="template/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="template/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="template/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/maskedinput/jquery.maskedinput.js"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="template/plugins/select2/select2.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="template/scripts/app.js?v=<?php echo PROJECT_VERSION ?>" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<?php 
	if(is_ext_installed())
	{
		echo smart_input::render_js_includes();	
	}
?>

<script>
jQuery(document).ready(function() {     
  App.init();
});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>