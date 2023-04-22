<?php

namespace Drupal\schemes_and_benefits_api\Plugin\views\style;

use Drupal\rest\Plugin\views\style\Serializer;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * The style plugin for serialized output formats.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "custom_serialization",
 *   title = @Translation("Schemes & Benefits API"),
 *   help = @Translation("Serializes views row data using the Serializer
 *   component."), display_types = {"data"}
 * )
 */
class CustomSerializer extends Serializer {

  /**
   * {@inheritdoc}
   */
  public function render() {

    $request_path = \Drupal::request()->getSchemeAndHttpHost();
    $media_fields = [
      "employee_schemes_banner", "friends_family_offer_banner",
    ];
    $data = [];
    if (isset($this->view->result) && !empty($this->view->result)) {

      foreach ($this->view->result as $row_index => $row) {
        $this->view->row_index = $row_index;
        $view_render = $this->view->rowPlugin->render($row);
        $view_render = json_encode($view_render);
        $rendered_data = json_decode($view_render, TRUE);

        foreach ($rendered_data as $key => $values) {
          /* Custom image & video formattter.To check media image field exist  */
          if (in_array($key, $media_fields)) {
            $media_formatted_data = $this->customMediaFormatter($key, $values);
            $rendered_data[$key] = $media_formatted_data;
          }

          /* Change video or image actual path to absolute path. */
          if ($key === "offer_description" & !empty($values)) {
            $body_summary = str_replace('src="/sites/default/files/', 'src="' . $request_path . '/sites/default/files/', $values);
            $rendered_data[$key] = $body_summary;
          }

          /* merge URL Link into single json */
          $key_data = $rendered_data['content_type'] . "_link";
          if (($rendered_data['content_type'] == "friends_family_offer"
                || $rendered_data['content_type'] == "employee_schemes")
                && $key === "link" && !empty($values)
            ) {
            $rendered_data[$key_data] = [
              "url" => $rendered_data['link'],
              "link_text" => $rendered_data['text'],
            ];
            unset($rendered_data['text']);
            unset($rendered_data['link']);
          }
          elseif ($key === "link" && empty($values)) {
            $rendered_data[$key_data] = [
              "url" => "",
              "link_text" => "",
            ];
            unset($rendered_data['text']);
            unset($rendered_data['link']);
          }
        }
        unset($rendered_data['content_type']);
        $data[] = $rendered_data;
      }
      $rows['status'] = 200;
      $rows['message'] = "Success";
      $rows['data'] = $data;
      return $this->serializer->serialize($rows, 'json', ['views_style_plugin' => $this]);
    }
    else {
      $rows = [];
      $rows['status'] = 204;
      $rows['message'] = "No Records Found";

      return $this->serializer->serialize($rows, 'json', ['views_style_plugin' => $this]);
    }

  }

  /**
   * To get media files details from db.
   */
  public function customMediaFormatter($key, $values) {

    if (!empty($values)) {
      $media_entity = Media::load($values);
      $media_type = $media_entity->bundle();
      if ($media_type === 'image') {
        $mid = $media_entity->get('field_media_image')->target_id;
        if (!empty($mid)) {
          $mname = $media_entity->get('name')->value;
          $query = \Drupal::database()->select('media__field_media_image');
          $query->condition('entity_id', $values);
          $query->fields('media__field_media_image');
          $result = $query->execute()->fetchAll();
          if (!empty($result)) {
            $malt = $result[0]->field_media_image_alt;
          }
          else {
            $malt = $media_entity->get('field_media_image')->alt;
          }
          /**
           * Get the File Details.
           *
           * @var object
           */
          $query = \Drupal::database()->select('file_managed');
          $query->condition('fid', $mid);
          $query->fields('file_managed');
          $result22 = $query->execute()->fetchAll();
          if (!empty($result22)) {
            $uri = $result22[0]->uri;
          }
          $url = ImageStyle::load('image_crop_16_9')->buildUrl($uri);

        }
        $media_data = [
          'url'  => $url,
          'name' => $mname,
          'alt'  => $malt,
        ];
      }
      elseif ($media_type === "remote_video") {
        $url = $media_entity->get('field_media_oembed_video')->value;
        $mname = $media_entity->get('name')->value;
        $site = (stripos($media_entity->get('field_media_oembed_video')->value, 'vimeo') !== FALSE) ? 'vimeo' : 'youtube';
        $media_data = [
          'url'  => $url,
          'name' => $mname,
          'site'  => $site,
        ];
      }
      elseif ($media_type === "video") {
        $mname = $media_entity->get('name')->value;
        $site = (stripos($media_entity->get('field_media_video_file')->value, 'vimeo') !== FALSE) ? 'vimeo' : 'youtube';
        $mid = $media_entity->get('field_media_video_file')->target_id;
        if (!empty($mid)) {
          /**
           * Get the File Details.
           *
           * @var object
           */
          $file = File::load($mid);
          $url = $file->url();
        }

        $media_data = [
          'url'  => $url,
          'name' => $mname,
          'site'  => $site,
        ];
      }
      return $media_data;
    }
    else {
      if ($key == "friends_family_offer_banner" || $key == "employee_schemes_banner") {
        $media_data = [
          'url'  => '',
          'name' => '',
          'alt'  => '',
        ];
      }
      return $media_data;
    }
  }

}
