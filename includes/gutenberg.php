<?php

class TCFDATASHOWCASE_GUTENBERG {
  private object $obj;

  public function __construct( $that ) {
    $this->obj = $that;
    $this->add_actions();
  }

  /**
   * Actions.
   */
  private function add_actions(): void {
    // Enqueue block editor assets for Gutenberg.
    add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
  }

  /**
   * Enqueue scripts/styles for Gutenberg.
   *
   * @return void
   */
  public function enqueue_block_editor_assets() {
    wp_enqueue_script($this->obj->prefix . '_gutenberg', $this->obj->plugin_url . '/assets/js/gutenberg.js', array( 'wp-blocks', 'wp-element' ), $this->obj->version);
    wp_localize_script($this->obj->prefix . '_gutenberg', 'tcfdatashowcase', array(
      'title' => $this->obj->nicename,
      'data' => $this->get_shortcodes(),
    ));
  }

  /**
   * Return shortcode possible values.
   *
   * @return false|string
   */
  public function get_shortcodes() {
    $data = array(
      0 => array(
        'id' => 'list',
        'title' => __('List', 'tcfdatashowcase'),
        'shortcode' => '[' . $this->obj->shortcode . ' format="list"]',
      ),
      1 => array(
        'id' => 'grid',
        'title' => __('Grid', 'tcfdatashowcase'),
        'shortcode' => '[' . $this->obj->shortcode . ' format="grid"]',
      ),
    );

    return json_encode($data);
  }
}
