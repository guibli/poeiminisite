<?php

namespace Drupal\twig_extender;

use Drupal\Core\Template\TwigExtension;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Validation\DrupalTranslator;
use Drupal\twig_extender\Plugin\Twig\TwigPluginManagerInterface;

/**
 * Service Provider Class for TwigExtesion Plugins.
 */
class TwigExtenderService extends TwigExtension {

  /**
   * Construtor.
   */
  public function __construct(RendererInterface $renderer, TwigPluginManagerInterface $manager) {
    parent::__construct($renderer);
    $this->renderer = $renderer;
    $this->translator = new DrupalTranslator();
    $this->pluginManager = $manager;
    $this->plugins = $this->pluginManager->getDefinitions();
  }

  /**
   * Load all function plugins.
   */
  public function getFunctions() {
    $functions = parent::getFunctions();
    foreach ($this->plugins as $id => $plugin) {
      $plugin = $this->pluginManager->createInstance($id, $plugin);
      if ($plugin->getType() != 'function') {
        continue;
      }
      $functions[] = $plugin->register();
    }
    return $functions;
  }

  /**
   * Load all filter plugins.
   */
  public function getFilters() {
    $filters = parent::getFilters();
    foreach ($this->plugins as $id => $plugin) {
      $plugin = $this->pluginManager->createInstance($id);
      if ($plugin->getType() != 'filter') {
        continue;
      }
      $filters[] = $plugin->register();
    }
    return $filters;
  }

}
