=== Labels for AnsPress ===
Contributors: nerdaryan
Donate link: https://www.paypal.com/cgi-bin/webscr?business=rah12@live.com&cmd=_xclick&item_name=Donation%20to%20AnsPress%20development
Labels: anspress, question, answer, labels, q&a, forum, stackoverflow, quora
Requires at least: 4.1.1
Tested up to: 4.4
Stable label: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add label (taxonomy) support in AnsPress

== Description ==
Support forum: http://anspress.io/questions/

Labels for AnsPress is an extension for AnsPress, which add labels (taxonomy) support for questions. An auto suggest labels fields is added in ask form. This extensions will add a page in AnsPress:

* Label page (Single label page, list questions of a specfic label)

This extension will also add labels widget, which can be used to to show popular question labels anywhere in WordPress.

== Installation ==

Simply go to WordPress plugin installer and search for labels for anspress and click on install button and then activate it.

Or if you want to install it manually simple follow this:

1. Download the extension zip file, uncompress it.
2. Upload labels-for-anspress folder to the /wp-content/plugins/ directory
3. Activate the plugin through the 'Plugins' menu in WordPress


== Changelog ==

= 3.0 =

* Made compatible with AnsPress 3.0
* Fixed pagination issue
* FIX: AnsPress breadcrumb in labels page show Questions > Categories rather than labels
* Fix: Subscribe Widget fatal error
* Improved list filter
* Fixed warning and use ap_option_groups for register options

= 2.0.0 =
* Improved loading order of extension

= 1.5.1 =
* Minor bug fixes

= 1.5 =
* Fix: error 404 when label is numeric
* Fix: Don't check minimum characters when it is zero
* Fix: not accepting utf8 words
* Language: Added de_DE
* Fix: subscribe button
* New: Added color for labels button
* New: Rebuild + Keyboard navigation + Accessibility + JS Translation
* Fix: Labels field fix
* Language: Added Turkish language
* Fix: Set subscribe button type
* New: Option to change label path string
* New: Duplicate check of labels
* New: Improved labels suggestion
* Language: Updated French .mo
* New: Added ap-labels position
* Fix: pagination

= 1.2.4 =
* fixed ajax suggestion in ask form

= 1.1 =

* Add labels field in question form …
* Hooked to ask form
* Added theme layout
* Hooked labels in question
* Added default options
