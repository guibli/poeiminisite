<?php

/**
 * @file
 * Contains Drupal\multiple_registration\Form\DeleteRegistrationPageForm.
 */

namespace Drupal\multiple_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\multiple_registration\Controller\MultipleRegistrationController;

class DeleteRegistrationPageForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'delete_registration_page_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $rid = NULL) {

    if (!isset($rid)) {
      return FALSE;
    }
    $roles = user_role_names();
    if (!isset($roles[$rid])) {
      return FALSE;
    }
    $form['rid'] = array('#type' => 'value', '#value' => $rid);
    $form['message'] = array(
      '#markup' => '<p>' . $this->t('Are you sure want to delete registration page for %role role?', array('%role' => $roles[$rid])) . '</p>',
    );
    $form['dont_remove'] = array(
      '#type' => 'submit',
      '#value' => t('No'),
    );
    $form['remove'] = array(
      '#type' => 'submit',
      '#value' => t('Yes'),
    );


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $rid = $form_state->getValue('rid');
    $clicked_button = end($form_state->getTriggeringElement()['#parents']);

    switch ($clicked_button) {

      case 'remove':
        if ($rid) {
          $multipleRegistration = \Drupal::service('multiple_registration.controller_service');
          $multipleRegistration->removeRegisterPage($rid);

        }
        break;
    }
    $form_state->setRedirect('multiple_registration.multiple_registration_list_index');
  }

}
