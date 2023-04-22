<?php

namespace Drupal\glegal;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Terms & Conditions Accepted entity.
 *
 * @ingroup glegal
 */
interface gAcceptedInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
