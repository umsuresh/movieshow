<?php

namespace Drupal\coin_config_form\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\media\Entity\Media;

/**
 * {@inheritdoc}
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {

    return [
      'coin_config_form.adminsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {

    return 'admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('coin_config_form.adminsettings');

    $form['wallet_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Wallet title'),
      '#description' => $this->t('Wallet title'),
      '#default_value' => $config->get('wallet_config'),
    ];

    $form['wallet_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Please attach your wallet image.'),
      '#required' => TRUE,
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png'],
      ],
      '#multiple' => FALSE,
      '#upload_location' => 'public://images',
    ];

    return parent::buildForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $image_target_id = $form_state->getValue('wallet_image');

    $this->config('coin_config_form.adminsettings')
      ->set('wallet_title', $form_state->getValue('wallet_title'))
      ->set('wallet_image', $image_target_id[0])
      ->save();

    // Create media entity with saved file.
    $image_media = Media::create(
          [
            'name' => 'Wallet image',
            'bundle' => 'image',
            'uid' => \Drupal::currentUser()->id(),
            'langcode' => 'en',
            'status' => 0,
            'field_media_image' => [
              'target_id' => $image_target_id[0],
              'alt' => $this->t('wallet image'),
              'title' => $this->t('wallet image'),
            ],
            'field_author' => 'admin',
            'field_date' => \Drupal::time()->getCurrentTime(),
          ]
      );
    $image_media->save();

  }

}
