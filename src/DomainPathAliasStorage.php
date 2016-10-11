<?php

namespace Drupal\domain_path;

use Drupal\Core\Path\AliasStorage;
use Drupal\Core\Language\LanguageInterface;

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

  /**
   * {@inheritdoc}
   */
  public function lookupPathSource($path, $langcode) {
    $alias = $this->connection->escapeLike($path);
    $langcode_list = [$langcode, LanguageInterface::LANGCODE_NOT_SPECIFIED];

    // See the queries above. Use LIKE for case-insensitive matching.
    $select = $this->connection->select(static::TABLE, 'ua')
      ->fields('ua', ['source'])
      ->condition('ua.alias', $alias, 'LIKE');

    // Inner join provided current domain ID.
    if ($domain = \Drupal::service('domain.negotiator')->getActiveDomain()) {
      $select->innerJoin('node_access', 'na', "CONCAT('/node/', CAST(na.nid AS CHAR)) = ua.source");
      $select->condition('na.realm', 'domain_id');
      $select->condition('na.gid', $domain->getDomainId());
    }

    if ($langcode == LanguageInterface::LANGCODE_NOT_SPECIFIED) {
      array_pop($langcode_list);
    }
    elseif ($langcode > LanguageInterface::LANGCODE_NOT_SPECIFIED) {
      $select->orderBy('ua.langcode', 'DESC');
    }
    else {
      $select->orderBy('ua.langcode', 'ASC');
    }

    $select->orderBy('ua.pid', 'DESC');
    $select->condition('ua.langcode', $langcode_list, 'IN');
    try {
      return $select->execute()->fetchField();
    }
    catch (\Exception $e) {
      $this->catchException($e);
      return FALSE;
    }
  }

}
