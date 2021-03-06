<?php

/**
 * Description of Example
 *
 * @author Andreas Kollaros
 */

class Merchant_Billing_Example extends Merchant_Billing_Gateway {
  const TEST_URL = 'https://example.com/test';
  const LIVE_URL = 'https://example.com/live';

  # The countries the gateway supports merchants from as 2 digit ISO country codes
  protected $supported_countries = array('US', 'GR');

  # The card types supported by the payment gateway
  protected $supported_cardtypes = array('visa', 'master', 'american_express', 'switch', 'solo', 'maestro');

  # The homepage URL of the gateway
  protected $homepage_url = 'http://www.example.net';

  # The display name of the gateway
  protected $display_name = 'New Gateway';

  private $options;
  private $post;

  /**
   *
   * @param array $options
   */
  public function __construct($options = array()) {
    #$this->required_options('login, password', $options);

    $this->options = $options;
  }

  /**
   *
   * @param float $money
   * @param Merchant_Billing_CreditCard $creditcard
   * @param array $options
   * @return Merchant_Billing_Response
   */
  public function authorize($money, Merchant_Billing_CreditCard $creditcard, $options=array()) {
    $this->add_invoice($options);
    $this->add_creditcard($creditcard);
    $this->add_address($creditcard, $options);
    $this->add_customer_data($options);

    return $this->commit('authonly', $money);
  }

  /**
   *
   * @param float $money
   * @param Merchant_Billing_CreditCard $creditcard
   * @param array $options
   * @return Merchant_Billing_Response
   */
  public function purchase($money, Merchant_Billing_CreditCard $creditcard, $options=array()) {
    $this->add_invoice($options);
    $this->add_creditcard($creditcard);
    $this->add_address( $options);
    $this->add_customer_data($options);

    return $this->commit('sale', $money);
  }

  /**
   *
   * @param float $money
   * @param string $authorization
   * @param array $options
   * @return Merchant_Billing_Response
   */
  public function capture($money, $authorization, $options = array()) {
    $this->post = array('authorization_id' => $authorization);
    $this->add_customer_data($options);

    return $this->commit('capture', $money);
  }

  /**
   *
   * @param string $authorization
   * @param array $options
   * @return Merchant_Billing_Response
   */
  public function void($authorization, $options = array()) {
    $this->post = array('authorization' => $authorization);
    return $this->commit('void', null);
  }

  /**
   *
   * @param float $money
   * @param string $identification
   * @param array $options
   * @return Merchant_Billing_Response
   */
  public function credit($money, $identification, $options = array()) {
     $this->post = array('authorization' => $identification);

     $this->add_invoice($options);
     return $this->commit('credit', $money);
  }
  /* Private */

  /**
   *
   * @param array $options
   */
  private function add_customer_data($options) {

  }

  /**
   *
   * @param array $options
   */
  private function add_address($options) {
    /**
     * Options key can be 'billing_address' or 'shipping address' or 'address'
     * Each of these keys must have an address array like:
     * $options['name']
     * $options['company']
     * $options['address1']
     * $options['address2']
     * $options['city']
     * $options['state']
     * $options['country']
     * $options['zip']
     * $options['phone']
     * common pattern for addres is
     * billing_address = options['billing_address'] || options['address']
     * shipping_address = options['shipping_address']
     */
  }

  /**
   *
   * @param array $options
   */
  private function add_invoice($options) {

  }

  /**
   *
   * @param Merchant_Billing_CreditCard $creditcard
   */
  
  private function add_creditcard(Merchant_Billing_CreditCard $creditcard) {

  }

  /**
   *
   * @param string $body
   */
  private function parse($body) {
    /**
     * Parse the raw data response from gateway
     */
  }

  /**
   *
   * @param string $action
   * @param float $money
   * @param array $parameters
   * @return Merchant_Billing_Response
   */
  private function commit($action, $money, $parameters) {
    $url = $this->is_test() ? self::TEST_URL : self::LIVE_URL;

    $data = $this->ssl_post($url, $this->post_data($action, $parameters));

    $response = $this->parse($data);

    $test_mode = $this->is_test();

    return new Merchant_Billing_Response($this->success_from($response), $this->message_from($response), $response, array(
        'test' => $test_mode,
        'authorization' => $response['authorization_id'],
        'fraud_review' => $this->fraud_review_from($response),
        'avs_result' => $this->avs_result_from($response),
        'cvv_result' => $response['card_code']
      )
    );
  }

  /**
   *
   * @param array $response
   * @return string
   */
  private function success_from($response) {
    return $response['success_code_from_gateway'];
  }

  /**
   *
   * @param array $response
   * @return string
   */
  private function message_from($response) {
    return $response['message_from_gateway'];
  }


  /**
   *
   * @param array $response
   * @return string
   */
  private function fraud_review_from($response) {
    
  }

  /**
   *
   * @param array $response
   * @return string
   */
  private function avs_result_from($response) {
    return array( 'code' => $response['avs_result_code'] );
  }

  /**
   *
   * @param string $action
   * @param array $parameters
   */
  private function post_data($action, $parameters = array()) {
    /**
     * Add final parameters to post data and
     * build $this->post to the format that your payment gateway understands
     */
  }

}
?>
