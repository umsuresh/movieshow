<?php

namespace Drupal\glegal;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Terms & Conditions Conditions entity.
 *
 * @ingroup glegal
 */
interface gConditionsInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
