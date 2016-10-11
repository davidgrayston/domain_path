<?php

namespace Drupal\domain_path;

use Drupal\pathauto\PathautoWidget;

/**
 * Extends the core path auto widget.
 */
class DomainPathPathautoWidget extends PathautoWidget {
  use DomainPathPathWidgetValidator;
}
