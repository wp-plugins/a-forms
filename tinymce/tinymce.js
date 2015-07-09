function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function insertAForm() {
	tagtext = jQuery("#aform").val();
	if(window.tinyMCE) {

		/* get the TinyMCE version to account for API diffs */
    var tmce_ver=window.tinyMCE.majorVersion;

    /* Check for TinyMCE version */
    if (tmce_ver >= 4) {
    	/* In TinyMCE 4, we must be use the execCommand */
      window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
    } else {
    	window.tinyMCE.execInstanceCommand(window.tinyMCE.activeEditor.id, 'mceInsertContent', false, tagtext);
    }

		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}