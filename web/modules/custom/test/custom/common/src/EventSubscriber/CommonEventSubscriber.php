<?php

namespace Drupal\common\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 *  * Event subscribe funtion to redirect into login page.
 *  
 */
class CommonEventSubscriber implements EventSubscriberInterface {

  /**
   *
   */
  public function __construct() {
    $this->account = \Drupal::currentUser();
  }

  /**
   *
   */
  public function checkAuthStatus(GetResponseEvent $event) {
    $current_path = \Drupal::routeMatch()->getRouteName();
    /* Check if the current page request is not ajax request,login page and reset password page */
    $exc_path = ["user.login", "user.pass", "legal.legal", "glegal.glegal"];
    if ($this->account->isAnonymous() && !in_array($current_path, $exc_path)) {
      global $base_url;
      $url = $base_url . "/user/login";
      $response = new RedirectResponse($url, 302);
      $response->send();
    }
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['checkAuthStatus'];
    return $events;
  }

}
