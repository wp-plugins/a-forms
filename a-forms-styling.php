<?php
	if (isset($_POST["action"]) && $_POST["action"] == "Reset") {
		aform_copy_directory(AFormsPath::normalize(dirname(__FILE__)."/css"), get_template_directory());  		
	}
	wp_enqueue_script('jquery');
	wp_register_script("a-forms", plugins_url("js/application.js", __FILE__));
  wp_enqueue_script("a-forms");

  wp_register_style("a-forms", plugins_url("admin_css/style.css", __FILE__));
  wp_enqueue_style("a-forms");

   wp_localize_script( 'a-forms', 'AFormsAjax', array(
    'ajax_url' => admin_url('admin-ajax.php')
   ));

	$css_content = file_get_contents(get_template_directory_uri()."/aforms_css/".get_option("aform_current_css_file"));
	if (isset($_POST["css_content"])) {
		$location = get_template_directory()."/aforms_css/".get_option("aform_current_css_file");
		$css_content = $_POST["css_content"];
		$css_content = str_replace('\"', "\"", $css_content);
		$css_content = str_replace("\'", '\'', $css_content);
		tom_write_to_file($css_content, $location);
		$_GET["message"] = "Update Complete";
	}

?>
<div class="wrap a-form">
<h2>A Forms - Styling</h2>
<?php
	if (isset($_GET["message"]) && $_GET["message"] != "") {
    echo("<div class='updated below-h2'><p>".$_GET["message"]."</p></div>");
  }
?>
<div class="postbox " style="display: block; ">
<div class="inside">
  <form action="" method="post">
  	<p>
  		<label for="css_file_selection">Select CSS File</label>
  		<select id="css_file_selection" name="css_file_selection">
  			<?php
  			if ($handle = opendir(get_template_directory()."/aforms_css")) {
			    /* This is the correct way to loop over the directory. */
			    while (false !== ($entry = readdir($handle))) {
			        if (preg_match("/\.css$/", $entry)) {
			        	$selected = "";
			        	if (get_option("aform_current_css_file") == $entry) {
			        		$selected = "selected";
			        	}
			        	echo "<option value='".$entry."' ".$selected.">".$entry."</option>";
			        }
			    }
			    closedir($handle);
				}
  			?>
  		</select>
  	</p>
  	<p><label for="css_content">CSS</label><textarea id="css_content" name="css_content"><?php echo($css_content); ?></textarea></p>
  	<p><input type="submit" value="Update"/></p>
  </form>

  <h2>Reset Stylesheets</h2>
  <p>If you run into any issues with the A Forms stylesheet, you can reset them by clicking on the reset button below. You will lose your current css changes though, so make sure you do a backup.</p>
  <form action="" method="post">
  	<p><input type="submit" name="action" value="Reset"/></p>
  </form>
</div>
</div>
</div>
<div class="clear"></div>
<?php tom_add_social_share_links("http://wordpress.org/extend/plugins/a-forms/"); ?>