<?php
final class AdminAFormSectionsController {

  public static function editAction() {
    AdminAFormSectionsPage::editPage();
  }

	public static function updateAction() {
		if (AdminAFormSectionsValidation::is_valid()) {
      
      $valid = tom_update_record_by_id("a_form_sections", 
        tom_get_form_query_strings("a_form_sections", array("created_at", "updated_at"), array("updated_at" => gmdate( 'Y-m-d H:i:s'))), "ID", $_POST["ID"]);

      if ($valid) {
        if ($_POST["sub_action"] == "Update") {
          $url = get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&message=Update Complete&controller=AFormSections&action=edit&id=".$_POST["ID"]."";
        } else {
          $form = tom_get_row_by_id("a_form_forms", "*", "ID", $_POST["ID"]);
          $url = get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&action=edit&id=".$form->ID."&message=Update Complete";
        }
        
        echo("<meta http-equiv='refresh' content='0;url=".$url."/'>");
        echo("<script language='javascript'>window.location='".$url."';</script>");
        exit;
      }

    }
    AdminAFormSectionsPage::editPage();
	}

  public static function newAction() {
    AdminAFormSectionsPage::newPage();
  }

	public static function createAction() {
		if (AdminAFormSectionsValidation::is_valid()) {
      $current_datetime = gmdate( 'Y-m-d H:i:s');
      $section_count = count(tom_get_results("a_form_sections", array("ID"), "", array()));
      $valid = tom_insert_record("a_form_sections", 
        tom_get_form_query_strings("a_form_sections", array("ID", "created_at", "updated_at", "section_order"), array("created_at" => $current_datetime, "section_order" => ($section_count * 2))));

      if ($valid) {
        $url = get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&message=Record Created&action=edit&id=".$_POST["form_id"]."";
        echo("<meta http-equiv='refresh' content='0;url=".$url."/'>");
        echo("<script language='javascript'>window.location='".$url."';</script>");
        exit;
      }
    }
    AdminAFormSectionsPage::newPage();
	}

	public static function deleteAction() {
		// Delete record by id.
    $url = "";
    if (isset($_GET["id"])) {
      $section = tom_get_row_by_id("a_form_sections", "*", "ID", $_GET["id"]);
      $form_id = $section->form_id;
      tom_delete_record_by_id("a_form_sections", "ID", $_GET["id"]);
      tom_delete_record_by_id("a_form_fields", "section_id", $_GET["id"]);
      $url = get_option("siteurl")."/wp-admin/admin.php?page=a-forms/a-forms.php&action=edit&id=".$form_id."&message=Record Deleted";
      
      echo("<meta http-equiv='refresh' content='0;url=".$url."/'>");
      echo("<script language='javascript'>window.location='".$url."';</script>");
    }
    exit;
	}
}
?>