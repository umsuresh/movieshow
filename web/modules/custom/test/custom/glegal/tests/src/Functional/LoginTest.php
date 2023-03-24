<?php

namespace Drupal\Tests\glegal\Functional;

use Drupal\user\Entity\User;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Tests a user loging into an account and accepting new T&C.
 *
 * @group glegal
 */
class LoginTest extends glegalTestBase {

  use StringTranslationTrait;

  /**
   * Test loging in with default glegal seetings.
   */
  public function testLogin() {

    // Test with default glegal settings.
    // Log user in.
    $this->drupalPostForm('user/login', $this->loginDetails, 'Log in');

    // Check user is redirected to T&C acceptance page.
    $current_url = $this->getUrl();
    $expected_url = substr($current_url, strlen($this->baseUrl), 20);
    $this->assertEquals($expected_url, '/glegal_accept?token=');
    $this->assertResponse(200);

    // Accept T&Cs and submit form.
    $edit = ['edit-glegal-accept' => TRUE];
    $this->submitForm($edit, 'Confirm', 'glegal-login');

    // Check user is logged in.
    $account = User::load($this->uid);
    $this->drupalUserIsLoggedIn($account);

    // Check user is redirected to their user page.
    $current_url = $this->getUrl();
    $expected_url = $this->baseUrl . '/user/' . $this->uid;
    $this->assertEquals($current_url, $expected_url);
  }

  /**
   * Test if T&Cs scroll box (textarea) displays and behaves correctly.
   */
  public function testScrollBox() {

    // Set conditions to display in an un-editable HTML text area.
    $this->config('glegal.settings')
      ->set('login_terms_style', 0)
      ->set('login_container', 0)
      ->save();

    // Log user in.
    $this->drupalPostForm('user/login', $this->loginDetails, 'Log in');

    // Check T&Cs displayed as textarea.
    $readonly = $this->assertSession()
      ->elementExists('css', 'textarea#edit-conditions')
      ->getAttribute('readonly');

    // Check textarea field is not editable.
    $this->assertEquals($readonly, 'readonly');

    // Check textarea only contains plain text.
    $this->assertSession()
      ->elementTextContains('css', 'textarea#edit-conditions', $this->conditionsPlainText);
  }

  /**
   * Test if T&Cs scroll box (CSS) displays and behaves correctly.
   */
  public function testScrollBoxCss() {

    // Set conditions to display in a CSS scroll box.
    $this->config('glegal.settings')
      ->set('login_terms_style', 1)
      ->set('login_container', 0)
      ->save();

    // Log user in.
    $this->drupalPostForm('user/login', $this->loginDetails, 'Log in');

    // Check T&Cs displayed as a div with class JS will target as a scroll box.
    $this->assertSession()
      ->elementExists('css', '#glegal-login > div.glegal-terms-scroll');

    // Check scroll area contains full HTML version of T&Cs.
    $this->assertSession()
      ->elementContains('css', '#glegal-login > div.glegal-terms-scroll', $this->conditions);
  }

  /**
   * Test if T&Cs displays as HTML.
   */
  public function testHtml() {

    // Set conditions to display as HTML.
    $this->config('glegal.settings')
      ->set('login_terms_style', 2)
      ->set('login_container', 0)
      ->save();

    $this->drupalPostForm('user/login', $this->loginDetails, 'Log in');

    // Check T&Cs displayed as HTML.
    $this->assertSession()
      ->elementContains('css', '#glegal-login > div.glegal-terms', $this->conditions);
  }

  /**
   * Test if T&Cs page link displays and behaves correctly.
   */
  public function testPageLink() {

    // Set to display as a link to T&Cs.
    $this->config('glegal.settings')
      ->set('login_terms_style', 3)
      ->set('login_container', 0)
      ->save();

    $this->drupalPostForm('user/login', $this->loginDetails, $this->t('Log in'));

    // Check link display.
    $this->assertSession()
      ->elementExists('css', '#glegal-login > div.js-form-item.form-item.js-form-type-checkbox.form-type-checkbox.js-form-item-glegal-accept.form-item-glegal-accept > label > a');

    // Click the link.
    $this->click('#glegal-login > div.js-form-item.form-item.js-form-type-checkbox.form-type-checkbox.js-form-item-glegal-accept.form-item-glegal-accept > label > a');

    // Check user is on page displaying T&C.
    $current_url = $this->getUrl();
    $expected_url = $this->baseUrl . '/glegal';
    $this->assertEquals($current_url, $expected_url);
  }

}
