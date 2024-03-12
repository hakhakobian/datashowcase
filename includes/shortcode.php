<?php

class TCFDATASHOWCASE_SHORTCODE {
  private object $obj;
  private object $api;

  public function __construct( $that, $api ) {
    $this->obj = $that;
    $this->api = $api;
    $this->add_actions();
  }

  /**
   * Actions.
   */
  private function add_actions(): void {
    add_shortcode($this->obj->shortcode, array( $this, 'content' ));
  }

  /**
   * Replace shortcode with the HTML.
   *
   * @param $params
   *
   * @return array|false|string|string[]
   */
  public function content( $params = array() ) {
    // Enqueue the registered style.
    wp_enqueue_style($this->obj->prefix . '_general');

    // Allowed values for display format.
    $allowed_values = [ 'list', 'grid' ];
    $format = isset($params['format']) && in_array($params['format'], $allowed_values) ? $params['format'] : 'list';

    // Get data making API call based on options.
    $data = $this->api->get_data();

    ob_start();
    if ( !empty($data->errors) ) {
      // In case of there is any error.
      ?>
      <strong><?php echo esc_html($data->errors['api_error'][0]); ?></strong>
      <?php
    }
    elseif ( is_string($data) ) {
      // In case of the data type is string.
      ?>
      <p><?php echo esc_html($data); ?></p>
      <?php
    }
    elseif ( is_array($data) ) {
      // In case of the data type is json, decode and show the data in grid or list depend on shortcode option.
      $divided_array = array_chunk($data, 2, TRUE);
      foreach ( $divided_array as $chunk ) {
        ?>
        <div class="tcfdatashowcase_row">
          <?php
          foreach ( $chunk as $key => $value ) {
            ?>
            <div class="tcfdatashowcase_column <?php echo esc_attr('tcfdatashowcase_' . $format); ?>">
              <p><?php echo esc_html($key); ?></p>
              <p><?php echo esc_html(is_array($value) ? (json_encode($value)) : $value); ?></p>
            </div>
            <?php
          }
          ?>
        </div>
        <?php
      }
    }

    return str_replace(array( "\r\n", "\n", "\r" ), '', ob_get_clean());
  }
}
