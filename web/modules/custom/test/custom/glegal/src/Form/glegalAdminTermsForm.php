<?php

namespace Drupal\glegal\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\glegal\Entity\gConditions;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings form for administering content of Terms & Conditions.
 */
class glegalAdminTermsForm extends ConfigFormBase {

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * GlegalAdminTermsForm constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory,
                              ModuleHandlerInterface $module_handler,
                              LanguageManagerInterface $language_manager,
                              DateFormatterInterface $date_formatter) {
    parent::__construct($config_factory);
    $this->moduleHandler = $module_handler;
    $this->languageManager = $language_manager;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler'),
      $container->get('language_manager'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'glegal_admin_terms';
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

    $config       = $this->config('glegal.settings');
    $conditions   = glegal_get_conditions();
    $multilingual = $this->moduleHandler->moduleExists('language');

    if ($multilingual) {
      $langcode   = $this->languageManager->getCurrentLanguage()->getId();
      $conditions = glegal_get_conditions($langcode);

      foreach ($this->languageManager->getLanguages() as $key => $object) {
        $languages[$key] = $object->getName();
      }
      $language         = $langcode;
      $version_options  = [
        'version'  => $this->t('All users (new version)'),
        'revision' => $this->t('Language specific users (a revision)'),
      ];
      $version_handling = 'version';
    }
    else {
      $languages        = ['en' => $this->t('English')];
      $language         = 'en';
      $version_handling = 'version';
    }

    $form['current_tc'] = [
      '#type'  => 'fieldset',
      '#title' => $this->t('Current T&C'),
    ];

    if (empty($conditions['version'])) {
      $form['current_tc']['no_tc_message'] = [
        '#type'  => 'html_tag',
        '#tag'   => 'strong',
        '#value' => $this->t('Terms & Conditions are not being displayed to users, as no T&C have been saved.'),
      ];
    }
    else {

      $form['current_tc']['#theme'] = 'glegal_current_metadata';

      $form['current_tc']['current_version'] = [
        '#type'   => 'item',
        '#title'  => $this->t('Version'),
        '#markup' => $conditions['version'],
      ];

      $form['current_tc']['current_revision'] = [
        '#type'   => 'item',
        '#title'  => $this->t('Version'),
        '#markup' => $conditions['revision'],
      ];

      $form['current_tc']['current_language'] = [
        '#type'   => 'item',
        '#title'  => $this->t('Language'),
        '#markup' => $conditions['language'],
      ];

      $form['current_tc']['current_date'] = [
        '#type'   => 'item',
        '#title'  => $this->t('Created'),
        '#markup' => $this->dateFormatter->format($conditions['date'], 'short'),
      ];

      $form['current_tc']['multilingual'] = [
        '#type'   => 'item',
        '#markup' => $multilingual,
      ];
    }

    $form['glegal_tab'] = [
      '#type' => 'vertical_tabs',
    ];

    $form['terms_of_use'] = [
      '#type'  => 'details',
      '#title' => $this->t('Terms of use'),
      '#group' => 'glegal_tab',
    ];

    $form['terms_of_use']['conditions'] = [
      '#type'          => 'text_format',
      '#title'         => $this->t('Terms & Conditions'),
      '#default_value' => $conditions['conditions'],
      '#description'   => $this->t('Your Terms & Conditions'),
      '#format'        => $conditions['format'] ?? filter_default_format(),
      '#required'      => TRUE,
    ];

    $form['group_page'] = [
      '#type'  => 'details',
      '#title' => $this->t('Display Style Group Page'),
      '#group' => 'glegal_tab',
    ];

    $form['group_page']['group_type'] = [
      '#type'          => 'checkboxes',
      '#options'       => [
        'group_join' => $this->t('Group Join Link'),
        'group_member_req' => $this->t('Group Membership Request'),
        'group_invite' => $this->t('Group Invite'),
      ],
      '#title'         => $this->t('Group Type'),
      '#default_value' => $config->get('group_type'),
      '#description'   => $this->t(''),
    ];

    // Only display options if there's more than one language available.
    if (count($languages) > 1) {
      // Language and version handling options.
      $form['language'] = [
        '#type'  => 'details',
        '#title' => $this->t('Language'),
        '#group' => 'glegal_tab',
      ];

      $form['language']['language'] = [
        '#type'          => 'select',
        '#title'         => $this->t('Language'),
        '#options'       => $languages,
        '#default_value' => $language,
      ];

      $form['language']['version_handling'] = [
        '#type'          => 'select',
        '#title'         => $this->t('Ask To Re-accept'),
        '#description'   => $this->t('<strong>All users</strong>: all users will be asked to accept the new version of the T&C, including users who accepted a previous version.<br />
                           <strong>Language specific</strong>: only new users, and users who accepted the T&C in the same language as this new revision will be asked to re-accept.'),
        '#options'       => $version_options,
        '#default_value' => $version_handling,
      ];
    }
    else {
      $form['language']['language']         = [
        '#type'  => 'value',
        '#value' => $language,
      ];
      $form['language']['version_handling'] = [
        '#type'  => 'value',
        '#value' => $version_handling,
      ];
    }

    // Notes about changes to T&C.
    $form['changes'] = [
      '#type'        => 'details',
      '#title'       => $this->t('Explain Changes'),
      '#description' => $this->t('Explain what changes were made to the T&C since the last version. This will only be shown to users who accepted a previous version. Each line will automatically be shown as a bullet point.'),
      '#group'       => 'glegal_tab',
    ];

    $form['changes']['changes'] = [
      '#type'          => 'textarea',
      '#title'         => $this->t('Changes'),
      '#default_value' => !empty($conditions['changes']) ? $conditions['changes'] : '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();

    // Preview request, don't save anything.
    if ($form_state->getTriggeringElement()['#value'] == $this->t('Preview')) {
      return;
    }

    $this->configFactory->getEditable('glegal.settings')
      ->set('group_type', $values['group_type'])
      ->save();

    // If new conditions are different from current, enter in database.
    if ($this->glegalConditionsUpdated($values)) {
      $version = glegal_version($values['version_handling'], $values['language']);
      $uid = \Drupal::currentUser()->id();
      gConditions::create([
        'version'    => $version['version'],
        'revision'   => $version['revision'],
        'language'   => $values['language'],
        'conditions' => $values['conditions']['value'],
        'format'     => $values['conditions']['format'],
        'date'       => time(),
        'changes'    => $values['changes'],
        'uid'        => $uid,
      ])->save();

      $this->messenger()->addMessage(t('Terms & Conditions have been saved.'));
    }

    parent::submitForm($form, $form_state);

    // @todo flush only the cache elements that need to be flushed.
    drupal_flush_all_caches();

  }

  /**
   * Check if T&Cs have been updated.
   *
   * @param array $new
   *   Newly created T&Cs.
   *
   * @return bool
   *   TRUE if the newly created T&Cs are different from the current T&Cs.
   */
  protected function glegalConditionsUpdated(array $new) {

    $previous_same_language = glegal_get_conditions($new['language']);
    $previous               = glegal_get_conditions();

    if (($previous_same_language['conditions'] != $new['conditions']['value']) && ($previous['conditions'] != $new['conditions']['value'])) {
      return TRUE;
    }

    return FALSE;
  }

}
