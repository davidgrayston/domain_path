<?php

namespace Drupal\domain_path;

use Drupal\Core\Form\FormStateInterface;

/**
 * Supplies validator methods for path widgets.
 */
trait DomainPathPathWidgetValidator {
  /**
   * @inheritdoc
   */
  public static function validateFormElement(array &$element, FormStateInterface $form_state) {
    // Trim the submitted value of whitespace and slashes.
    $alias = rtrim(trim($element['alias']['#value']), " \\/");
    if (!empty($alias)) {
      $form_state->setValueForElement($element['alias'], $alias);


      // If 'all affiliates' is checked, check for existence of alias on other 'all affiliates' nodes.
      $allAffiliates = $form_state->getValue('field_domain_all_affiliates');
      if (!empty($allAffiliates['value'])) {
        // Validate that the submitted alias does not exist yet.
        $is_exists = \Drupal::service('path.alias_storage')->domainAliasExists($alias, $element['langcode']['#value'], $element['source']['#value']);
        if ($is_exists) {
          $form_state->setError($element, t('The alias is already in use by content available to all affiliates.'));
        }
      }
      elseif ($domainValues = $form_state->getValue('field_domain_access')) {
        // If domains are checked, check existence of alias on each domain.
        foreach ($domainValues as $domainValue) {
          $domain = \Drupal::service('domain.loader')->load($domainValue['target_id']);
          // Validate that the submitted alias does not exist yet.
          $is_exists = \Drupal::service('path.alias_storage')->domainAliasExists($alias, $element['langcode']['#value'], $element['source']['#value'], $domain->getDomainId());
          if ($is_exists) {
            $form_state->setError($element, t('The alias is already in use on :domain.', [':domain' => $domain->get('name')]));
          }
        }
      }
      else {
        // If no domains are checked and 'all affiliates' is unchecked, check default domain only.
        $domain = \Drupal::service('domain.loader')->loadDefaultDomain();
        $is_exists = \Drupal::service('path.alias_storage')->domainAliasExists($alias, $element['langcode']['#value'], $element['source']['#value'], $domain->getDomainId());
        if ($is_exists) {
          $form_state->setError($element, t('The alias is already in use on :domain (default domain).', [':domain' => $domain->get('name')]));
        }
      }

    }

    if ($alias && $alias[0] !== '/') {
      $form_state->setError($element, t('The alias needs to start with a slash.'));
    }
  }
}
