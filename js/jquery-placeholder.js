jQuery(function() {
  if(navigator.appVersion.indexOf("MSIE")!=-1) {
    jQuery('[placeholder]').focus(function() {
      var input = jQuery(this);
      if (input.val() == input.attr('placeholder')) {
        input.val('');
        input.removeClass('placeholder');
      }
    }).blur(function() {
      var input = jQuery(this);
      if (input.val() == '' || input.val() == input.attr('placeholder')) {
        input.addClass('placeholder');
        input.val(input.attr('placeholder'));
      }
    }).blur();

    jQuery("form").submit(function() {
      jQuery('[placeholder]').each(function() {
        if (jQuery(this).attr("placeholder") == jQuery(this).val()) {
          jQuery(this).val("");
        }
      });
    });
  }
});