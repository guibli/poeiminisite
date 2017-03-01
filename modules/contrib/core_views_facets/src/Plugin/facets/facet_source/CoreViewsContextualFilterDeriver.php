<?php

namespace Drupal\core_views_facets\Plugin\facets\facet_source;

use Drupal\facets\FacetSource\FacetSourceDeriverBase;
use Drupal\views\Views;
use Drupal\views\Plugin\views\query\Sql;

/**
 * Derives a facet source plugin definition for views with contextual filters.
 */
class CoreViewsContextualFilterDeriver extends FacetSourceDeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $base_plugin_id = $base_plugin_definition['id'];

    if (!isset($this->derivatives[$base_plugin_id])) {
      $plugin_derivatives = array();

      /** @var \Drupal\Core\Entity\EntityStorageInterface $views_storage */
      $views_storage = $this->entityTypeManager->getStorage('view');
      $all_views = $views_storage->loadMultiple();

      /** @var \Drupal\views\Entity\View $view */
      foreach ($all_views as $view) {

        $displays = $view->get('display');
        foreach ($displays as $name => $display_info) {
          if ($display_info['display_plugin'] == 'page') {
            $view_executable = Views::getView($view->id());
            $view_executable->setDisplay($name);
            if ($view_executable && $view_executable->getQuery() instanceof Sql) {

              $contextual_filter_available = FALSE;
              if (!empty($view_executable->getHandlers('argument', $name))) {
                $contextual_filter_available = TRUE;
              }

              if (!$contextual_filter_available) {
                continue;
              }

              $machine_name = $view->id() . '__' . $name;

              $plugin_derivatives[$machine_name] = [
                'id' => $base_plugin_id . ':' . $machine_name,
                'label' => $this->t('Core view contextual filter: %view_name, display: %display_title', [
                  '%view_name' => $view->label(),
                  '%display_title' => $display_info['display_title'],
                ]),
                'description' => $this->t('Provides a facet source by contextual filter.'),
                'config_dependencies' => array(
                  'config' => array(
                    $view->getConfigDependencyName(),
                  ),
                ),
                'view_id' => $view->id(),
                'view_display' => $name,
              ] + $base_plugin_definition;
            }
          }
        }
      }
      uasort($plugin_derivatives, array($this, 'compareDerivatives'));

      $this->derivatives[$base_plugin_id] = $plugin_derivatives;
    }
    return $this->derivatives[$base_plugin_id];
  }

}
