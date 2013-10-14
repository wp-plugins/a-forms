<?php
final class AdminAFormSectionsPage {

  public static function editPage() {
    AdminAFormSectionsPage::common_header();
      // Display Edit Page
      $a_form = tom_get_row_by_id("a_form_sections", "*", "ID", esc_html($_GET["id"])); ?>
      <div class="postbox " style="display: block; ">
      <div class="inside">
        <form action="" method="post">
          <?php AdminAFormSectionsPage::render_admin_a_form_sections_form($a_form, "Update"); ?>
        </form>
      </div>
      </div>
      </div>
    </div>
    <?php
  }

  public static function newPage() {
    AdminAFormSectionsPage::common_header();
      // Display New Page
      ?>
      <div class="postbox " style="display: block; ">
      <div class="inside">
        <form action="" method="post">
          <?php AdminAFormSectionsPage::render_admin_a_form_sections_form(null, "Create"); ?>
        </form>
      </div>
      </div>
      </div>
    </div>
    <?php
  }

  public static function common_header() {
    ?>
    <div class="wrap a-form">
    <h2>A Forms <a class="add-new-h2" href="<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=a-forms/a-forms.php&action=new">Add New Form</a></h2>  
    <?php

    if (isset($_GET["message"]) && $_GET["message"] != "") {
      echo("<div class='updated below-h2'><p>".($_GET["message"])."</p></div>");
    }
  }

  public static function render_admin_a_form_sections_form($instance, $action) { 
    if ($instance != null) {
      $form = tom_get_row_by_id("a_form_forms", "*", "ID", $instance->form_id);?>
      <h2><?php echo $form->form_name; ?> <a class="add-new-h2" href="<?php echo(get_option("siteurl")); ?>/wp-admin/admin.php?page=a-forms/a-forms.php&action=edit&id=<?php echo($instance->form_id); ?>">Edit Form</a></h2>
    <?php } ?>
    <input type="hidden" name="controller" value="AFormSections" />
    <?php
    tom_add_form_field($instance, "hidden", "ID", "ID", "ID", array(), "span", array("class" => "hidden"));
    tom_add_form_field($instance, "hidden", "ID", "form_id", "form_id", array(), "span", array("class" => "hidden"));
    tom_add_form_field($instance, "text", "Name *", "section_name", "section_name", array("class" => "text"), "p", array());

    $fields = tom_get_results("a_form_fields", "*", "section_id=".$instance->ID, $order_array = array("field_order ASC"), $limit = "");
    $index = 0;
    ?>
    <input type="hidden" name="action" value="<?php echo($action); ?>" />
    <p><input type="submit" name="sub_action" value="<?php echo($action); ?>" /> <?php if ($instance != null) { ?><input type="submit" name="sub_action" value="Save and Finish" /><?php } ?></p>
    <?php
  }
}
?>