<?php

namespace Drupal\domain_path;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Overrides the core services.
 */
class DomainPathServiceProvider extends ServiceProviderBase implements ServiceModifierInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Override path.alias_storage.
    $container->getDefinition('path.alias_storage')->setClass('Drupal\domain_path\DomainPathAliasStorage');
    // Override path.alias_manager.
    $container->getDefinition('path.alias_manager')->setClass('Drupal\domain_path\DomainPathAliasManager');
  }

}
