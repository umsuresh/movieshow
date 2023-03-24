<?php

namespace Drupal\coin_config_form\Form;

use Drupal\user\Entity\User;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * {@inheritdoc}
 */
class WalletImageConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {

    return [
      'coin_config_form.wallet_image_configuration_form',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {

    return 'wallet_image_configuration_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('coin_config_form.wallet_image_configuration_form');

    $form['wallet_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Wallet title'),
      '#description' => $this->t('Please Enter your title'),
      '#default_value' => $config->get('wallet_title'),
    ];

    // Upload image show in preview.
    $walletImage = $config->get('wallet_image');

    if (!empty($walletImage)) {
      if ($file = File::load($walletImage)) {
        $form['wallet_image1'] = [
          '#theme' => 'image_style',
          '#style_name' => 'medium',
          '#uri' => $file->getFileUri(),
        ];
      }
    }

    $form['wallet_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Please attach your wallet image.'),
      '#description' => $this->t('Allowed image types jpg, jpeg, png only'),
      '#required' => TRUE,
      '#theme' => 'image_widget',
      '#preview_image_style' => 'medium',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg png'],
      ],
      '#multiple' => FALSE,
      '#upload_location' => 'public://wallet',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $image_target_id = $form_state->getValue('wallet_image');
    if (!empty($image_target_id)) {
      $this->config('coin_config_form.wallet_image_configuration_form')
        ->set('wallet_title', $form_state->getValue('wallet_title'))
        ->set('wallet_image', $image_target_id[0])
        ->save();

      // Create media entity with saved file.
      $user_id = \Drupal::currentUser()->id();
      $get_user = User::load($user_id);
      $image_media = Media::create(
            [
              'name' => 'Wallet image',
              'bundle' => 'image',
              'uid' => \Drupal::currentUser()->id(),
              'langcode' => 'en',
              'status' => 1,
              'field_media_image' => [
                'target_id' => $image_target_id[0],
                'alt' => $this->t('wallet image'),
                'title' => $this->t('wallet image'),
              ],
              "thumbnail" => [
                "target_id" => $image_target_id[0],
                "alt" => $this->t('wallet image'),
              ],
              'field_author' => $get_user->name->value,
              'field_date' => \Drupal::time()->getCurrentTime(),
            ]
        );
      $image_media->save();
    }
  }

}
