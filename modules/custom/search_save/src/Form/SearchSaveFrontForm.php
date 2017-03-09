<?php

namespace Drupal\search_save\Form;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
/**
 * Class SearchSaveFrontForm.
 *
 * @package Drupal\search_save\Form
 */
class SearchSaveFrontForm extends FormBase {

  protected $currentRequest;
  protected $currentUser;

  public function __construct(
    RequestStack $request,
    AccountProxy $current_user
  ) {
    $this->currentRequest = $request;
    $this->currentUser = $current_user;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('current_user')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'search_save_front_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
		$form['#prefix'] = '<div id="my-form-wrapper-id">';
		$form['#suffix'] = '</div>';
    $form['titre'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Titre'),
      '#description' => $this->t('Titre de la recherche'),
    ];

    $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
				'#attributes' => [
					'class' => [
						'btn',
						'btn-md',
						'btn-primary',
						'use-ajax-submit'
					],
				],
			'#ajax' => [
				'wrapper' => 'my-form-wrapper-id',
			]
    ];


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
    // Display result.

		if($form_state->getValue('titre')!=""){

			$titre = $form_state->getValue('titre');
			$user = $this->currentUser->getAccount()->id();
			$url = $this->currentRequest->getCurrentRequest()->server->get('HTTP_REFERER');

			$query = \Drupal::database()->insert('search_save');
			$query->fields([
				'uid' => $user,
				'url' => $url,
				'title' => $titre
			]);

			$query->execute();
		}
		$form['titre'] = [

			'#default_value' => 'Recherche sauvegardée',
		];
  }

}
