<?php

namespace Drupal\wallet\Plugin\Block;

use Drupal\user\Entity\User;
use Drupal\Core\Block\BlockBase;

/**
 * Defines a Current statement block block type.
 *
 * @Block(
 *   id = "currentstatement_block",
 *   admin_label = @Translation("Current Statement block"),
 *   category = @Translation("Wallet"),
 * )
 */
class CurrentstatementBlock extends BlockBase {

  /**
   * Get the wallet api url .
   */

  protected $api_url;

  /**
   * Get Wallet API Credentails.
   */
  public function __construct() {
    $configFactory = \Drupal::configFactory();
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
    $current_user = \Drupal::currentUser();
    $user = User::load($current_user->id());
    $mobileno = '';
    if (isset($user->get('field_phone_number')->value) && !empty($user->get('field_phone_number')->value)) {
      $mobileno = $user->get('field_phone_number')->value;
    }

    $result = [
      'mobile_no' => $mobileno,
      'url' => $this->api_url,
    ];
    return [
      '#theme' => 'current_statement',
      '#data' => $result,
    ];
  }

}
