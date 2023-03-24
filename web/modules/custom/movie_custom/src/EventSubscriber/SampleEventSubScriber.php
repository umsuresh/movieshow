<?php

/**
 * @file
 * Contains \Drupal\movie_custom\ExampleEventSubScriber.
 */

namespace Drupal\movie_custom\EventSubscriber;

use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ConfigEvents;
use Drupal\movie_custom\SampleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class ExampleEventSubScriber.
 *
 * @package Drupal\movie_custom
 */
class SampleEventSubScriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    
    $events[SampleEvent::SUBMIT][] = array('doSomeAction', 800);
    return $events;

  }

  /**
   * Subscriber Callback for the event.
   * @param SampleEvent $event
   */
  public function doSomeAction(SampleEvent $event) {
    
    \Drupal::messenger()->addMessage("The Example Event has been subscribed, which has bee dispatched on submit of the form with " . $event->getReferenceID() . " as Reference");
  }

  
}