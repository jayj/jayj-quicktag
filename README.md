# Jayj Quicktag

**Contributors:** Jayjdk  
**Tags:** quicktag, quicktags, editor, quick, tag, generator, import, export  
**Requires at least:** 3.3  
**Tested up to:** 4.0  
**Stable tag:** 1.3.1  

Allows you easily to add custom Quicktags to the post editor.

## Description

This plugin, Jayj Quicktag, allows you easily to add custom Quicktags to the post, page, or a custom editor.

It adds a settings page where you can add all the Quicktags you want (see Screenshots)

It supports both import and export of your Quicktags so you easily can add them to another WordPress install.

## Installation

1. Install Jayj Quicktag either via the WordPress.org plugin directory, or by uploading the files to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the settings page `Settings > Jayj Quicktag`
4. Add all the Quicktags you want!

## Frequently Asked Questions

### Does this work with older versions than 3.3?

No, sorry. Due to the [javascript changes](http://wpdevel.wordpress.com/2011/09/23/javascript-changes-in-3-3/) in 3.3 this plugin **does not** work with older versions.

### Can I add a self-closing Quicktag?

Yes. You can add a self-closing Quicktag (like &lt;hr /&gt;) by leaving "End Tag(s)" empty.

### Can I delete a Quicktag?

Yes, you can. There's two ways:

1. The first one is to click the delete button on the right.
2. The other way is to leave the "Button Label" field empty.

### Can I change the order of the Quicktags?

Yes. Since 1.2 you can change the order of the tags. Just roll over the number on the left and you should to able to drag 'n' drop the rows.

### Can I export/import Quicktags?
Yes. Version 1.1 introduced the import and export feature.

On the options page, click the "Export Quicktags" title. That should give you a textarea with some strange looking code.
You should copy/save that so you can use it to import on another site.

To find the import feature, click on the "Import Quicktags" on the same page. Paste the copied code into the textarea and click "Import Quicktags".

The Quicktags should now be imported and it doesn't overwrite the old ones.

## Screenshots

1. The Quicktag generator settings page. You can drag 'n' drop the rows to change the order.
2. A post editor with the new Quicktags

## Changelog

### 1.3.1
* Added Spanish translation by [Andrew Kurtis](http://www.webhostinghub.com/).
* Tested with WordPress 3.8

### 1.3.0
* Settings page tested with MP6
* An example quicktag is showed when adding a new quicktag
* Added a drop zone when dragging a quicktag
* The plugin name in the menu can now be translated
* Removed install function that added a example quicktag as it's not needed anymore
* Added minified version of the admin javascript
* Code cleanup

### 1.2.4
* Bug fix: Blank White screen when uninstalling the plugin

### 1.2.3
* Bug fix: Quicktags not displayed when using single quotes in the start and end tags.

### 1.2.2
* The plugin is now internationalized
* Danish translation added

### 1.2.1
* Somehow the javascript and CSS didn't get included

### 1.2
* You can now change the order of the Quicktags. Just drag 'n' drop them on the settings page
* You can easier remove Quicktags. Each row has a delete link on the right
* You can now add more than one Quicktag a time
* The settings page Javascript and CSS has been moved to seperate files

### 1.1.1
* Don't include the Quicktags javascript on pages without an editor

### 1.1
* You can now export and import Quicktags
* Fixed rare bug with Quicktags not deleting

### 1.0
* Initial Release

## Upgrade Notice

### 1.2.4
Fixes blank white screen when uninstalling the plugin

### 1.2.3
Bug fix: Quicktags not displayed when using single quotes in the start and end tags.

### 1.2
You can change the order of the tags. Easier to add and remove them.

### 1.1.1
The javascript is no longer included on all admin pages

### 1.1
You can now export and import Quicktags
