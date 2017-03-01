<?php

/**
 * @file
 * Contains \Drupal\multiple_registration\Plugin\Derivative\MultipleRegistrationLocalTasks.
 */

namespace Drupal\multiple_registration\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines dynamic local tasks.
 */
class MultipleRegistrationLocalTasks extends DeriverBase implements ContainerDeriverInterface {

  protected $availableUserRolesService;

  /**
   * The base plugin ID.
   *
   * @var string
   */
  protected $basePluginId;

  public function __construct($availableUserRolesService, $base_plugin_id) {
    $this->availableUserRolesService = $availableUserRolesService;
    $this->base_plugin_id = $base_plugin_id;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
        $container->get('multiple_registration.service'), $base_plugin_id
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $regPages = $this->availableUserRolesService->getRegistrationPages();

    if (!empty($regPages)) {
      foreach ($regPages as $rid => $role) {
        $this->derivatives[$rid] = array();
        $this->derivatives[$rid]['title'] = t('Create new @role account', array('@role' => $role['role_name']));
        $this->derivatives[$rid]['base_route'] = 'user.page';
        $this->derivatives[$rid]['route_name'] = 'multiple_registration.role_registration_page';
        $this->derivatives[$rid]['route_parameters'] = array('rid' => $rid);
      }
    }

    return parent::getDerivativeDefinitions($base_plugin_definition);
  }
}
