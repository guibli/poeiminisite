<?php

/**
 * @file
 * Contains Drupal\multiple_registration\Controller\MultipleRegistrationController.
 */

namespace Drupal\multiple_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Path\AliasStorage;
use Drupal\Core\Database\Database;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;

class MultipleRegistrationController extends ControllerBase {

  const MULTIPLE_REGISTRATION_SIGNUP_PATH_PATTERN = '/user/register/';
  const MULTIPLE_REGISTRATION_GENERAL_REGISTRATION_ID = 100;

  public $reg_pages_config;
  protected $availableUserRolesService;

  public function __construct($availableUserRolesService) {
    $this->reg_pages_config = \Drupal::configFactory()->getEditable('multiple_registration.create_registration_page_form_config');
    $this->availableUserRolesService = $availableUserRolesService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('multiple_registration.service')
    );
  }

  /**
   * Page with registration pages list.
   */
  public function index() {
    $regPages = $this->availableUserRolesService->getRegistrationPages();
    if ($regPages) {
      foreach ($regPages as $rid => $role) {
        $row = array();
        $row[] = $role['role_name'];
        $path_alias = \Drupal::service('path.alias_manager')->getAliasByPath($role['url']);
        $row[] = $path_alias;
        $edit_url = Url::fromRoute("multiple_registration.create_registration_page_form", ['rid' => $rid], array(
              'attributes' => array(
                'class' => 'use-ajax',
                'data-accepts' => 'application/vnd.drupal-modal',
                'data-dialog-type' => 'modal',
                'data-dialog-options' => '{"width": "50%"}',
        )));
        $row[] = \Drupal::l($this->t('Edit'), $edit_url);
        $remove_url = Url::fromRoute("multiple_registration.delete_registration_page_form", ['rid' => $rid], array(
              'attributes' => array(
                'class' => 'use-ajax',
                'data-accepts' => 'application/vnd.drupal-modal',
                'data-dialog-type' => 'modal',
                'data-dialog-options' => '{"width": "50%"}',
        )));
        $row[] = \Drupal::l($this->t('Remove'), $remove_url);

        $rows[] = array('data' => $row);
      }
      $header = array(
        t('Role'),
        t('Registration page path'),
        array('data' => t('Operations'), 'colspan' => 2),
      );
      $output = array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#attributes' => array('id' => 'user-roles-reg-pages'),
        '#attached' => array('library' => array(
            'core/drupal.dialog.ajax',
          )),
      );
    }
    else {
      $add_reg_pages_link = \Drupal::l($this->t('here'), Url::fromRoute('entity.user_role.collection'));
      $output = array(
        '#markup' => $this->t('There are no additional registration pages created yet. You can add new pages %here', array('%here' => $add_reg_pages_link)),
      );
    }
    $output['#suffix'] = '<p>' . \Drupal::l($this->t('Go to Roles managing page'), Url::fromRoute('entity.user_role.collection')) . '</p>';


    return $output;
  }

  /**
   * Get AliasStorage object.
   *
   * @return AliasStorage
   */
  public function getRegisterAliasStorage() {
    // Prepare database table.
    $connection = Database::getConnection();
    // Create Path object.
    $aliasStorage = new AliasStorage($connection, $this->moduleHandler());
    return $aliasStorage;
  }

  /**
   * Adds alias for registration page.
   *
   * @param string $source
   * @param string $alias
   */
  public function addRegisterPageAlias($source, $alias) {
    $aliasStorage = $this->getRegisterAliasStorage();
    $conditions = array(
      'source' => $source,
    );
    // Checks if alias exists for url.
    $existsAliases = $aliasStorage->load($conditions);
    $pid = NULL;
    if (isset($existsAliases['pid'])) {
      $pid = $existsAliases['pid'];
    }
    $aliasStorage->save($source, $alias, LanguageInterface::LANGCODE_NOT_SPECIFIED, $pid);
  }

  /**
   * Removes registration page alias for role.
   *
   * @param int $rid
   */
  public function removeRegisterPageAlias($rid) {
    $aliasStorage = $this->getRegisterAliasStorage();
    $pages_config = $this->reg_pages_config;
    $conditions = array(
      'source' => $pages_config->get('multiple_registration_url_' . $rid),
    );
    $aliasStorage->delete($conditions);
  }

  /**
   * Removes registration page for role.
   *
   * @param int $rid
   */
  public function removeRegisterPage($rid) {
    $pages_config = $this->reg_pages_config;
    if ($pages_config->get('multiple_registration_url_' . $rid)) {
      $this->removeRegisterPageAlias($rid);
      $pages_config->clear('multiple_registration_path_' . $rid)->clear('multiple_registration_url_' . $rid)->save();
      drupal_set_message($this->t('Registration page has been removed.'));
    }
    else {
      drupal_set_message($this->t('Registration page has not been removed. There are no pages for this role.'), 'error');
    }
  }

  /**
   * Check is field available for role.
   *
   * @param array $fieldRoles
   * @param string $route_name
   *
   * @return bool
   */
  public static function checkFieldAccess($fieldRoles) {
    $routeMatch = \Drupal::routeMatch();
    $roles = array();
    switch ($routeMatch->getRouteName()) {
      // Role page registration.
      case 'multiple_registration.role_registration_page':
        $roles = array($routeMatch->getParameter('rid'));
        break;
      // Default registration.
      case 'user.register':
        $roles = array(self::MULTIPLE_REGISTRATION_GENERAL_REGISTRATION_ID);
        break;
      // User edit page.
      case 'entity.user.edit_form':
        $roles = $routeMatch->getParameter('user')->getRoles();
        break;
    }

    $extractKeys = array_intersect($roles, $fieldRoles);

    if (!empty($extractKeys)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Gets the title for registration page.
   */
  public function getRegisterPageTitle(RouteMatchInterface $route) {
    $role = $route->getRawParameter('rid');
    $roles = user_role_names();
    return $this->t('Create new @role account', array('@role' => $roles[$role]));
  }
}
