<?php
/**
 * @file
 * Contains \Drupal\movie_custom\Plugin\Block\WeatherBlock.
 */

 namespace Drupal\movie_custom\Plugin\Block;

 use Drupal\Core\Block\BlockBase;

 /**
 * Provides a 'weather' block.
 *
 * @Block(
 *   id = "weather_block",
 *   admin_label = @Translation("weather block"),
 *   category = @Translation("Custom weather block")
 * )
 */


 class WeatherBlock extends BlockBase {

   /**
   * {@inheritdoc}
   */
   public function build(){
    $result = ['chennai'=> 35,'Erode'=>30,'covai'=>29];
    $build = [
        '#markup' => t('This the weather bloc'),
        '#theme' => 'weather',
        '#data' => $result,
      ];

     return $build; 
   }


   /**
 * {@inheritdoc}
 * return 0 If you want to disable caching for this block.
 */
public function getCacheMaxAge()
{

    return 0;
}

 }






