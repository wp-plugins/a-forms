jQuery(function() {

  jQuery(document).delegate("form.a-form .next, form.a-form .prev, form.a-form .send", "click", function(e) {

    jQuery('#'+jQuery(this).parents("form").attr("id")).after("<img class='a-form-loading' src='"+AFormsAjax.base_url+"/wp-content/plugins/a-forms/images/progress.gif' />").find("fieldset.submit").hide();

    if (jQuery(this).parents("form.a-form").hasClass("ajaxified")) {
      e.preventDefault();
      var field_hash = {};
      jQuery(this).parents("form.a-form").find("input[type=text], input[type=hidden], select, input[type=radio], textarea").each(function() {
        field_hash[jQuery(this).attr("name")] = jQuery(this).val();
      });
      jQuery(this).parents("form.a-form").find("input[type=file]").each(function() {
        field_hash[jQuery(this).attr("name").replace("[]", "")] = jQuery(this).val();
      });
      jQuery(this).parents("form.a-form").find("input[type=checkbox]").each(function() {
        if (jQuery(this).prop('checked')) {
          field_hash[jQuery(this).attr("name")] = jQuery(this).attr("value");
        } else {
          field_hash[jQuery(this).attr("name")] = "";
        }
      });
      field_hash["action"] = jQuery(this).val();
      var form_id = '#'+jQuery(this).parents("form.a-form").attr("id");
      
      jQuery.ajax({
        type: "POST",
        url: window.location.href,
        data: field_hash,
        success: function(data) {
          jQuery(form_id).html(data);
          jQuery(".a-form-loading").remove();
          jQuery(form_id).find("div.working").remove().find("fieldset.submit").show();
        }
      });
      return false; 
    }

    
  });


  var optional_fields = ["select", "radio", "checkbox"];
  
  if (jQuery( "#fields_sortable" ).length > 0) {
    jQuery( "#fields_sortable" ).sortable({
      update: function( event, ui ) {
        make_fields_sortable_odd_and_even_rows();
        sort_fields();
      }
    });    
  }

  show_hide_captcha_type();
  jQuery("#include_captcha_include_captcha_1").click(show_hide_captcha_type);

  jQuery(document).delegate(".shiftable .delete", "click", function() {
    var this_record = jQuery(this);
    jQuery.ajax({
      type: 'GET',
      url: jQuery(this).attr("href")
    }).success(function() {
      this_record.parents(".shiftable:first").remove();
      make_fields_sortable_odd_and_even_rows();
      sort_fields();
    });
    return false;
  });

  jQuery(document).delegate(".delete-option", "click", function() {
    jQuery(this).parent().addClass("deleted");
    new_options = [];
    jQuery(this).parents(".value-option-controls ul").find("li:not(.deleted)").each(function() {
      if (jQuery(this).find("input[name=key]").length > 0) {
        var key = jQuery(this).find("input[name=key]").val();
        var val = jQuery(this).find("input[name=value]").val();
        if (val != "") {
          if (new_options.indexOf(key + ":" + val) < 0) {
            console.log(key + ":" + val);
            new_options.push(key + ":" + val);
          }
        } else {
          if (new_options.indexOf(key + ":" + key) < 0) {
            new_options.push(key + ":" + key);
          }
        }
      }
    });
    jQuery(this).parent().parent().parent().parent().find(".value-options").val(new_options.join(","));
    jQuery(this).parent().remove();
    return false;
  });

  jQuery(document).delegate(".add.value-option", "click", function() {
    jQuery(this).parent().parent().find("ul").append(create_option_value_row("", ""));
    return false;
  });

  jQuery("#new_form_row").click(function() {
    var row = jQuery("#fields_row_clone li").clone().appendTo('#fields_sortable');
    jQuery(row).find("input, select").each(function() {
      jQuery(this).attr("name", jQuery(this).attr("name").replace("ph_", "")+"[]");
      if (jQuery(this).attr("id")) {
        jQuery(this).attr("id", jQuery(this).attr("id").replace("ph_", "") + "_" + jQuery("#fields_sortable > li.shiftable").length);
      }
    });
    jQuery(row).find("label").each(function() {
      jQuery(this).attr("for", jQuery(this).attr("for").replace("ph_", "") + "_" + jQuery("#fields_sortable > li.shiftable").length);
    });

    jQuery.ajax({
        type: "post",
        url: AFormsAjax.ajax_url,
        data: {section_id: jQuery("#fields_sortable li.section-heading:last").attr("id").replace("section_id_", ""), field_order: jQuery("#fields_sortable > li.shiftable").length, action: "add_field_to_section"}
    }).success(function(data) {
      var tmp = data.split("::");
      jQuery("#fields_sortable > li:last").attr("id", tmp[1]);
      jQuery("#fields_sortable > li:last input.fid").val(tmp[1]);
      jQuery("#fields_sortable > li:last input.section_id").val(tmp[0]);
      jQuery(row).find(".delete").attr("href", AFormsAjax.base_url + "&action=delete&controller=AFormSections&fid="+tmp[1]+"&section_id="+tmp[0]);
      sort_fields();
    });
    jQuery("#fields_sortable > li:not(.shiftable)").remove();
    make_fields_sortable_odd_and_even_rows();
    row.find(".field-type").change();
    return false;
  });

  jQuery(document).delegate(".value-option-controls input", "change", function() {
    var new_options = [];

    jQuery(this).parent().parent().find("li").each(function() {
      if (jQuery(this).find("input[name=key]").length > 0) {
        var key = jQuery(this).find("input[name=key]").val();
        var val = jQuery(this).find("input[name=value]").val();
        if (val != "") {
          new_options.push(key + ":" + val);
        } else {
          new_options.push(key + ":" + key);
        }
      }
    });
    jQuery(this).parent().parent().parent().parent().find(".value-options").val(new_options.join(","));
  });

  jQuery(document).delegate("#fields_sortable li .field-type", "change", function() {
    var row = jQuery(this).parent().parent();
    if (optional_fields.indexOf(jQuery(this).val()) > -1) {
      row.find(".value-option-controls").show();
      row.find(".file-ext-controls").hide();
    } else if (jQuery(this).val() == "file") {
      row.find(".value-option-controls").hide();
      row.find(".file-ext-controls").show();
    } else {
      row.find(".value-option-controls").hide();
      row.find(".file-ext-controls").hide();
    }
  });

  jQuery("#css_file_selection").change(function() {
    jQuery.ajax({
      type: "post",url: AFormsAjax.ajax_url,data: {css_file_selection: jQuery("#css_file_selection").val(), action: "aform_css_file_selector"},
      success: function(response){
        jQuery("#css_content").html(response);
      }
    });
  });

  jQuery( "#sections_sortable tr" ).addClass("shiftable");

  jQuery("#fields_sortable li").each(function() {
    if (optional_fields.indexOf(jQuery(this).find(".field-type").val()) < 0) {
      jQuery(this).find(".value-option-controls").hide();
    } else {
      jQuery(this).find(".value-option-controls").show();
    }
  });

  jQuery("#fields_sortable .value-option-controls").each(function() {
    var this_control = jQuery(this);
    var val_options = jQuery(this).parent().find("input.value-options");
    if (val_options.val() != "") {
      var options = val_options.val().split(",");
      for(i=0;i<options.length;i++) {
        if (options[i].match(":")) {
          option_with_value = options[i].split(":");
          key = option_with_value[0];
          value = option_with_value[1];
          this_control.find("ul").append(create_option_value_row(key, value));
        } else {
          this_control.find("ul").append(create_option_value_row(options[i], options[i]));
        }
      }
    }
  });

  jQuery("#fields_sortable .file-ext-controls").each(function() {
    if (jQuery(this).parent().find("select.field-type").val() == "file") {
      jQuery(this).show();
    }
  });

  make_fields_sortable_odd_and_even_rows();

  jQuery("table tr:odd").addClass("odd");
  jQuery("table tr:even").addClass("even");

  jQuery("td.tracking-enabled, td.include-admin-in-emails").each(function() {
    if (jQuery.trim(jQuery(this).html()) == "1") {
      jQuery(this).html("Yes");
    } else {
      jQuery(this).html("No");
    }
  });

  try{
    jQuery("html,body").animate({
      scrollTop: (jQuery("span.error").offset().top - 100)
    }, 2000);
  } catch(e) {
    // Ignore errors.
  }

  jQuery(document).ajaxStart(function() {
    jQuery("#aform_save_and_continue_panel").append("<span>Working, please wait ...</span>");
    jQuery("#aform_save_and_continue_panel input").hide();
  });
  jQuery(document).ajaxStop(function() {
    jQuery("#aform_save_and_continue_panel span").remove();
    jQuery("#aform_save_and_continue_panel input").show();
  });
});

function show_hide_captcha_type() {
  if (jQuery("#include_captcha_include_captcha_1").is(':checked')) {
    jQuery("#captcha_type_container").show();
  } else {
    jQuery("#captcha_type_container").hide();
  }
}

function make_fields_sortable_odd_and_even_rows() {
  jQuery("#fields_sortable > li").removeClass("odd").removeClass("even");
  jQuery("#fields_sortable > li:odd").addClass("odd");
  jQuery("#fields_sortable > li:even").addClass("even");
}

function create_option_value_row(key_value, value_value) {
  return "<li><input type='text' name='key' class='text' value='"+key_value+"'/><span class='colon'>:</span><input type='text' name='value' class='text' value='"+value_value+"'/> <a href='#' class='delete-option'>Remove</a></li>";
}

function sort_fields() {
  var section_id = 0;
  var section_sort_order = 0;

  if (!jQuery("#fields_sortable li:first").hasClass("section-heading")) {
    jQuery("#fields_sortable li.section-heading:first").insertBefore(jQuery("#fields_sortable li:first"));
    make_fields_sortable_odd_and_even_rows();
  }

  jQuery("#fields_sortable > li.shiftable").each(function() {
    if (jQuery(this).hasClass("section-heading")) {
      section_id = jQuery(this).attr("id").replace("section_id_", "");
      jQuery.ajax({
        type: 'POST',
        url: AFormsAjax.sort_section_url,
        data: {ID: section_id, section_order: section_sort_order}
      });
      section_sort_order++;
    } 
    jQuery(this).find("input.section_id").val(section_id);
  });

  var index = 0;
  jQuery("#fields_sortable > li.shiftable ul.options input[type=checkbox], #fields_sortable > li.shiftable ul.options input[type=hidden]").each(function(i) {
    index = parseInt(jQuery(this).parents("li.shiftable:first").index()-1);
    jQuery(this).parents("li.shiftable:first").find(".field_order").val(parseInt(index+1));
    if (jQuery(this).attr("name")) {
      jQuery(this).attr("name", jQuery(this).attr("name").replace(/\[\d*\]/, "["+index+"]"));
    }
    if (jQuery(this).attr("id")) {
      jQuery(this).attr("id", jQuery(this).attr("id").replace(/_\d*_/, "_"+index+"_"));
    }
    if (jQuery(this).parent().find("label")) {
      jQuery(this).parent().find("label").attr("for",
        jQuery(this).parent().find("label").attr("for").replace(/_\d*_/, "_"+index+"_")
      );
    }
  });
}