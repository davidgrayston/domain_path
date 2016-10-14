<?php

namespace Drupal\domain_path;

use Drupal\Core\Path\AliasManager;

/**
 * Overrides AliasManager.
 */
class DomainPathAliasManager extends AliasManager {
  /**
   * {@inheritdoc}
   */
  public function setCacheKey($key) {
    // Prefix the cache key to avoid clashes with other domains.
    if ($domain = \Drupal::service('domain.negotiator')->getActiveDomain()) {
      $key = $domain->getDomainId() . ':' . $key;
    }
    parent::setCacheKey($key);
  }
}
