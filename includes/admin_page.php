<?php
class TCFDATASHOWCASE_ADMIN_PAGE {
  private object $obj;
  public function __construct($that) {
    $this->obj = $that;
    $this->add_actions();
  }

  /**
   * Actions.
   */
  private function add_actions(): void {
    add_action( 'admin_menu', array($this, 'admin_menu') );
    add_action( 'admin_init', array($this, 'settings_page_init') );
  }

  /**
   * Add "Data Showcase Options" submenu under the WordPress "Settings" menu.
   *
   * @return void
   */
  public function admin_menu() {
    add_options_page(__('Data Showcase Options', 'tcfdatashowcase'), __('Data Showcase Options', 'tcfdatashowcase'), 'manage_options', $this->obj->prefix . '_options', array($this, 'settings_page'));
  }

  /**
   * Add options' section and register necessary options.
   *
   * @return void
   */
  public function settings_page_init() {
    register_setting($this->obj->prefix . '_option_group', $this->obj->prefix . '_option_name', array($this, 'sanitize'));
    // Add section to show options in it.
    add_settings_section($this->obj->prefix . '_section', '', array(), $this->obj->prefix . '_options');

    // Add fields to the section.
    add_settings_field('api_key', __('API Key', 'tcfdatashowcase'), array($this, 'api_key_callback'), $this->obj->prefix . '_options', $this->obj->prefix . '_section');
    add_settings_field('api_url', __('API URL', 'tcfdatashowcase'), array($this, 'api_url_callback'), $this->obj->prefix . '_options', $this->obj->prefix . '_section');
    add_settings_field('data_type', __('Data type', 'tcfdatashowcase'), array($this, 'data_type_callback'), $this->obj->prefix . '_options', $this->obj->prefix . '_section');
  }

  /**
   * Settings page.
   *
   * @return void
   */
  public function settings_page() {
    // Get options from DB.
    $this->obj->options = get_option( $this->obj->prefix . '_option_name' );
    if ( !current_user_can('manage_options') ) {
      wp_die('You do not have sufficient permissions to access this page.');
    }
    ?>
    <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
      <form method="post" action="options.php">
        <?php
        settings_fields($this->obj->prefix . '_option_group');
        do_settings_sections($this->obj->prefix . '_options');
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  /**
   * API key option.
   *
   * @return void
   */
  public function api_key_callback() {
    $value = isset($this->obj->options['api_key']) ? $this->obj->options['api_key'] : '';

    echo '<input type="text" id="api_key" name="' . esc_attr($this->obj->prefix . '_option_name' . '[api_key]') . '" value="' . esc_attr($value) . '" />';
  }

  /**
   * API URL option.
   *
   * @return void
   */
  public function api_url_callback() {
    $value = isset($this->obj->options['api_url']) ? $this->obj->options['api_url'] : '';

    echo '<input type="text" id="api_url" name="' . esc_attr($this->obj->prefix . '_option_name' . '[api_url]') . '" value="' . esc_attr($value) . '" />';
    echo '<p class="description">' . sprintf(__('Mock JSON data URL: %s', 'tcfdatashowcase'), esc_attr(rest_url() . $this->obj->prefix . '/v1/data')) . '</p>';
  }

  /**
   * Data type option (json|text).
   *
   * @return void
   */
  public function data_type_callback() {
    $available_types = array(
      'json' => __('JSON', 'tcfdatashowcase'),
      'text' => __('Text', 'tcfdatashowcase'),
    );
    $value = isset($this->obj->options['data_type']) ? $this->obj->options['data_type'] : '';

    echo '<select id="data_type" name="' . esc_attr($this->obj->prefix . '_option_name' . '[data_type]') . '">';
    foreach ( $available_types as $key => $option ) {
      echo '<option value="' . esc_attr($key) . '" ' . ($value === $key ? 'selected' : '') . '>' . esc_html($option) . '</option>';
    }
    echo '</select>';
  }

  /**
   * Sanitize options by type.
   *
   * @param $input
   *
   * @return array
   */
  public function sanitize( $input ) {
    $new_input = array();
    if ( isset($input['api_key']) ) {
      $new_input['api_key'] = absint($input['api_key']);
    }
    if ( isset($input['api_url']) ) {
      $new_input['api_url'] = sanitize_url($input['api_url']);
    }
    if ( isset($input['data_type']) ) {
      $new_input['data_type'] = sanitize_text_field($input['data_type']);
    }

    return $new_input;
  }
}
