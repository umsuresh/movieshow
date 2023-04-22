<?php

namespace Drupal\coin_greetings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Birthday wish mail advanced settings.
 */
class EmployeeBigDayWishSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'coin_greetings_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'coin_greetings.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $settings = $this->config('coin_greetings.settings');
    $form['#tree'] = TRUE;
    $form['dob_site'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Birthday Wish Mail Template'),
    ];
    $form['dob_site']['title'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Subject'),
      '#default_value' => $settings->get('dob_site')['title'],
      '#required' => TRUE,
    ];
    $form['dob_site']['content'] = [
      '#type'          => 'textarea',
      '#title'         => $this->t('Message'),
      '#default_value' => $settings->get('dob_site')['content'],
      '#element_validate' => ['token_element_validate'],
      '#token_types' => ['user'],
      '#required' => TRUE,
    ];
    // Add the token tree UI .
    $form['dob_site']['token_tree'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['user'],
      '#show_restricted' => TRUE,
      '#global_types' => FALSE,
      '#weight' => 90,
    ];

    $form['doj_site'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Work Anniverdary Wish Mail Template'),
    ];
    $form['doj_site']['title'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Subject'),
      '#default_value' => $settings->get('doj_site')['title'],
      '#required' => TRUE,
    ];
    $form['doj_site']['content'] = [
      '#type'          => 'textarea',
      '#title'         => $this->t('Message'),
      '#default_value' => $settings->get('doj_site')['content'],
      '#element_validate' => ['token_element_validate'],
      '#token_types' => ['user'],
      '#required' => TRUE,
    ];
    // Add the token tree UI .
    $form['doj_site']['token_tree'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['user'],
      '#show_restricted' => TRUE,
      '#global_types' => FALSE,
      '#weight' => 90,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $settings = $this->configFactory->getEditable('coin_greetings.settings');
    $settings->set('dob_site', $form_state->getValue('dob_site'))->save();
    $settings->set('doj_site', $form_state->getValue('doj_site'))->save();
    $this->messenger()->addMessage($this->t('Configuration has been set.'));
  }

}
