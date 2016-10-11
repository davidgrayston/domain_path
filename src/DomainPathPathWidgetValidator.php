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

      if ($domainValues = $form_state->getValue('field_domain_access')) {
        foreach ($domainValues as $domainValue) {
          $domain = \Drupal::service('domain.loader')->load($domainValue['target_id']);
          // Validate that the submitted alias does not exist yet.
          $is_exists = \Drupal::service('path.alias_storage')->domainAliasExists($alias, $element['langcode']['#value'], $element['source']['#value'], $domain->getDomainId());
          if ($is_exists) {
            $form_state->setError($element, t('The alias is already in use on :domain.', [':domain' => $domain->get('name')]));
          }
        }
      }

    }

    if ($alias && $alias[0] !== '/') {
      $form_state->setError($element, t('The alias needs to start with a slash.'));
    }
  }
}
