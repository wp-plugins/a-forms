<?php

if ( !defined('ABSPATH') )
    die('You are not allowed to call this page directly.');

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>A Forms</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/ui/jquery.ui.core.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-includes/js/jquery/ui/jquery.ui.widget.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo site_url(); ?>/wp-content/plugins/a-forms/tinymce/tinymce.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo plugins_url("/css/style.css", __FILE__); ?>" media="all" />

  <base target="_self" />
</head>

<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
	
	<div class="panel_wrapper">
		<?php
		global $wpdb;
		$a_forms_table = $wpdb->prefix."a_form_forms";
		$aforms = $wpdb->get_results("SELECT * FROM $a_forms_table");
		?>
		<p><label for="aform">A Form</label> <select id="aform" name="aform">
			<option value=""></option>
			<?php foreach ($aforms as $aform) { ?>
				<option value="[a-form id='<?php echo(str_replace(" ", "_", strtolower($aform->ID))); ?>'][/a-form]"><?php echo($aform->form_name); ?></option>
			<?php }?>
		</select></p>
		<div class="mceActionPanel">
			<div id="cancel_aform">
				<input type="button" id="cancel" name="cancel_aform" value="<?php _e("Cancel", 'aforms'); ?>" onclick="tinyMCEPopup.close();" />
			</div>
			<div id="insert_aform">
				<input type="submit" id="insert" name="insert_aform" value="<?php _e("Insert", 'aforms'); ?>" onclick="insertAForm();" />
			</div>
		</div>
	</div>
</body>
</html>