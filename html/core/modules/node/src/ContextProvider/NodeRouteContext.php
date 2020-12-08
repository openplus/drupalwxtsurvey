<?php

namespace Drupal\node\ContextProvider;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Plugin\Context\EntityRouteContextBase;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Sets the current node as a context on node routes.
 */
class NodeRouteContext extends EntityRouteContextBase {

  public function __construct(RouteMatchInterface $route_match, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($route_match, $entity_type_manager, 'node', 'node.add');
  }

}
