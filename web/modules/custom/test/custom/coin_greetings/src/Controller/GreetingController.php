<?php

namespace Drupal\coin_greetings\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Controller\ControllerBase;

/**
 *
 */
class GreetingController extends ControllerBase {

  /**
   *
   */
  public function sendGreetingMail($user_id) {
    $mailManager = \Drupal::service('plugin.manager.mail');
    // Echo $user_id;.
    $user = User::load($user_id);
    // $to = $user->getEmail();
    $to = "bharathi@unimity.com";
    $dob = $user->get('field_dob')->getValue()[0]['value'];
    $doj = $user->get('field_doj')->getValue()[0]['value'];
    $langcode = \Drupal::currentUser()->getPreferredLangcode();

    $today = new DrupalDateTime('now');
    $birthday = new DrupalDateTime($dob);
    $work = new DrupalDateTime($doj);

    $mail_config = \Drupal::config('coin_greetings.settings');

    if ($birthday->format("m-d") == $today->format("m-d")) {
      $params['message'] = $mail_config->get('dob_site')['content'];
      $params['subject'] = $mail_config->get('dob_site')['title'];

      // Send Birthday Wish.
      $dob_result = $mailManager->mail('coin_greetings', 'send_greetings', $to, $langcode, $params, NULL, TRUE);
      if ($dob_result['result'] !== TRUE) {
        \Drupal::messenger()->addStatus(t('There was a problem sending your message and it was not sent.'));
      }
      else {
        \Drupal::messenger()->addStatus(t('Your wishes has been sent.'));
      }
    }

    if ($work->format("m-d") == $today->format("m-d")) {
      $params['message'] = $mail_config->get('doj_site')['content'];
      $params['subject'] = $mail_config->get('doj_site')['title'];

      // Send Work Anniversary Wish.
      $doj_result = $mailManager->mail('coin_greetings', 'send_greetings', $to, $langcode, $params, NULL, TRUE);
      if ($doj_result['result'] !== TRUE) {
        \Drupal::messenger()->addStatus(t('There was a problem sending your message and it was not sent.'));
      }
      else {
        \Drupal::messenger()->addStatus(t('Your wishes has been sent.'));
      }
    }
    $response = new RedirectResponse("/employee-dashboard");
    $response->send();
    exit();
    return ['#markup' => TRUE];
  }

}
