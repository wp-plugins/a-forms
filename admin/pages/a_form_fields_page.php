<?php
final class AdminAFormFieldsPage {
	public static function render_admin_a_form_fields_row($instance, $index) {
    $placeholder = "";
    if ($instance == null) {
      $placeholder = "ph_";
    }
    AFormsTomM8::add_form_field($instance, "hidden", "FID", $placeholder."FID", $placeholder."FID", array("class" => "fid"), "span", array(), array(), $index);  
    AFormsTomM8::add_form_field($instance, "hidden", "field_order", $placeholder."field_order", $placeholder."field_order", array("class" => "field_order"), "span", array(), array(), $index); 
    AFormsTomM8::add_form_field($instance, "hidden", "Section ID", $placeholder."section_id", $placeholder."section_id", array("class" => "section_id"), "span", array(), array(), $index);  
    AFormsTomM8::add_form_field($instance, "text", "Label", $placeholder."field_label", $placeholder."field_label", array("class" => "text"), "span", array(), array(), $index);  
    AFormsTomM8::add_form_field($instance, "select", "Field Type *", $placeholder."field_type", $placeholder."field_type", array("class" => "field-type text"), "span", array(), array("" => "", "text" => "text", "hidden" => "hidden", "placeholder_text" => "placeholder_text", "select" => "select", "textarea" => "textarea", "placeholder_textarea" => "placeholder_textarea", "radio" => "radio", "checkbox" => "checkbox", "file" => "file"), $index);
    ?>
    <ul class="validation-controls">
      <?php 
        // AFormsTomM8::add_form_field($instance, "checkbox", "", $placeholder."validation", $placeholder."validation", array(), "li", array(), array("required" => "required"), $index); 

        if (isset($_REQUEST["validation"][$index])) {
          $_GET["validation"][$index] = $_REQUEST["validation"][$index];
        } else {
          $_GET["validation"][$index] = $instance->validation;
        }

        if (isset($_REQUEST["validation_category"][$index])) {
          $_GET["validation_category"][$index] = $instance->validation." ".$_REQUEST["validation_category"][$index];
        } else {
          $_GET["validation_category"][$index] = $instance->validation;
        }

        ?>
        <li>
          <input type="hidden" name="validation[<?php echo($index); ?>]" value="">
          <input type="checkbox" id="validation_validation_<?php echo($index); ?>_required" name="validation[<?php echo($index); ?>]" value="required" <?php echo(preg_match("/required/", $instance->validation) ? "checked" : ""); ?>>
          <label for="validation_validation_<?php echo($index); ?>_required">required</label>
        </li>
        <li>
          <select id="validation_category_<?php echo($index); ?>" name="validation_category[<?php echo($index); ?>]">
            <option value=""></option>
            <option <?php echo(preg_match("/email/", $instance->validation) ? "selected" : ""); ?> value="email">email</option>
            <option <?php echo(preg_match("/postcode/", $instance->validation) ? "selected" : ""); ?> value="postcode">postcode</option>
            <option <?php echo(preg_match("/phone/", $instance->validation) ? "selected" : ""); ?> value="phone">phone</option>
            <option <?php echo(preg_match("/mobile/", $instance->validation) ? "selected" : ""); ?> value="mobile">mobile</option>
          </select>
        </li>
    </ul>

    <?php
    AFormsTomM8::add_form_field($instance, "hidden", "", $placeholder."value_options", $placeholder."value_options", array("class" => "value-options"), "span", array(), array(), $index);  
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
        <?php AFormsTomM8::add_form_field($instance, "checkbox", "", $placeholder."file_ext_allowed", $placeholder."file_ext_allowed", array(), "li", array(), array(".jpg" => ".jpg", ".png" => ".png", ".pdf" => ".pdf", ".doc" => ".doc", ".txt" => ".txt"), $index); ?>
      </ul>
    </div>

    <a href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=a-forms/a-forms.php&action=delete&controller=AFormFields&fid=<?php echo($instance->FID); ?>&form_id=<?php echo($instance->form_id); ?>" class="delete">Delete</a>
    <?php
  }
}
?>