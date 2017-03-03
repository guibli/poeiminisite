<?php

namespace Drupal\search_save\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxy;

/**
 * Class SaveSearchForm.
 *
 * @package Drupal\search_save\Form
 */
class SaveSearchForm extends ConfigFormBase {

  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;
  public function __construct(
    ConfigFactoryInterface $config_factory,
      AccountProxy $current_user
    ) {
    parent::__construct($config_factory);
        $this->currentUser = $current_user;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
            $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'search_save.savesearch',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'save_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('search_save.savesearch');
    // List views and display for #option
		$views = Views::getViewsAsOptions();

    $form['view'] = [
      '#type' => 'select',
      '#title' => $this->t('View'),
      '#options' => $views,
      '#size' => 5,
      '#default_value' => $config->get('view'),
    ];
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

    $this->config('search_save.savesearch')
      ->set('view', $form_state->getValue('view'))
      ->save();
  }

}
