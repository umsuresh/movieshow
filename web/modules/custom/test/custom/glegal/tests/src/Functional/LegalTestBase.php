<?php

namespace Drupal\Tests\glegal\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\glegal\Entity\Conditions;
use Drupal\filter\Entity\FilterFormat;

/**
 * Provides setup and helper methods for glegal module tests.
 *
 * @group glegal
 */
abstract class glegalTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['glegal', 'filter'];

  /**
   * The user account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * Login details.
   *
   * @var array
   */
  protected $loginDetails;

  /**
   * The user ID.
   *
   * @var int
   */
  protected $uid;

  /**
   * Conditions.
   *
   * @var string
   */
  protected $conditions;

  /**
   * Conditions plain text.
   *
   * @var string
   */
  protected $conditionsPlainText;

  /**
   * {@inheritdoc}
   */
  public function setUp() {

    parent::setUp();

    // Suppress Drush output errors.
    $this->setOutputCallback(function () {
    });

    // Create Full HTML text format.
    $full_html_format = FilterFormat::create([
      'format' => 'full_html',
      'name'   => 'Full HTML',
    ]);

    $full_html_format->save();

    // Create a user.
    $this->account = $this->drupalCreateUser([]);
    // Activate user by logging in.
    $this->drupalLogin($this->account);

    // Get login details of new user.
    $this->loginDetails['name'] = $this->account->getAccountName();
    $this->loginDetails['pass'] = $this->account->pass_raw;
    $this->uid                  = $this->account->id();

    $this->drupalLogout();

    // Glegal settings.
    $language                  = 'en';
    $version                   = glegal_version('version', $language);
    $this->conditions          = '<div class="glegal-html-text">Lorem ipsum.</div>';
    $this->conditionsPlainText = 'Lorem ipsum.';

    // Create T&C.
    Conditions::create([
      'version'    => $version['version'],
      'revision'   => $version['revision'],
      'language'   => $language,
      'conditions' => $this->conditions,
      'format'     => 'full_html',
      'date'       => time(),
      'changes'    => '',
    ])->save();

  }

}
