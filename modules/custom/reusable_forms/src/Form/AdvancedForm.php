<?php

namespace Drupal\reusable_forms\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the AdvancedForm class.
 */
class AdvancedForm extends ReusableFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'advanced_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['first_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('First name'),
    );

    $form['address'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
    );

    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Handle form submission.
  }
}
