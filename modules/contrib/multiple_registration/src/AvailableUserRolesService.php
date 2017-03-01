<?php

/**
 * @file
 * Contains Drupal\multiple_registration\AvailableUserRolesService.
 */

namespace Drupal\multiple_registration;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityManagerInterface;

class AvailableUserRolesService {

  /**
   * The role storage used when changing the admin role.
   *
   * @var \Drupal\user\RoleStorageInterface
   */
  protected $entityManager;

  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * Get all roles with ability to create registration page.
   *
   * @return array.
   */
  public function getAvailableRoles() {
    $roles = user_role_names();
    $role_storage = $this->entityManager->getStorage('user_role');
    $admin_role = $role_storage->getQuery()
      ->condition('is_admin', TRUE)
      ->execute();
    $admin_role = reset($admin_role);
    $notAvalible = array(
      AccountInterface::ANONYMOUS_ROLE => $roles[AccountInterface::ANONYMOUS_ROLE],
      AccountInterface::AUTHENTICATED_ROLE => $roles[AccountInterface::AUTHENTICATED_ROLE],
      $admin_role => $roles[$admin_role],
    );

    return array_diff_assoc($roles, $notAvalible);
  }

  /**
   * Get all role ids for whom registration forms was created.
   *
   * @return mixed
   *   If registration forms exists, array of paths.
   *   In other situation - FALSE.
   */
  public function getRegistrationPages() {
    $roles = $this->getAvailableRoles();
    if (!empty($roles)) {
      $pages_config = \Drupal::configFactory()->getEditable('multiple_registration.create_registration_page_form_config');
      $reg_pages = array();
      foreach ($roles as $rid => $role_name) {
        if ($url = $pages_config->get('multiple_registration_url_' . $rid)) {
          $reg_pages[$rid]['url'] = $url;
          $reg_pages[$rid]['role_name'] = $role_name;
        }
      }
      return $reg_pages;
    }
    return FALSE;
  }
}
