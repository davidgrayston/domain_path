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
    $definition = $container->getDefinition('path.alias_storage');
    $definition->setClass('Drupal\domain_path\DomainPathAliasStorage');
  }

}
