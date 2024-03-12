<?php
class TCFDATASHOWCASE_API {
  private object $obj;
  public function __construct($that) {
    $this->obj = $that;
    // Register rout to get mock data with URL.
    add_action( 'rest_api_init', function () {
      register_rest_route( $this->obj->prefix . '/v1', '/data', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array( $this, 'mock_data'),
        'args' => array(),
        'permission_callback' => '__return_true',
      ) );
    } );
  }

  /**
   * Get data from mock JSON file included in the plugin.
   *
   * @param WP_REST_Request $request
   *
   * @return void
   */
  public function mock_data( WP_REST_Request $request ) {
    // Path to the mock API response JSON file.
    $json_file_path = $this->obj->plugin_dir . '/sample.json';

    // Check if the file exists.
    if ( !file_exists($json_file_path) ) {
      return new WP_Error('api_error', __('API response file not found.', 'tcfdatashowcase'));
    }

    // Read the JSON data from the file.
    $api_response_data = file_get_contents($json_file_path);

    // Check if reading the file was successful.
    if ( $api_response_data === FALSE ) {
      return new WP_Error('api_error', __('Failed to read API response file.', 'tcfdatashowcase'));
    }

    // Decode the JSON data.
    $api_data = json_decode($api_response_data, true);

    // Check if JSON decoding was successful.
    if ( json_last_error() !== JSON_ERROR_NONE ) {
      return new WP_Error('api_error', __('Error decoding API response JSON.', 'tcfdatashowcase'));
    }

    // Return the API response data.
    return $api_data;
  }

  /**
   * Return API response based on the parameters defined on "Data Showcase Options" page.
   *
   * @return array|mixed|string|WP_Error
   */
  public function get_data() {
    $options = get_option( $this->obj->prefix . '_option_name' );

    if ( empty($options['api_url']) ) {
      return new WP_Error('api_error', __('Error decoding API response JSON.', 'tcfdatashowcase'));
    }

    if ( empty($options['data_type']) ) {
      $options['data_type'] = 'json';
    }

    if ( $options['data_type'] == 'json' ) {
      $headers = array(
        'Content-Type' => 'application/json',
      );
    }
    else {
      $headers = array(
        'Content-Type' => 'text/plain',
      );
    }
    if ( !empty($options['api_key']) ) {
      // Set the headers including the API key.
      $headers['Authorization'] = 'Bearer ' . $options['api_key'];
    }
    // Set up the arguments for the request.
    $args = array(
      'headers' => $headers,
    );

    $cached_data = get_option($this->obj->prefix . '_data');
    if ( empty($cached_data) || empty($cached_data[$options['api_url']]) ) {
      // Make the API request if there is no cached data for the API URL.
      $response = wp_remote_get($options['api_url'], $args);
      // Check if the request was successful.
      if ( is_wp_error($response) ) {
        return $response; // Return WP_Error if the request failed.
      }
      // Retrieve the body of the response.
      $api_response_body = wp_remote_retrieve_body($response);
      if ( $options['data_type'] == 'json' ) {
        // Decode the JSON data.
        $api_data = json_decode($api_response_body, TRUE);
        // Check if JSON decoding was successful.
        if ( json_last_error() !== JSON_ERROR_NONE ) {
          return new WP_Error('api_error', __('Error decoding API response JSON.', 'tcfdatashowcase'));
        }
      }
      elseif ( $options['data_type'] == 'text' ) {
        $api_data = (string) $api_response_body;
      }
      // Cache the fetched data to reduce load times for the next time.
      $this->cache_data($options['api_url'], $api_data);
      // Return the API response data.
      return $api_data;
    }
    else {
      // Return cached data by API URL.
      return $cached_data[$options['api_url']];
    }

  }

  /**
   * Cache the fetched data to reduce load times for the next time.
   *
   * @param $api_url
   * @param $api_data
   *
   * @return void
   */
  private function cache_data($api_url, $api_data) {
    $cached_data = get_option($this->obj->prefix . '_data');
    if ( empty($cached_data) ) {
      $cached_data = array();
    }
    $cached_data[$api_url] = $api_data;
    update_option($this->obj->prefix . '_data', $cached_data);
  }
}
