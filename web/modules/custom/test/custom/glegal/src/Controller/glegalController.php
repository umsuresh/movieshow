<?php

namespace Drupal\glegal\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class glegalController.
 *
 * @package Drupal\glegal\Controller
 */
class glegalController extends ControllerBase {

  /**
   * Page callback.
   *
   * @return array
   *   Render array of terms and conditions.
   */
  public function glegalPageAction() {

    $language   = $this->languageManager()->getCurrentLanguage();
    $conditions = glegal_get_conditions($language->getId());

    return [
      '#type' => 'markup',
      '#markup' => check_markup($conditions['conditions'], $conditions['format'], $conditions['language']),
    ];
  }

}
