<?php 

namespace Drupal\movie_custom\EventSubscriber;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\movie_custom\Event\UserLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\movie_custom\EventSubscriber\PermissionRedirectSubscriber;

/**
 * Class UserLoginSubscriber.
 *
 * @package Drupal\movie_custom\EventSubscriber
 */

class UserLoginSubscriber implements EventSubscriberInterface  {

    use StringTranslationTrait;

    /**
     * @var \Drupal\Core\Messenger\MessengerInterface
     */


   private $messenger;


   /**
    * @var Drupal\Core\Datetime\DateFormatterInterface
    */

    private $date_formatter;


      /**
   * LoginEventSubscriber constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   */


   public function __construct(MessengerInterface $messenger,DateFormatterInterface $date_formatter){
       $this->messanger = $messanger;
       $this->date_formatter = $date_formatter;
   }


   /**
   * @return array
   */
  public static function getSubscribedEvents() {
    return [
      UserLoginEvent::EVENT_NAME => 'onUserLogin',
    ];
  }

  /**
 * Subscribe to the user login event dispatched.
 *
 * @param \Drupal\movie_custom\Event\UserLoginEvent $event
 *   Dat event object yo.
 */
public function onUserLogin(UserLoginEvent $event) {
    $last_logged_in = $this->date_formatter->format($event->account->getLastLoginTime(), 'short');
    $username = $event->account->getAccountName();
  
    if (empty($last_logged_in)) {
      $last_logged_in = 'Never';
    }
  
    $this->messenger
      ->addStatus($this->t('<strong>Hey there</strong>: %name.',
        [
          '%name' => $username,
        ]
      ))
      ->addStatus($this->t('<strong>You last logged in</strong>: %last_logged_in',
        [
          '%last_logged_in' => $last_logged_in
        ]
      ));
  }

}
