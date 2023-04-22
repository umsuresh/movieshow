<?php

namespace Drupal\glegal\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\glegal\gConditionsInterface;
use Drupal\user\UserInterface;

/**
 * Defines the glegal Terms & Conditions entity.
 *
 * @ingroup glegal
 *
 * @ContentEntityType(
 *   id = "glegal_conditions",
 *   label = @Translation("Group Terms & Conditions"),
 *   base_table = "glegal_conditions",
 *   entity_keys = {
 *     "id" = "tc_id",
 *     "label" = "tc_id",
 *     "uuid" = "uuid",
 *     "user_id" = "user_id",
 *   },
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *   },
 * )
 */
class gConditions extends ContentEntityBase implements gConditionsInterface {

  // Implements methods defined by EntityChangedInterface.
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be manipulated
   * in the GUI. The behaviour of the widgets used can be determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Standard field, used as unique if primary index.
    $fields['tc_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Terms & Conditions.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Terms & Conditions.'))
      ->setReadOnly(TRUE);

    $fields['version'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Version'))
      ->setDescription(t('The version number of the Terms & Conditions.'));

    $fields['revision'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Revision'))
      ->setDescription(t('The revision number of the Terms & Conditions.'));

    $fields['language'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('Language code of the language this applies to.'));

    $fields['conditions'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Terms & Conditions'))
      ->setDescription(t('Terms & Conditions text.'));

    $fields['format'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Format'))
      ->setDescription(t('Input Format of Terms & Conditions text.'));

    // Owner field of the contact.
    // Entity reference field, holds the reference to the user object.
    // The view shows the user name field of the user.
    // The form presents a auto complete field for the user name.
    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User Name'))
      ->setDescription(t('The Name of the associated user.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['date'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('When the Terms & Conditions were created.'));

    $fields['changes'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Changes'))
      ->setDescription(t('Explanation of changes to T&C since last version.'));

    return $fields;
  }

}
