<?php

namespace Drupal\domain_path;

use Drupal\Core\Path\AliasStorage;

/**
 * Overrides AliasStorage.
 */
class DomainPathAliasStorage extends AliasStorage {
  /**
   * {@inheritdoc}
   */
  public function domainAliasExists($alias, $langcode, $source = NULL, $domain_id) {
    // Use LIKE and NOT LIKE for case-insensitive matching.
    $query = $this->connection->select(static::TABLE, 'ua')
      ->condition('ua.alias', $this->connection->escapeLike($alias), 'LIKE')
      ->condition('ua.langcode', $langcode);

    // Inner join provided domain_id.
    if (strpos($source, '/node/') === 0) {
      $query->innerJoin('node_access', 'na', "CONCAT('/node/', CAST(na.nid AS CHAR)) = ua.source");
      $query->condition('na.realm', 'domain_id');
      $query->condition('na.gid', $domain_id);
    }

    if (!empty($source)) {
      $query->condition('source', $this->connection->escapeLike($source), 'NOT LIKE');
    }
    $query->addExpression('1');
    $query->range(0, 1);

    try {
      return (bool) $query->execute()->fetchField();
    }
    catch (\Exception $e) {
      $this->catchException($e);
      return FALSE;
    }
  }

}
