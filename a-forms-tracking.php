<?php

if (are_a_forms_dependencies_installed()) {
	add_action("admin_init", "register_a_form_tracking_scripts");
	function register_a_form_tracking_scripts() {
	  wp_enqueue_script('jquery');
	  wp_enqueue_script('jquery-ui-datepicker');
	  wp_register_script("a-forms", plugins_url("/js/application.js", __FILE__));
	  wp_enqueue_script('jquery-ui-sortable');
	  wp_enqueue_script("a-forms");
	  wp_register_style("a-forms", plugins_url("/admin_css/style.css", __FILE__));
	  wp_enqueue_style("a-forms");

	  wp_enqueue_style("jquery-ui-core");
	  wp_enqueue_style("jquery-ui");
	  wp_enqueue_style("jquery-ui-datepicker");
	  ?>

	  <script language="javascript">
	  jQuery(function() {
	    jQuery('.datepicker').datepicker({
	      dateFormat : 'yy-m-d',
	      showOn: "button",
	      buttonImage: "<?php echo(plugins_url( '/images/calendar.gif', __FILE__ )); ?>",
	      buttonImageOnly: true
	    });
	  });
	  </script>
  	<?php
	}
	
	AdminAFormTrackingController::indexAction();

	?>
<?php } ?>