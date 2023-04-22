<?php

namespace Drupal\wallet\Plugin\Block;

use Drupal\user\Entity\User;
use Drupal\Core\Block\BlockBase;
use GuzzleHttp\Exception\RequestException;
use Drupal\file\Entity\File;

/**
 * Provides a Wallet block type.
 *
 * @Block(
 *   id = "wallet_block",
 *   admin_label = @Translation("Wallet block"),
 *   category = @Translation("Custom"),
 * )
 */
class WalletBlock extends BlockBase {


  /**
   * Get the wallet api authkey .
   */

  protected $api_authkey;

  /**
   * Get the wallet api authsecret .
   */

  protected $api_authsecret;

  /**
   * Get the wallet api url .
   */

  protected $api_url;

  /**
   * Constructs a Wallet API Credentails.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $configFactory = \Drupal::configFactory();
    $this->api_authkey = $configFactory->get('WalletAPI.settings')->get('AUTHKEY');
    $this->api_authsecret = $configFactory->get('WalletAPI.settings')->get('AUTHSECRET');
    $this->api_url = $configFactory->get('WalletAPI.settings')->get('URL');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get Current Logged In User ID.
    $current_user = \Drupal::currentUser();
    $user = User::load($current_user->id());
    // Get user phone no, user status & area.
    $mobileno = '';
    $user_status = '';
    $payroll_area = '';
    $wallet_config_image_url = '';
    $wallet_bal = '';
    $current_month_cons = '';
    $last_trans_date = '';
    $merchant = '';
    $wallet_status = '';

    if (isset($user->get('field_phone_number')->value) && !empty($user->get('field_phone_number')->value)
      && isset($user->get('field_user_status')->value) && !empty($user->get('field_user_status')->value)
      && isset($user->get('field_payroll_area')->value) && !empty($user->get('field_payroll_area')->value)
      ) {
      $mobileno = $user->get('field_phone_number')->value;
      $user_status = $user->get('field_user_status')->value;
      $payroll_area = $user->get('field_payroll_area')->value;
    }

    // Get wallet api credentials.
    $url = $this->api_url;
    $authkey = $this->api_authkey;
    $authsecret = $this->api_authsecret;

    // Get Wallet balance data.
    $route_type = "Wallet_balance";
    $page = 1;
    $wallet_balance = $this->getApiData($url, $authkey, $authsecret, $mobileno, $route_type, $page);
    $route_type = "last_transaction";
    $last_transaction = $this->getApiData($url, $authkey, $authsecret, $mobileno, $route_type, $page);
    $route_type = "Current_month_consumption";
    $current_month_consumption = $this->getApiData($url, $authkey, $authsecret, $mobileno, $route_type, $page);
    $route_type = "Check_account_status";
    $check_account_status = $this->getApiData($url, $authkey, $authsecret, $mobileno, $route_type, $page);

    if (isset($wallet_balance) && $wallet_balance->code == 200 && $wallet_balance->status == "Success" && !empty($wallet_balance->details)) {
      $wallet_bal = $wallet_balance->details->walletBalance;
    }

    if (isset($current_month_consumption) && $current_month_consumption->code == 200 && $current_month_consumption->status == "Success" && !empty($current_month_consumption->details->transactions)) {
      $current_month_cons = $current_month_consumption->details->transactions[0]->amount;
    }

    if (isset($last_transaction) && $last_transaction->code == 200 && $last_transaction->status == "Success" && !empty($last_transaction->details->transactions)) {
      $last_trans_date = $last_transaction->details->transactions->transaction_date;
      $merchant = $last_transaction->details->transactions->Merchant;
    }

    if (isset($check_account_status) && $check_account_status->code == 200 && $check_account_status->status == "Success" && !empty($check_account_status->details)) {
      $wallet_status = $check_account_status->details->wallet_Status;
    }
    // Get Non wallet image configuration.
    $config_wallte_image = \Drupal::config('coin_config_form.wallet_image_configuration_form');
    if (isset($config_wallte_image) && !empty($config_wallte_image->get('wallet_image'))) {
      $config_get_wallet_image_id = $config_wallte_image->get('wallet_image');
      $media = File::load($config_get_wallet_image_id);
      if (!empty($media)) {
        $wallet_config_image_url = file_create_url($media->get('uri')->getValue()[0]['value']);
      }
    }

    $result = ['wallet_bal' => $wallet_bal, 'current_month_cons' => $current_month_cons, 'last_trans_date' => $last_trans_date, 'merchant' => $merchant, 'wallet_status' => $wallet_status, 'mobileno' => $mobileno, 'user_status' => $user_status, 'payroll_area' => $payroll_area, 'wallet_image' => $wallet_config_image_url];
    return [
      '#theme' => 'wallet',
      '#data' => $result,
      '#attached' => [
        'library' => [
          'wallet/wallet',
        ],
      ],
    ];
  }

  /**
   *
   */
  public function getApiData($url, $authkey, $authsecret, $mobileno, $route_type) {
    $body = ['mobileNumber' => $mobileno];
    if ($route_type == "Current_month_consumption") {
      $body = ['mobileNumber' => $mobileno, 'page' => 1];
    }
    try {
      $response = \Drupal::httpClient()
        ->post(
            $url . $route_type, [
              'body' => json_encode($body),
              'headers' => [
                'Content-Type' => 'application/json',
                'authkey' => $authkey,
                'authsecret' => $authsecret,
              ],
            ]
        );
      $json_data = $response->getBody()->getContents();
      $this->wallet_log($url . $route_type, $json_data);
      return json_decode($json_data);
    }
    catch (RequestException $e) {
      \Drupal::logger('error')->error("Connection error in wallet api");
    }
  }

  /**
   * Logger  for wallet api response to stored to database.
   *
   * @param string $url
   *   API input request url.
   *
   * @param string $response
   *   API response data  $response.
   */
  public function wallet_log($url, $response) {
    try {
      $user_id = \Drupal::currentUser()->id();
      $current_time = \Drupal::time()->getCurrentTime();
      $cdate = date('Y-m-d H:i:s', $current_time);
      $database = \Drupal::database();
      $database->insert('wallet_api_data_response')->fields(
      [
        'uid' => $user_id,
        'api_url' => $url,
        'api_response' => $response,
        'updated_date' => $cdate,
      ]
      )->execute();
    }
    catch (RequestException $e) {
      $message = 'wallet log not inserted';
      \Drupal::logger('Wallet')->error($message);
    }

  }

}
