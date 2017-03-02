<?php

namespace Drupal\reusable_forms\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the ReusableFormBase abstract class
 */
abstract class ReusableFormBase extends FormBase implements ReusableFormInterface {

  /**
   * @var \Drupal\Core\Entity\EntityInterface.
   */
  protected $entity;

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $build_info = $form_state->getBuildInfo();
    if ($build_info['args'] && $build_info['args'][0] instanceof EntityInterface) {
      $this->entity = $build_info['args'][0];
    }

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );

    return $form;
  }
}
