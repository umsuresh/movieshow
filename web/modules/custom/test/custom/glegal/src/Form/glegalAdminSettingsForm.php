<?php

namespace Drupal\glegal\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class glegalAdminSettingsForm.
 *
 * @package Drupal\glegal\Form
 */
class glegalAdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'glegal_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'glegal.settings',
    ];
  }

  /**
   * Module settings form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('glegal.settings');

    $form['description'] = [
      '#markup' => '<p>' . $this->t('Configuration options for display of Terms & Conditions.') . '</p>',
    ];

    $form['except_glegal'] = [
      '#type'        => 'fieldset',
      '#title'       => $this->t('Exempt User Roles'),
      '#description' => $this->t('Users with the selected roles will never be shown T&C.'),
      '#collapsible' => TRUE,
      '#collapsed'   => TRUE,
    ];

    $role_options = user_role_names(TRUE);

    $form['except_glegal']['except_roles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Exempt user roles'),
      '#options' => $role_options,
      '#default_value' => $config->get('except_roles'),
      '#description' => $this->t('Do not display Terms and Conditions check box for the selected user roles.'),
    ];

    /*$form['accept_every_group_request'] = [
    '#type'          => 'checkbox',
    '#title'         => $this->t('Ask to accept T&Cs on Group Request'),
    '#default_value' => $config->get('accept_every_group_request'),
    ];
     */

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();

    $this->configFactory->getEditable('glegal.settings')
      ->set('except_roles', $values['except_roles'])
      ->save();

    $this->messenger()->addMessage($this->t('Configuration changes have been saved.'));

    parent::submitForm($form, $form_state);

    // @todo flush only the cache elements that need to be flushed.
    drupal_flush_all_caches();
  }

}
