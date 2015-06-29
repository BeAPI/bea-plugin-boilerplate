bea-plugin-boilerplate
======================

The BEA Plugin Boilerplate serves as a foundation off of which to build your WordPress plugins.

# Usages
 The purpose of the classes are described at the beginning of each class
 
## Main usage
The plugin is namespaced and autoloaded for not having to load every class by the hand. But this need to have some consistency in your folder and Class naming.
You have to respect the folder structure based on the namespace, so if you are doing a namespace like this :

__\\BEA\\PB\\Notifications\\Members\\__ then a class named __Create__

You will need a folder structure like this :
__\\classes\\notifications\\members\\create.php__

### Other folders
If you need other classes autoloaded but not on the __classes__ folder you need to register a new namespace.
Go to the __autoload.php__ file and then add the following (in our case, the inc folder):
```php
$loader->addNamespace( 'BEA\PB\Inc', BEA_PB_DIR . 'inc' );
```

And then the autoloader will load this classes to !
 
## Singleton

To create a Singleton youhave to do something like this 
```php
<?php
namespace BEA\PB;

class singleton_test extends Singleton {
	function __construct() {}

	/**
	 * @return self
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
```

## Model
The model included are :
- post type model (abstract)
- user model

You need to extend the post type model \BEA\PB\Models\Model to make the things working.
You need to add the __post_type__  property and set it.

### Update meta

The Model is using ACF for getting fields or metadata_api if not available. The Model can get a key (acf key) from the normal slug of the fields. So if you have to update a meta with ACF do not worry about getting the right field ID.

There is few bundled methods like __update_meta__ this using the update_field method or update_post_meta if needed.
You can override a meta value update by defining a method with the current pattern : _update_meta\_{meta_key}_ in the class.

### Update
The update method is working like the update_meta but you only have to override the update method.
Then you have to call the \_update method to call wp_update_post

### Posts to posts
The model handle the connecting and the disconnecting from posts to posts with the methods :
 * connect
 * disconnect


### Examples : 

#### Meta updating
```php
<?php
namespace BEA\PB\Models\;

class My_Post_Type extends Model {
	protected $post_type = BEA_PB_CPT_NAME;
	
	/**
	 * Update the status meta
	 *
	 * @param $value
	 *
	 * @return bool|int
	 * @author Nicolas Juen
	 */
	public function update_meta_status( $value ) {
		
		// Make some transformations
		$value = strtolower($value):
		
		// Update the meta with the built in method
		$updated = $this->_update_meta( 'status', $value );

		//Return the updated
		return $updated;
	}
}
```