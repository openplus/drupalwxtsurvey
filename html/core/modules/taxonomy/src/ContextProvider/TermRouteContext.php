<?php

namespace Drupal\taxonomy\ContextProvider;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Plugin\Context\EntityRouteContextBase;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Sets the current taxonomy term as a context on taxonomy term routes.
 */
class TermRouteContext extends EntityRouteContextBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(RouteMatchInterface $route_match, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($route_match, $entity_type_manager, 'taxonomy_term', 'entity.taxonomy_term.add_form');
  }

}
