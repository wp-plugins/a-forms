<?php
final class AdminAFormFieldsPage {
	public static function render_admin_a_form_fields_row($instance, $index) {
    $placeholder = "";
    if ($instance == null) {
      $placeholder = "ph_";
    }
    tom_add_form_field($instance, "hidden", "FID", $placeholder."FID", $placeholder."FID", array("class" => "fid"), "span", array(), array(), $index);  
    tom_add_form_field($instance, "hidden", "field_order", $placeholder."field_order", $placeholder."field_order", array("class" => "field_order"), "span", array(), array(), $index); 
    tom_add_form_field($instance, "hidden", "Section ID", $placeholder."section_id", $placeholder."section_id", array("class" => "section_id"), "span", array(), array(), $index);  
    tom_add_form_field($instance, "text", "Label", $placeholder."field_label", $placeholder."field_label", array("class" => "text"), "span", array(), array(), $index);  
    tom_add_form_field($instance, "select", "Field Type *", $placeholder."field_type", $placeholder."field_type", array("class" => "field-type text"), "span", array(), array("" => "", "text" => "text", "hidden" => "hidden", "placeholder_text" => "placeholder_text", "select" => "select", "textarea" => "textarea", "placeholder_textarea" => "placeholder_textarea", "radio" => "radio", "checkbox" => "checkbox", "file" => "file"), $index);
    ?>
    <ul class="validation-controls">
      <?php tom_add_form_field($instance, "checkbox", "", $placeholder."validation", $placeholder."validation", array(), "li", array(), array("required" => "required", "email" => "email"), $index); ?>
    </ul>

    <?php
    tom_add_form_field($instance, "hidden", "", $placeholder."value_options", $placeholder."value_options", array("class" => "value-options"), "span", array(), array(), $index);  
    ?>
    <div class="value-option-controls">
      <ul>
        <li><strong class="label">Label</strong><strong class="value">Value</strong></li>
      </ul>
      <span class="actions"><a href='#' class='add value-option'>Add</a></span>
    </div>

    <div class="file-ext-controls">
      <ul>
        <li><strong>Restrict File Extension</strong></li>
        <?php tom_add_form_field($instance, "checkbox", "", $placeholder."file_ext_allowed", $placeholder."file_ext_allowed", array(), "li", array(), array(".jpg" => ".jpg", ".png" => ".png", ".pdf" => ".pdf", ".doc" => ".doc", ".txt" => ".txt"), $index); ?>
      </ul>
    </div>

    <a href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=a-forms/a-forms.php&action=delete&controller=AFormFields&fid=<?php echo($instance->FID); ?>&form_id=<?php echo($instance->form_id); ?>" class="delete">Delete</a>
    <?php
  }
}
?>