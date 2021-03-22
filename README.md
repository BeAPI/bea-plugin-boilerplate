# BEA Plugin Boilerplate #

## Description ##

The BEA Plugin Boilerplate serves as a foundation off of which to build your WordPress plugins.
 
## Getting Started ##

For making this plugin easily usable, you can make the given replacements please enable the case sensitive search and then :

* Search for: `bea-plugin-boilerplate` and replace with: `my-plugin`
* Search for: `BEA\PB` and replace with: `BEA\My_plugin`
* Search for: `BEA_PB_` and replace with: `MY_PLUGIN_`
* Search for: `Plugin Boilerplate` and replace with: `My plugin name`
* Search for: `init_bea_pb_plugin` and replace with: `init_my_plugin`
* Search for: `bea-pb` and replace with: `my-plugin`

Then you have to rename the `bea-plugin-boilerplate.php` to `my-plugin.php` and edit the plugin header.

### Composer ###
You need composer to autoload all your classes from the classes folder.

Use the `beapi/composer-scaffold-plugin` package that add it automatically to the composer.json file.
You can add it yourself like this :
 
```composer.json
    "autoload": {
        "psr-4": {
            "BEA\PB\\": "content/plugins/bea-plugin-boilerplate/classes/"
        }
    }
```

## Autoload ##
The autoload is based on psr-4 and handled by composer.

## Changelog ##

### 3.3.1
* 22 March 2021
* Fix Fatal Error in Model

### 3.3.0
* 15 March 2021
* Fix all PSALM errors
* Enhance the phpcs:ignore rules to be compatible with skeleton
* Use shortarray syntax
* Enforce return types
* Use InvalidArgumentException when model wrongly instaciated
* Remove thumbnail deletion on remove_post_thumnail
* Rename get_ID to get_id
* Add psalm
* Remove `_*` methods from Models

### 3.2.0
* 1 March 2021
* Introduce interfaces and abstract classes to register Gutenberg blocks
* Update copyright date.

### 3.1.1
* Fev 2021
* Rename hook : `BEA/Helpers/locate_template/templates` in `beapi_helpers_locate_template_templates` for PHPCS
* Improve PHPCS

### 3.1.0
* Jan 2021
* Update Singleton to be compatible with PHP8.0

### 3.0.0
* May 2020
* Remove autoload.php file, it's have to be on the composer.json file autoloading
* Move compatibility class to the classes directory
* Use the PSR-4 naming convention

### 2.2
* February 2019
* Remove widget feature

### 2.1.8
* August 2018
* Fix misuse of singleton in shortcode factory

### 2.1.7
* 14 June 2017
* Fix wrong use of get_object_term_cache() and php Exception

### 2.1.6
* 22 Nov 2016
* Fix Non-static method init_translations() should not be called statically

### 2.1.5
* 15 Nov 2016
* Fix method get_model using model_class in post_type

### 2.1.4
* 06 Oct 2016
* Fix textdomain load
* Add french translations

### 2.1.3
* 13 April 2016
* Fix model class name with namespace

### 2.1.2
* 16 Mar 2016
* Fix user model filename

### 2.1.1
* 6 Mar 2016
* Fix plugin version number

### 2.1.0
* 12 Feb 2016
* Add Shortcode implementation

### 2.0.1
* 11 Jan 2016
* Fix title display in widget view

### 2.0.0
* 13 Oct 2015
* Add traits

### 1.1.2
* 30 Sep 2015
* Fix widget registration

### 1.1.1
* 4 Sep 2015

### 1.1.0
* 4 Sep 2015
* Add new filter on locate_template

### 1.0.0
* 18 Feb 2016
* Initial
