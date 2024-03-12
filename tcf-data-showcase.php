<?php
/**
 * Plugin Name: Data Showcase
 * Description: Seamlessly integrate with external APIs to dynamically fetch and elegantly display real-time data on your website.
 * Version: 1.0.0
 * Requires at least: 4.6
 * Requires PHP: 7.0
 * Author: Hakob Hakobyan
 * License: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tcfdatashowcase
 */

final class TCFDATASHOWCASE {
  protected static $_instance = null;
  public string $prefix = "tcfdatashowcase";
  public string $nicename = "Data Showcase by TCF";
  public string $version = "1.0.0";
  public string $plugin_url = '';
  public string $plugin_dir = '';
  public string $shortcode = 'TCF_DATA_SHOWCASE';

  public $options;

  /**
   * Ensures only one instance is loaded or can be loaded.
   *
   * @return  self|null
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function __construct() {
    $this->plugin_url = plugins_url(plugin_basename(dirname(__FILE__)));
    $this->plugin_dir = WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__));

    $this->add_actions();

    require_once($this->plugin_dir . '/includes/admin_page.php');
    new TCFDATASHOWCASE_ADMIN_PAGE($this);

    require_once($this->plugin_dir . '/includes/api.php');
    $api = new TCFDATASHOWCASE_API($this);

    require_once($this->plugin_dir . '/includes/shortcode.php');
    new TCFDATASHOWCASE_SHORTCODE($this, $api);

    require_once($this->plugin_dir . '/includes/gutenberg.php');
    new TCFDATASHOWCASE_GUTENBERG($this);
  }

  /**
   * Add actions.
   *
   * @return void
   */
  private function add_actions(): void {
    // Register styles.
    add_action('wp_enqueue_scripts', array($this, 'register_frontend_scripts'));

    // Register activation/deactivation hooks.
    register_activation_hook(__FILE__, array($this, 'activate'));
    register_deactivation_hook( __FILE__, array($this, 'deactivate'));
  }

  /**
   * Register styles to enqueue later.
   *
   * @return void
   */
  public function register_frontend_scripts() {
    wp_register_style($this->prefix . '_general', $this->plugin_url . '/assets/css/shortcode.css', array(), $this->version);
  }

  /**
   * Add default options on the plugin activate.
   *
   * @return void
   */
  public function activate(): void {
    $options = array(
      "api_key" => 0,
      "api_url" => rest_url() . $this->prefix . '/v1/data',
      "data_type" => "json",
    );
    add_option($this->prefix . '_option_name', $options);
  }

  /**
   * Remove whole data connected with the plugin on deactivate.
   *
   * @return void
   */
  public function deactivate(): void {
    // Delete options.
    delete_option($this->prefix . '_option_name');
    // Delete all cached data.
    delete_option($this->prefix . '_data');
  }
}

/**
 * Main instance of TCFDATASHOWCASE.
 *
 * @return TCFDATASHOWCASE The main instance to prevent the need to use globals.
 */
function TCFDATASHOWCASE() {
  return TCFDATASHOWCASE::instance();
}

TCFDATASHOWCASE();
