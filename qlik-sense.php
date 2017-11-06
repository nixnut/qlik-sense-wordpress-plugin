<?php
	/*
	Plugin Name: Qlik Sense
	Plugin URI: https://github.com/yianni-ververis/qlik-sense-wordpress-plugin
	Description: a plugin to connect to Qlik Sense server and get the objects
	Version: 0.1
	Author: yianni.ververis@qlik.com
	License: MIT
	*/

    define( 'QLIK_SENSE_PLUGIN_VERSION', '1.0.1' );
    define( 'QLIK_SENSE_PLUGIN_MINIMUM_WP_VERSION', '4.0' );
    define( 'QLIK_SENSE_PLUGIN_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );

	// Get the CSS and JS from Sense
    add_action( 'wp_enqueue_scripts', 'my_enqueued_assets' );
    function my_enqueued_assets() {
        wp_enqueue_style( 'qlik-sense', 'https://'.esc_attr( get_option('qs_host') ).'/resources/autogenerated/qlik-styles.css' );
		wp_enqueue_script( 'qlik-sense', 'https://'.esc_attr( get_option('qs_host') ).'/resources/assets/external/requirejs/require.js', array(), '1.0.0' );

		// Register the script
		// wp_register_script( 'qlik-sense-js', QLIK_SENSE_PLUGIN_PLUGIN_DIR . 'index.js', array('jquery'), '1.0.0' );
		wp_register_script( 'qlik-sense-js', QLIK_SENSE_PLUGIN_PLUGIN_DIR . 'index.js', array('jquery'), '1.0.0' );

		// Localize the script with new data
		$translation_array = array(	
			'qs_host'		=> esc_attr( get_option('qs_host') ),
			'qs_prefix'		=> esc_attr( get_option('qs_prefix') ),
			'qs_id'			=> esc_attr( get_option('qs_id') ),
		);
		wp_localize_script( 'qlik-sense-js', 'vars', $translation_array );

		// Enqueued script with localized data.
		wp_enqueue_script( 'qlik-sense-js' );
	}

	function my_plugin_menu() {
		add_menu_page('My Plugin Settings', 'Qlik Sense', 'administrator', 'my-plugin-settings', 'my_plugin_settings_page', 'dashicons-admin-generic');
	}
	
	// Create the options to be saved in the Database
	add_action( 'admin_init', 'my_plugin_settings' );	
	function my_plugin_settings() {
		register_setting( 'my-plugin-settings-group', 'qs_host' );
		register_setting( 'my-plugin-settings-group', 'qs_prefix' );
		register_setting( 'my-plugin-settings-group', 'qs_id' );
	}

	// Create the Admin Setting Page
	add_action('admin_menu', 'my_plugin_menu');
	function my_plugin_settings_page() {
?>
		<div class="wrap">
			<h2>Qlik Sense Plugin Settings</h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'my-plugin-settings-group' ); ?>
				<?php do_settings_sections( 'my-plugin-settings-group' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Host</th>
						<td><input type="text" name="qs_host" value="<?php echo esc_attr( get_option('qs_host') ); ?>" /></td>
					</tr>					
					<tr valign="top">
					<th scope="row">Virtual Proxy (Prefix)</th>
					<td><input type="text" name="qs_prefix" value="<?php echo esc_attr( get_option('qs_prefix') ); ?>" /></td>
					</tr>
					
					<tr valign="top">
					<th scope="row">App ID</th>
					<td><input type="text" name="qs_id" value="<?php echo esc_attr( get_option('qs_id') ); ?>" /></td>
					</tr>
				</table>
				<?php submit_button(); ?>
				<div><a href="https://www.qlik.com/us/"><img src="<?php echo QLIK_SENSE_PLUGIN_PLUGIN_DIR . "/QlikLogo-RGB.png"?>" width="200"></a></div>
			</form>
		</div>
<?php
	}

	// Create the Html Snippet for use inside the posts/pages
	function sense_object_func( $atts ) {
		return "<div id=\"{$atts['qvid']}\" data-qvid=\"{$atts['qvid']}\" data-nointeraction=\"{$atts['nointeraction']}\" class=\"wp-qs\" style=\"height:{$atts['height']}px\"></div>";
	}
	add_shortcode( 'sense-object', 'sense_object_func' );
?>