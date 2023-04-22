<?php

namespace Drupal\glegal\Form;

use Drupal\Component\Render\HtmlEscapedText;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class glegalLanguageSettings.
 *
 * @package Drupal\glegal\Form
 */
class glegalLanguageSettings extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'glegal_language_settings';
  }

  /**
   * Languages administration form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $latest_header = [$this->t('Language'), $this->t('Version'), $this->t('Revision')];
    $latest_rows   = $this->glegalVersionsLatestGet();
    $rows          = [];

    foreach ($latest_rows as $language_name => $language) {
      $row    = [];
      $row[]  = new HtmlEscapedText($language_name);
      $row[]  = empty($language['version']) ? '-' : $language['version'];
      $row[]  = empty($language['revision']) ? '-' : $language['revision'];
      $rows[] = $row;
    }

    $form['latest'] = [
      '#type'  => 'details',
      '#title' => $this->t('Latest Version'),
    ];

    $form['latest']['#value'] = [
      '#type'   => 'table',
      '#header' => $latest_header,
      '#rows'   => $rows,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * Get latest version for each language.
   */
  public function glegalVersionsLatestGet($language = NULL) {
    $conditions      = [];
    $current_version = $this->database->select('glegal_conditions', 'lc')
      ->fields('lc', ['version'])
      ->orderBy('version', 'DESC')
      ->range(0, 1)
      ->execute()
      ->fetchField();

    // Get latest version for each language.
    if (empty($language)) {
      $languages = \Drupal::languageManager()->getLanguages();
      foreach ($languages as $language_id => $language) {
        $result = $this->database->select('glegal_conditions', 'lc')
          ->fields('lc')
          ->condition('version', $current_version)
          ->condition('language', $language_id)
          ->orderBy('revision', 'DESC')
          ->range(0, 1)
          ->execute()
          ->fetchAllAssoc('tc_id');
        $row    = count($result) ? (object) array_shift($result) : FALSE;

        $conditions[$language->getId()] = $this->glegalVersionsLatestGetData($row);
      }

    }
    else {
      // Get latest version for specific language.
      $result = $this->database->select('glegal_conditions', 'lc')
        ->fields('lc')
        ->condition('language', $language)
        ->groupBy('language')
        ->orderBy('version', 'DESC')
        ->range(0, 1)
        ->execute()
        ->fetchAllAssoc('tc_id');
      $row    = count($result) ? (object) array_shift($result) : FALSE;

      $conditions[$language] = $this->glegalVersionsLatestGetData($row);
    }

    return $conditions;
  }

  /**
   * Get data from T&C object.
   *
   * @param object $data
   *   T&C object.
   *
   * @return array
   *   T&C data as an array.
   */
  public function glegalVersionsLatestGetData($data) {
    $row['revision']   = $data->revision ?? '';
    $row['language']   = $data->language ?? '';
    $row['conditions'] = $data->conditions ?? '';
    $row['date']       = $data->date ?? '';
    $row['changes']    = $data->changes ?? '';

    return $row;
  }

  /**
   * Access control callback.
   *
   * Check that Locale module is enabled and user has access permission.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
  public function access(AccountInterface $account) {
    // Check permissions and combine with any custom access checking needed.
    // Pass forward parameters from the route and/or request as needed.
    if (!\Drupal::moduleHandler()->moduleExists('locale')) {
      return AccessResult::forbidden();
    }

    return AccessResult::allowed();
  }

}
