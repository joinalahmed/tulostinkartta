=== 3DPrint Lite ===
Contributors: fuzzoid
Tags: 3D, printing, 3dprinting, 3D printing, 3dprint, printer, stl
Requires at least: 3.5
Tested up to: 4.4
Stable tag: 1.5.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin for selling 3D printing services.

== Description ==

If you have a 3D printer and wish to charge for model printing this plugin is for you. 

How it works:

Site administrator configures printers, materials and pricing in the admin. Customers upload their models, choose printer and material, see the estimated price, input their email address and comments and press the "Request a Quote" button. 
The admin receives the request notification by email and sends the quotes through the Price Request Manager or discards the quote requests. 

Features:

* Supported file types: STL (bin, ascii), OBJ, ZIP.
* Configurable printers, filaments and coatings.
* Customizable pricing: can be configured to charge per model weight, filament volume or bounding box volume.
* Large file upload support (upload chunking).
* Filament price calculator.
* Email notification.
* Price request manager.
* Translation ready.
* Responsive layout.

Demo: http://www.wp3dprinting.com/index.php/3d-print-lite-demo/

Premium version of the plugin has all features of the lite version plus:

* WooCommerce integration.
* Infill calculation.
* Model Resize feature.
* More layout options.
* Custom attributes with configurable price.
* Ability to assign different printers and materials to different products.
* Free support.
* New cool features to come.

== Installation ==

* Make sure you have WordPress installed properly (wp-content/uploads/ directory should be writeable).
* Copy 3dprint-lite folder to wp-content/plugins.
* Activate the plugin from the Plugins menu within the WordPress admin.
* On the settings page configure the main settings, printers and materials.
* Create a new page, give it a name and paste shortcode [3dprint-lite] into the page body.
* Click "Publish" button.
* Done!

== Frequently Asked Questions ==

= Does the plugin offer WooCommerce integration? =

Only the premium version - http://www.wp3dprinting.com/

= How is the printing price calculated? =

Generally the formula is: printing price = printer cost + material cost . Printer and material cost are calculated depending on the settings on the 3D Printing page. The cost can be calculated through filament volume, weight or bounding box. 

= Does the plugin check models for printability? =

The current version only checks if the model is larger than the selected printer size.

= How do I set up different price rates for different amounts of material and volume? =

For example you want to charge 0.5$ for < 200cm3, 0.4$ for >200cm3, 0.3$ for >400cm3. Instead of regular numeric price enter this formula: 0:0.5;200:0.4;400:0.3

The price and amount are delimted by ":" symbol and price-amount pairs are delimited by ";". This works on printer, material and coating prices.

= How do I translate the plugin? =

Download Poedit program (https://poedit.net/) and open languages/3dprint-lite.pot. When the translation is ready click "save" and it will generate two files .mo and .po. If you locale is en_EN name them like 3dprint-lite-en_EN.mo and 3dprint-lite-en_EN.po and copy to languages directory. (Don't know your locale code? Check your site language settings (Settings->General) and check WordPress Locale at http://wpcentral.io/internationalization/)

More details - https://premium.wpmudev.org/blog/how-to-translate-a-wordpress-plugin/


== Changelog ==


= 1.5.1 =

bug fixes

= 1.5 =

Minor js fix.

= 1.4.9 =

Cookie lifetime is configurable.

Fixed install script.

= 1.4.8 =

Materials can be assigned to coatings.

Can be loaded everywhere.

Uppercase extension fix.

Added gzip instructions to .htaccess

Fixed zip archives with utf8 files inside.

File size is checked for extracted files.

Added uninstall script.

Minor css fix.

= 1.4.3 =

Materials can be assigned to printers.

Can hide model stats.

If a printer/plane color is empty they get invisible.

= 1.4 =
Renamed Filament to Material, enabled Density field for non-filament materials.

Now it’s possible to set different price rates for different amounts of material/volume (see FAQ).

Fixed uploading of files with utf8 names.

Printers, materials and coatings are translatable now.

Button color is configurable.

Canvas stats and printer/material/coating boxes can be hidden.

Minor layout fixes.

= 1.3 =
Zip file support. Models can be upload in a zip archive (one model per archive).

Obj models with .mtl files and textures can be uploaded in a zip archive.

Better obj file support.

Added a housekeeping feature.

Minor layout fix. 

= 1.1.3 =
A bugfix
= 1.1.2 =
Bugfixes
= 1.1.1 =
A bugfix
= 1.1 =

Added coating material

Added price formatting options

Some bugfixes and layout adjustments

= 1.0.4 =
Uploader fix
= 1.0.3 =
Minor layout fix
= 1.0.2 =
Minor layout fix
= 1.0 =
* Initial release.
