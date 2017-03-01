<?php

/**
 * @file
 * Contains Drupal\multiple_registration\Form\CreateRegistrationPageForm.
 */

namespace Drupal\multiple_registration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\multiple_registration\Controller\MultipleRegistrationController;


class CreateRegistrationPageForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'multiple_registration.create_registration_page_form_config'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'create_registration_page_form';
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
    $config = $this->config('multiple_registration.create_registration_page_form_config');
    $form['multiple_registration_path_' . $rid] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Registration page path'),
      '#description' => $this->t('Path for registration page.'),
      '#default_value' => $config->get('multiple_registration_path_' . $rid),
    );
    $form['multiple_registration_url_' . $rid] = array(
      '#type' => 'value',
      '#value' => MultipleRegistrationController::MULTIPLE_REGISTRATION_SIGNUP_PATH_PATTERN . $rid,
    );


    return parent::buildForm($form, $form_state);
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
    parent::submitForm($form, $form_state);
    $rid = $form_state->getValue('rid');
    $source = $form_state->getValue('multiple_registration_url_' . $rid);
    $alias = $form_state->getValue('multiple_registration_path_' . $rid);

    $this->config('multiple_registration.create_registration_page_form_config')
        ->set('multiple_registration_path_' . $rid, $alias)
        ->set('multiple_registration_url_' . $rid, $source)
        ->save();

    $multipleRegistration = \Drupal::service('multiple_registration.controller_service');
    $multipleRegistration->addRegisterPageAlias($source, '/'.$alias);
    $form_state->setRedirect('multiple_registration.multiple_registration_list_index');
  }
}
