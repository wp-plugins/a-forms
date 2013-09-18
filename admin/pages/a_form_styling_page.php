<?php
final class AdminAFormStylingPage {

	public static function indexPage($css_content) {
	?>
		<div class="wrap">
		<h2>A Forms - Styling</h2>
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
	<?php
	}
}
?>