<?php

namespace Drupal\wallet\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for wallet api.
 */
class WalletController extends ControllerBase {

  /**
   * Get Current Statement.
   *
   * @return array
   *   The response of current statement.
   */
  public function getCurrentStatementData() {
    $result = $this->getCurUserPhoneNo();
    return [
      '#title' => 'Current Statement',
      '#theme' => 'current_statement',
      '#data' => $result,
    ];
  }

  /**
   * Get Last Raised Statement.
   *
   * @return array
   *   The response of last raised statement.
   */
  public function getLastRaisedStatementData() {
    $result = $this->getCurUserPhoneNo();
    return [
      '#title' => 'Last Raised Statement',
      '#theme' => 'last_raised_statement',
      '#data' => $result,
    ];
  }

  /**
   * Get Wallet Details.
   *
   * @return array
   *   The response of wallet detail.
   */
  public function getWalletDetailsData() {
    $result = $this->getCurUserPhoneNo();
    return [
      '#title' => 'Wallet Details',
      '#theme' => 'wallet_details',
      '#data' => $result,
    ];
  }

  /**
   * Get Current User Phone Number.
   *
   * @return array
   *   The response of user phone no.
   */
  public function getCurUserPhoneNo() {
    $uid = \Drupal::currentUser()->id();
    $user = User::load($uid);
    $mobile_no = $user->get('field_phone_number')->value;
    return ['mobil_no' => $mobile_no];
  }

}
