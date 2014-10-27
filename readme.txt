=== A Forms ===
Contributors: MMDeveloper, The Marketing Mix Osborne Park Perth
Donate link: 
Tags: form, contact, plugin
Requires at least: 3.3
Tested up to: 4.0
Stable tag: 2.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create easy contact forms.

== Description ==

A wordpress plugin that allows you to add a form on your website. It's quite easy to configure, some what like C Forms II, but you have the ability to get regular updates and its available on the Wordpress Plugin site.

Since version 2.0.2, you can now create advanced validation rules. There are unlimited amount of validation rules that you could have in a form which makes it difficult to manage using a UI. In A Forms you can now create a Global variable called $GLOBALS["aforms_additional_validations_for_4"] (where 4 is the id of the form) in your themes function file and set a validation array to it. 

For example lets say you have an email field, a contact field and a preferred method of contact field. The preferred method of contact field is a checkbox field with the values Email and Phone. You want to make sure your visitor fills in Phone if Phone is selected by the preferred method of contact. All you need to do is assign an array like $GLOBALS["aforms_additional_validations_for_4"]["a_form_contact_us_phone"] = "required" to $GLOBALS["aforms_additional_validations_for_4"] (remember to replace the 4 with your forms id) if the phone field is empty while the preferred method of contact is Phone.

Here is some sample code:

add_action( 'init', 'register_a_forms_additional_validations' );
function register_a_forms_additional_validations() {
	$GLOBALS["aforms_additional_validations_for_4"] = array();
	if (isset($_POST["a_form_contact_us_preferred_method_of_contact_1"]) && isset($_POST["a_form_contact_us_phone"]) && $_POST["a_form_contact_us_preferred_method_of_contact_1"] == "Phone" && $_POST["a_form_contact_us_phone"] == "") {
		$GLOBALS["aforms_additional_validations_for_4"]["a_form_contact_us_phone"] = "required";
	}
	if (isset($_POST["a_form_contact_us_preferred_method_of_contact_0"]) && isset($_POST["a_form_contact_us_email"]) && $_POST["a_form_contact_us_preferred_method_of_contact_0"] == "EMail" && $_POST["a_form_contact_us_email"] == "") {
		$GLOBALS["aforms_additional_validations_for_4"]["a_form_contact_us_email"] = "required";
	}
}

== Installation ==

1) Install WordPress 4.0 or higher

2) Download the latest from:

http://wordpress.org/extend/plugins/jquery-ui-theme 

http://wordpress.org/extend/plugins/a-forms

3) Login to WordPress admin, click on Plugins / Add New / Upload, then upload the zip file you just downloaded.

4) Activate the plugin.


Built for The Marketing Mix Perth: http://www.marketingmix.com.au


== Changelog ==

= 2.3.2 =

* Fixed phone number behaviour.

= 2.3.1 =

* UI on page/post Add A Form TinyMCE control now works in Wordpress 3.9 and higher.

= 2.3.0 =

* Add Google Event to the form submission. Assumes that you have installed Google Analytics on your site. If you haven't obviously don't fill in the Google Analytic Event fields.

= 2.2.0 =

* Fixed broken reference to wordpress library file.

= 2.1.1 =

* Fixed bug when resorting form.

= 2.1.0 =

* Added ability to validate against phone, mobile and postcode. Only allows numbers to be entered for phone, mobile and postcode.

= 2.0.2 =

* Added in advanced validation ability, please see description above. Also checkout /a-forms/validations/a_form_validation.php and look at method is_valid($atts);

= 2.0.0 =

* Removed Tom M8te dependency.

= 1.6.4 =

* Security hole found in Tom m8te and so needed to make change so that A Forms can work with Tom M8te 1.6.0.

= 1.6.3 =

* Noticed that latest version of php does not support dynamic mapping of controllers and actions, so I had to redo the route mapping for admin.

= 1.6.2 =

* Small admin css updates.

= 1.6.1 =

* Better compatible with IE.

= 1.6.0 =

* Display * on front end view if form item is required.

= 1.5.9 =

* Ability to use placeholders in the forms. Fixed up stylesheet reset button. Ability to choose if you want sections per page or in one page.

= 1.5.8 =

* Added logo, found that some clients have issues with routing to correct admin page, so fixed that. Strange cos I didn't see the issue on mine.

= 1.5.7 =

* Last fix didn't work so well with ajax on so I provided a message before and let you know you couldn't track referrer with ajax on. This release fixes that bug, so now you can use ajax and track referrer url.

= 1.5.6 =

* Fixed up referrer url in tracking.

= 1.5.5 =

* Still noticed IE issues, so not using jQuery form library like in 1.5.4. Made my own and seems to work better. Also prevent double click on form or resubmitting submitted form for both ajax and non ajax.

= 1.5.4 =

* Increased compatibility for Ajax across all IE versions. 1.5.3 seemed to be only good for IE10 for ajax.

= 1.5.3 =

* Able to add form to widget area.

= 1.5.2 =

* Ajusted securimage captcha. Updated captcha container class if it errors.

= 1.5.1 =

* Updated tracking code. Allows you to upload images with new VC code. Allows you to use AJAX.

= 1.5 =

* Refactored the code to make it more efficient and easier to manage. Should make it easier to make changes in the future.

= 1.4.4 = 

* Fixed issue with adding fields to the form. In previous versions it was slower, options were forgotten or given to something else, when the fields were rearranged. Now even if you rearrange the fields, it can work out those past problems.

= 1.4.3 =

* Fixed yet another issue with critical cross-site scripting. I've forced SiteLock to check and check, and I think its finally fixed.

= 1.4.2 =

* Fixed another issue with critical cross-site scripting.

= 1.4.1 =

* Fixed up critical cross-site scripting vulnerability. 

= 1.4.0 =

* Able to use Math generated captcha as well as Securimage Captcha.

= 1.3.3 =

* Able to now use algorithms such as SSL or TLS security algorithms when sending out emails.

= 1.3.2 =

* Found bugs where you have two or more A Forms on the same page and it didn't know which validation belonged to which form, this has now been fixed.

= 1.3.1 =

* Found that if you put quotes in the email, it would display them incorrectly. There were major issues with displaying the form information on the site, so I changed how to present information on the tracking screen.

= 1.3.0 =

* Changed how the dependency plugin code works. Fixed the from email address not showing the correct email.

= 1.2.4 =

* Added social share links to the bottom of all admin pages.

= 1.2.3 =

* Fixed a div issue and added ability to reset stylesheet.

= 1.2.2 =

* Fixed up css issues. Added search facility with tracking.

= 1.2.1 =

* When you activate, don't print useless warning error messages to the log.

= 1.2 =

* Improved code with styles. Updated admin GUI. Updated tracking page.

= 1.1 =

* Fixed section issue.

= 1.0 =

* Initial Commit

== Upgrade notice ==

= 2.3.2 =

* Fixed phone number behaviour.

= 2.3.1 =

* UI on page/post Add A Form TinyMCE control now works in Wordpress 3.9 and higher.

= 2.3.0 =

* Add Google Event to the form submission.

= 2.2.0 =

* Fixed broken reference to wordpress library file.

= 2.1.1 =

* Fixed bug when resorting form.

= 2.1.0 =

* Added ability to validate against phone, mobile and postcode. Only allows numbers to be entered for phone, mobile and postcode.

= 2.0.2 =

* Added in advanced validation ability, please see description above. Also checkout /a-forms/validations/a_form_validation.php and look at method is_valid($atts);

= 2.0.0 =

* Removed Tom M8te dependency.

= 1.6.4 =

* Security hole found in Tom m8te and so needed to make change so that A Forms can work with Tom M8te 1.6.0.

= 1.6.3 =

* Noticed that latest version of php does not support dynamic mapping of controllers and actions, so I had to redo the route mapping for admin.

= 1.6.2 =

* Small admin css updates.

= 1.6.1 =

* Better compatible with IE.

= 1.6.0 =

* Display * on front end view if form item is required.

= 1.5.9 =

* Ability to use placeholders in the forms. Fixed up stylesheet reset button. Ability to choose if you want sections per page or in one page. Need to upgrade your Tom M8te libary to use placeholder fields.

= 1.5.8 =

* Added logo, found that some clients have issues with routing to correct admin page, so fixed that. Strange cos I didn't see the issue on mine.

= 1.5.7 =

* Last fix didn't work so well with ajax on so I provided a message before and let you know you couldn't track referrer with ajax on. This release fixes that bug, so now you can use ajax and track referrer url.

= 1.5.6 =

* Fixed up referrer url in tracking.

= 1.5.5 =

* Still noticed IE issues, so not using jQuery form library like in 1.5.4. Made my own and seems to work better. Also prevent double click on form or resubmitting submitted form for both ajax and non ajax.

= 1.5.4 =

* Increased compatibility for Ajax across all IE versions. 1.5.3 seemed to be only good for IE10 for ajax.

= 1.5.3 =

* Able to add form to widget area.

= 1.5.2 =

* Ajusted securimage captcha. Updated captcha container class if it errors.

= 1.5.1 =

* Updated tracking code. Allows you to upload images with new VC code. Allows you to use AJAX.

= 1.5 =

* Refactored the code to make it more efficient and easier to manage. Should make it easier to make changes in the future.

= 1.4.4 = 

* Fixed issue with adding fields to the form. In previous versions it was slower, options were forgotten or given to something else, when the fields were rearranged. Now even if you rearrange the fields, it can work out those past problems.

= 1.4.3 =

* Fixed yet another issue with critical cross-site scripting. I've forced SiteLock to check and check, and I think its finally fixed.

= 1.4.2 =

* Fixed another issue with critical cross-site scripting.

= 1.4.1 =

* Fixed up critical cross-site scripting vulnerability. 

= 1.4.0 =

* Able to use Math generated captcha as well as Securimage Captcha.

= 1.3.3 =

* Able to now use algorithms such as SSL or TLS security algorithms when sending out emails.

= 1.3.2 =

* Found bugs where you have two or more A Forms on the same page and it didn't know which validation belonged to which form, this has now been fixed.

= 1.3.1 =

* Found that if you put quotes in the email, it would display them incorrectly. There were major issues with displaying the form information on the site, so I changed how to present information on the tracking screen.

= 1.3.0 =

* Changed how the dependency plugin code works. Fixed the from email address not showing the correct email.

= 1.2.4 =

* Added social share links to the bottom of all admin pages.

= 1.2.3 =

* Fixed a div issue and added ability to reset stylesheet.

= 1.2.2 =

* Fixed up css issues. Added search facility with tracking.

= 1.2.1 =

* When you activate, don't print useless warning error messages to the log.

= 1.2 =

* Improved code with styles. Updated admin GUI. Updated tracking page.

= 1.1 =

* Fixed section issue.

= 1.0 =

* Initial Commit