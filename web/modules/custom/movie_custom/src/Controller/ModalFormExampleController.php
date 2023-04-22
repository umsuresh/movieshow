<?php 

namespace Drupal\movie_custom\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;


/**
 * ModalFormExampleController class.
 */
class ModalFormExampleController extends ControllerBase {

/**
 *  @var \Drupal\Core\Form\FormBuilder 
 * 
 * */

 protected $formBuilder;

 public function __construct(FormBuilder $formBuilder){
    
    $this->formBuilder = $formBuilder;

 }

 /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  
   public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }

 /***
  * Call open dialog form  
  * */

  public function openModalForm() {
    $response = new AjaxResponse();

    // Get the modal form using the form builder.
    $modal_form = $this->formBuilder->getForm('Drupal\movie_custom\Form\ModelForm');

    // Add an AJAX command to open a modal dialog with the form as the content.
    $response->addCommand(new OpenModalDialogCommand('My Modal Form', $modal_form, ['width' => '800']));

    return $response;
  }
}
