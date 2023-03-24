<?php

namespace Drupal\employee_directory\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\File\FileSystemInterface;

/**
 * Employee Profile related functionalities.
 */
class EmployeeDirectoryController extends ControllerBase {

  /**
   * Get the api username .
   */

  protected $api_username;

  /**
   * Get the api password .
   */

  protected $api_password;

  /**
   * Get the api url .
   */

  protected $api_url;

  /**
   * Get the api image url .
   */

  protected $api_imageurl;

  /**
   *
   */
  public function __construct() {
    $configFactory = \Drupal::configFactory();
    $this->api_username = $configFactory->get('API.settings')->get('USERNAME');
    $this->api_password = $configFactory->get('API.settings')->get('PASSWORD');
    $this->api_url = $configFactory->get('API.settings')->get('URL');
    $this->api_imageurl = $configFactory->get('API.settings')->get('IMAGE_URL');
  }

  /**
   * Get employee profile data from API.
   *
   * @param int $emp_id
   *   User employee id.
   *
   * @param int $mobile_no
   *   User phone number.
   *
   * @param string $adid
   *   User ADID.
   */
  public function getApiDataEmpDir($emp_id, $mobile_no, $adid) {
    try {
      $type = '$format=json';
      $url = $this->api_url . "/EmpDirSet(LvEmpid='" . $emp_id . "',LvAdid='" . $adid . "',LvMobNo='" . $mobile_no . "')?" . $type;
      $response = \Drupal::httpClient()
        ->get(
          $url, [
            'auth' => [$this->api_username, $this->api_password],
          ]
        );
      $json_data = $response->getBody()->getContents();
      $result = json_decode($json_data);
      return $result;
    }
    catch (RequestException $e) {
      $message = 'Failed to call the Employee profile api';
      \Drupal::logger('employee_directory')->error($message);
      return FALSE;
    }
  }

  /**
   * Update the Bulk Employee Profile data from API.
   */
  public function getApiBulkDataEmpDir($date, $time) {
    try {
      $filter = 'filter=IV_Empid';
      $type = '$format=json';
      $datetime = $date . "T" . $time;
      $url = $this->api_url . "/EmpDbulkSet?" . $filter . " eq ' ' and IV_Date eq datetime'" . $datetime . "'&" . $type;
      $response = \Drupal::httpClient()
        ->get(
            $url, [
              'auth' => [$this->api_username, $this->api_password],
            ]
        );

      $json_data = $response->getBody()->getContents();
      return json_decode($json_data);

    }
    catch (RequestException $e) {
      $message = 'Failed to call the employee profile bulk data api.';
      \Drupal::logger('employee_directory')->error($message);
      return FALSE;
    }
  }

  /**
   * Update Employee Directory Data.
   */
  public function updateEmployeeDirectoryData($result, $uid) {
    try {
      $user = User::load($uid);
      if ($result->d->EmpId != '') {
        $user->set('field_employee_id', $result->d->EmpId);
      }
      if ($result->d->Adid != '') {
        $user->set('field_adid', $result->d->Adid);
      }
      if ($result->d->Email != '') {
        $user->set('mail', $result->d->Email);
      }
      if ($result->d->FirstName != '') {
        $user->set('field_first_name', $result->d->FirstName);
        $start_with = substr($result->d->FirstName, 0, 1);
        $term_alpha = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['name' => $start_with, 'vid' => 'alphabetical']);
        $term_alpha = reset($term_alpha);
        $user->set('field_user_name_start_with', $term_alpha->id());
      }
      if ($result->d->MiddleName != '') {
        $user->set('field_middle_name', $result->d->MiddleName);
      }
      if ($result->d->LastName != '') {
        $user->set('field_last_name', $result->d->LastName);
      }
      if ($result->d->LocationCode != '') {
        $user->set('field_location_code', $result->d->LocationCode);
      }
      if ($result->d->LocationText != '') {
        $user->set('field_location_text', $result->d->LocationText);
      }
      if ($result->d->Doj != '') {
        $doj = $this->convertSapDate($result->d->Doj);
        $user->set('field_doj', $doj);
      }
      if ($result->d->Dob != '') {
        $dob = $this->convertSapDate($result->d->Dob);
        $user->set('field_dob', $dob);
      }
      if ($result->d->UserStatus != '') {
        if ($result->d->UserStatus == '3') {
          $field_user_status = 'Active';
        }
        elseif ($result->d->UserStatus == '1') {
          $field_user_status = 'Inactive';
        }
        else {
          $field_user_status = 'Withdrawn';
        }
        $user->set('field_user_status', $field_user_status);
      }
      if ($result->d->CPhoneNo != '') {
        $user->set('field_phone_number', $result->d->CPhoneNo);
      }
      if ($result->d->PayrollArea != '') {
        $user->set('field_payroll_area', $result->d->PayrollArea);
      }
      if ($result->d->Nationality != '') {
        $user->set('field_nationality', $result->d->Nationality);
      }
      if ($result->d->CompanyCode != '') {
        $user->set('field_company_code', $result->d->CompanyCode);
      }
      if ($result->d->WalletLimit != '') {
        $user->set('field_wallet_limit', $result->d->WalletLimit);
      }
      if ($result->d->Cluster != '') {
        $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['name' => $result->d->Cluster, 'vid' => 'sap_cluster']);
        $term = reset($term);
        $cluster_id = $term->get('field_cluster')->target_id;
        $user->set('field_cluster', $cluster_id);
      }
      if ($result->d->Division != '') {
        $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['name' => $result->d->Division, 'vid' => 'sap_division']);
        if (count($term) > 0) {
          $term = reset($term);
          $division_id = $term->get('field_division')->target_id;
          $user->set('field_division', $division_id);
        }
      }
      $user->save();
      return TRUE;
    }
    catch (RequestException $e) {
      $message = 'Failed to update the employee profile data.';
      \Drupal::logger('employee_directory')->error($message);
      return FALSE;
    }
  }

  /**
   * Update Bulk Employee Data.
   */
  public function updateBulkEmployeeDirectoryData($result) {
    try {
      $userStorage = \Drupal::entityTypeManager()->getStorage('user');
      $query = $userStorage->getQuery();
      $uid = $query
        ->condition('field_employee_id', $result->Empid)
        ->execute();
      if (count($uid) == 1) {
        $uid = array_values($uid)[0];
        $user = User::load($uid);
        if ($result->Userstatus != "") {
          if ($result->UserStatus == '3') {
            $field_user_status = 'Active';
          }
          elseif ($result->UserStatus == '1') {
            $field_user_status = 'Inactive';
          }
          else {
            $field_user_status = 'Withdrawn';
          }
          $user->set('field_user_status', $field_user_status);
        }
        if ($result->PArea != "") {
          $user->set('field_company_code', $result->PArea);
        }
        /*if ($result->Plans != "") {
        $user->set('field_plans', $result->Plans);
        } */
        if ($result->RManager != "") {
          $user->set('field_response_manager', $result->RManager);
        }
        if ($result->CPhone != "") {
          $user->set('field_phone_number', $result->CPhone);
        }
        if ($result->Division != '') {
          $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['name' => $result->Division, 'vid' => 'sap_division']);
          $term = reset($term);
          $division_id = $term->get('field_division')->target_id;
          $user->set('field_division', $division_id);
        }
        $user->save();
      }
      return TRUE;
    }
    catch (RequestException $e) {
      $message = 'Failed to bulk update the Employee directory data';
      \Drupal::logger('employee_directory')->error($message);
      return FALSE;
    }
  }

  /**
   * Get Api Data Employee Structure Update.
   */
  public function getApiDataEmpStr($emp_id) {
    try {
      $filter = '$filter=IvempId';
      $type = '$format=json';
      $url = $this->api_url . "/EmpStrucSet?" . $filter . " eq '" . $emp_id . "'and IvAdid eq ' '&" . $type;
      \Drupal::logger('API URL')->error($url);
      $response = \Drupal::httpClient()
        ->get(
            $url, [
              'auth' => [$this->api_username, $this->api_password],
            ]
        );
      $json_data = $response->getBody()->getContents();
      return json_decode($json_data);
    }
    catch (RequestException $e) {
      $message = 'Failed to call the Employee Structure api';
      \Drupal::logger('employee_directory')->error($message);
      return FALSE;
    }
  }

  /**
   * Get Api Bulk Data Employee Directory Update.
   */
  public function getApiBulkDataEmpStr($date, $time) {
    try {
      $filter = 'filter=IV_Empid';
      $type = '$format=json';
      $datetime = $date . "T" . $time;
      $url = $this->api_url . "/EmpSbulkSet?" . $filter . " eq ' ' and IV_Date eq datetime'" . $datetime . "'&" . $type;
      $response = \Drupal::httpClient()
        ->get(
            $url, [
              'auth' => [$this->api_username, $this->api_password],
            ]
        );

      $json_data = $response->getBody()->getContents();
      return json_decode($json_data);
    }
    catch (RequestException $e) {
      $message = 'Failed to call the Employee Structure bulk data api.';
      \Drupal::logger('employee_directory')->error($message);
      return FALSE;
    }
  }

  /**
   * Update Employee Structure ( Reporting Line and Direct Reports ) Data.
   */
  public function updateEmployeeStructureData($result, $uid) {
    try {
      /* Load the current logged in user details */
      $current_user = User::load($uid);
      $reports_field = 0;
      $line_field = 0;
      foreach ($result as $res) {
        $reporteeID = (int) $res->MEmpid;
        /* Get the user id based on the direct reports / reporting line user employee id */
        $userStorage = \Drupal::entityTypeManager()->getStorage('user');
        $query = $userStorage->getQuery();
        $user_details = $query
          ->condition('field_employee_id', $reporteeID)
          ->execute();
        $user_details = array_values($user_details);
        // Check reportee exist or not.
        if (count($user_details) > 0) {
          /* Update the Direct Reports reference field */
          if ($res->Ind != "") {
            $reports_field = $reports_field + 1;
            /* Remove the existing direct reports field details */
            if ($reports_field == 1) {
              unset($current_user->field_direct_report);
            }
            foreach ($user_details as $user_id) {
              $current_user->field_direct_report[] = $user_id;
            }
          }
          /* Update the Reporting line reference field */
          if (empty($res->Ind)) {
            $line_field = $line_field + 1;
            /* Remove the existing reporting line field details */
            if ($line_field == 1) {
              unset($current_user->field_reporting_line);
            }
            foreach ($user_details as $user_id) {
              $current_user->field_reporting_line[] = $user_id;
            }
          }
        }
      }
      $current_user->save();
      return TRUE;
    }
    catch (RequestException $e) {
      $message = 'Reporting Line and Direct Reports user fields are not updated.';
      \Drupal::logger('User')->error($message);
      return FALSE;
    }
  }

  /**
   * Update Bulk Employee Structure Data.
   */
  public function updateBulkEmployeeStructureData($result) {
    try {
      $userStorage = \Drupal::entityTypeManager()->getStorage('user');
      $query = $userStorage->getQuery();
      $uid = $query
        ->condition('field_employee_id', $result->Empid)
        ->execute();
      if (count($uid) == 1) {
        $uid = array_values($uid);
        $update_user = User::load($uid[0]);
        $reports_field = 0;
        $line_field = 0;
        foreach ($result as $res) {
          /* Get the user id based on the direct reports / reporting line user employee id */
          $ref_user_stroage = \Drupal::entityTypeManager()->getStorage('user');
          $ref_query = $ref_user_stroage->getQuery();
          $ref_user_details = $ref_query
            ->condition('field_employee_id', $res->MEmpid)
            ->execute();
          $ref_user_details = array_values($ref_user_details);
          if (count($ref_user_details) > 0) {
            /* Update the Direct Reports reference field */
            if ($res->Ind != "") {
              $reports_field = $reports_field + 1;
              /* Remove the existing direct reports field details */
              if ($reports_field == 1) {
                unset($update_user->field_direct_report);
              }
              foreach ($ref_user_details as $user_id) {
                $update_user->field_direct_report[] = $user_id;
              }
            }
            /* Update the Reporting line reference field */
            if ($res->Ind == "") {
              $line_field = $line_field + 1;
              /* Remove the existing reporting line field details */
              if ($line_field == 1) {
                unset($update_user->field_reporting_line);
              }
              foreach ($ref_user_details as $user_id) {
                $update_user->field_reporting_line[] = $user_id;
              }
            }
          }
        }
      }
    }
    catch (RequestException $e) {
      $message = 'Failed to update the bulk data for Reporting Line and Direct Reports Fields';
      \Drupal::logger('employee_directory')->error($message);
      return FALSE;
    }
  }

  /**
   * Adding the Employee directory API response details into database.
   */
  public function addedSapApiResponse($result, $uid) {
    try {
      $current_time = \Drupal::time()
        ->getCurrentTime();
      $res = $result->d;
      $uri = $res->__metadata->uri;
      $cdate = date('Y-m-d H:i:s', $current_time);
      $database = \Drupal::database();
      $database->insert('employee_directory_api_data_updated')->fields(
            [
              'uid' => $uid,
              'api_url' => $uri,
              'api_response' => json_encode($res),
              'updated_date' => $cdate,
            ]
        )->execute();
    }
    catch (RequestException $e) {
      $message = 'Failed to adding the employee directory API response into database';
      \Drupal::logger('employee_directory')->error($message);
      return FALSE;
    }
  }

  /**
   * Adding the Employee structure API response details into database.
   */
  public function addedSapApiEmpStrResponse($result, $uid, $uri) {
    try {
      $current_time = \Drupal::time()
        ->getCurrentTime();
      $cdate = date('Y-m-d H:i:s', $current_time);
      $database = \Drupal::database();
      $database->insert('employee_structure_api_data_updated')->fields(
            [
              'uid' => $uid,
              'api_url' => $uri,
              'api_response' => json_encode($result),
              'updated_date' => $cdate,
            ]
        )->execute();
    }
    catch (RequestException $e) {
      $message = 'Failed to adding the employee structure API response into database';
      \Drupal::logger('employee_directory')->error($message);
      return FALSE;
    }
  }

  /**
   * Converting the date time format into date format.
   */
  public function convertSapDate($str) {
    $match = preg_match('/\/Date\((\d+)\)\//', $str, $date);
    $timestamp = $date[1] / 1000;
    $datetime = new DrupalDateTime();
    $datetime->setTimestamp($timestamp);
    return $datetime->format('Y-m-d');
  }

  /**
   * Get employee profile image from API.
   *
   * @param int $employee_id
   *   User employee id.
   *
   * @return array
   *   The output of user profile, or Null if it was empty.
   */
  public function getApiEmpProfile($employee_id) {
    try {
      $image = '$value';
      $profile_img_url = $this->api_imageurl . "/EmployeePhotoSet('" . $employee_id . "')/$image";
      $response = \Drupal::httpClient()
        ->get(
            $profile_img_url, [
              'auth' => [$this->api_username, $this->api_password],
            ]
        );
      if ($response->getStatusCode() == 200) {
        return ['response' => $response, 'uri' => $profile_img_url];
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('employee_directory')->error("Profile API not called");
      return FALSE;
    }
  }

  /**
   * Get the image API and covert the image into local folder and assign to user profile image.
   *
   * @param array $result
   *   User profile image.
   *
   * @param int $employee_id
   *   User employee id.
   *
   * @return array
   *
   *   The output of user profile, or Null if it was empty.
   */
  public function getProfileImage($result, $employee_id) {
    try {
      if ($result['response']->getStatusCode() == 200) {
        /* Get the Entity image id  */
        $image_convert_data = $this->api_convert_profile_image($result, $employee_id);
        $user = $image_convert_data[2];
        $existing_user_img = [$user->get('user_picture')->target_id];
        $user->set('user_picture', $image_convert_data[4]);
        if ($user->save()) {
          if (count($existing_user_img) > 0) {
            $filestorage = \Drupal::entityTypeManager()->getStorage('file');
            $file_entity = $filestorage->loadMultiple($existing_user_img);
            $filestorage->delete($file_entity);
          }
          $this->image_response_log($image_convert_data[1], $result, $image_convert_data[3], $employee_id);
        }
      }
    }
    catch (RequestException $e) {
      \Drupal::logger('employee_directory')->error($e->getMessage());
      return FALSE;
    }
  }

  /**
   * Get the image API and covert the image into local folder and assign to user profile image.
   *
   * @param array $result
   *   User profile image.
   * @param int $employee_id
   *   User employee id.
   *
   * @return array
   *   The output of user profile, or Null if it was empty.
   */
  public function profie_media_generate($result, $employee_id) {
    try {
      if ($result['response']->getStatusCode() == 200) {
        /**Get the Entity  image id  */
        $image_convert_data = $this->api_convert_profile_image($result, $employee_id);
        /*Log  for profile image API response*/
        if ($image_convert_data) {
          $log_insert = $this->image_response_log($image_convert_data[1], $result, $image_convert_data[3], $employee_id);
        }
        return $image_convert_data;
      }
    }
    catch (RequestException $e) {
      \Drupal::logger('employee_directory')->error($e->getMessage());
      return FALSE;
    }
  }

  /**
   * Get the image API and covert the image into local folder and assign to user profile image.
   *
   * @param array $result
   *   User profile image.
   * @param int $employee_id
   *   User employee id.
   *
   * @return array
   *   The output of user profile, or Null if it was empty.
   */
  public function api_convert_profile_image($result, $employee_id) {
    $json_data = $result['response']->getBody()->getContents();
    $user_id = \Drupal::currentUser()->id();
    $get_user = User::load($user_id);
    $imagebase64 = base64_decode(base64_encode($json_data));
    $image_name  = $employee_id . '_' . uniqid();
    /* Upload the files in private directory */
    $file_repository = \Drupal::service('file.repository');
    $image = $file_repository->writeData(
      $imagebase64, 'public://' . $image_name . '.png',
      FileSystemInterface::EXISTS_REPLACE
    );
    $result = ['', $user_id, $get_user, $imagebase64, $image->id()];
    return $result;

  }

  /**
   * Get the image API and covert the image into local folder and assign to user profile image.
   *
   * @param array $result
   *   User profile image.
   * @param int $user_id
   *   User user id.
   * @param string $imagebase64
   *   Image value in imagebase64 format converted .
   * @param string $employee_id
   *   User employee id.
   */
  public function image_response_log($user_id, $result, $imagebase64, $employee_id) {
    /* Adding the Employee profile image API response stored into database Log. */
    $current_time = \Drupal::time()->getCurrentTime();
    $cdate = date('Y-m-d H:i:s', $current_time);
    $database = \Drupal::database();
    $database->insert('employee_structure_api_data_updated')->fields(
          [
            'uid' => $user_id,
            'api_url' => $result['uri'],
            'profile_image_response' => $imagebase64,
            'updated_date' => $cdate,
          ]
          )->execute();
    $message = 'Profile image updated - ' . $employee_id;
    \Drupal::logger('employee_directory')->info($message);
  }

}
