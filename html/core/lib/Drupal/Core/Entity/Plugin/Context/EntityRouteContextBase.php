<?php

namespace Drupal\Core\Entity\Plugin\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides an entity context based on the route.
 */
abstract class EntityRouteContextBase implements ContextProviderInterface {

  use StringTranslationTrait;

  /**
   * The route match object.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type ID.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * The route name for creating a new entity of this type.
   *
   * @var string
   */
  protected $createRouteName;

  /**
   * Constructs a new EntityRouteContextBase.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param string $entity_type_id
   *   The entity type ID.
   * @param string $create_route_name
   *   The route name for creating a new entity of this type.
   */
  public function __construct(RouteMatchInterface $route_match, EntityTypeManagerInterface $entity_type_manager, $entity_type_id, $create_route_name) {
    $this->routeMatch = $route_match;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeId = $entity_type_id;
    $this->createRouteName = $create_route_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getRuntimeContexts(array $unqualified_context_ids) {
    $result = [];
    $context_definition = EntityContextDefinition::create($this->entityTypeId)->setRequired(FALSE);
    $value = NULL;
    if (($route_object = $this->routeMatch->getRouteObject())) {
      $route_contexts = $route_object->getOption('parameters');
      // Check for a entity revision parameter first.
      // @todo https://www.drupal.org/i/2730631 will allow to use the upcasted
      //   node revision object.
      if ($revision_id = $this->routeMatch->getRawParameter($this->getEntityType()->getRevisionTable())) {
        $value = $this->entityTypeManager->getStorage($this->entityTypeId)->loadRevision($revision_id);
      }
      elseif (isset($route_contexts[$this->entityTypeId]) && $entity = $this->routeMatch->getParameter($this->entityTypeId)) {
        $value = $entity;
      }
      elseif (isset($route_contexts["{$this->entityTypeId}_preview"]) && $entity = $this->routeMatch->getParameter("{$this->entityTypeId}_preview")) {
        $value = $entity;
      }
    }
    if (!isset($route_contexts[$this->entityTypeId]) && $this->routeMatch->getRouteName() === $this->createRouteName) {
      $entity_type = $this->getEntityType();
      $values = [];
      if ($bundle_entity_type_id = $entity_type->getBundleEntityType()) {
        $bundle_entity = $this->routeMatch->getParameter($bundle_entity_type_id);
        $values[$entity_type->getKey('bundle')] = $bundle_entity->id();
      }
      $value = $this->entityTypeManager->getStorage($this->entityTypeId)->create($values);
    }

    $cacheability = new CacheableMetadata();
    $cacheability->setCacheContexts(['route']);

    $context = new Context($context_definition, $value);
    $context->addCacheableDependency($cacheability);
    $result[$this->entityTypeId] = $context;

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getAvailableContexts() {
    $entity_type = $this->getEntityType();
    $context = EntityContext::fromEntityTypeId($this->entityTypeId, $this->t('@label from URL', ['@label' => $entity_type->getLabel()]));
    return [$this->entityTypeId => $context];
  }

  /**
   * Gets the entity type.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   The entity type.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if $this->entityTypeId is invalid.
   */
  protected function getEntityType() {
    return $this->entityTypeManager->getDefinition($this->entityTypeId);
  }

}
