<?php

/**
 * @file
 * Drush commands for the nexx_integration module.
 */

/**
 * Implements hook_drush_commands().
 */
function nexx_integration_drush_command() {
  $items = array();

  $items['nexx-export-taxonomy'] = array(
    'description' => "Export taxonomy terms to omnia. This inserts all terms into Omnia, it cannot check if they already exist.",
    'arguments' => array(
      'source_vocabulary' => 'Drupal vocabulary to export.',
      'destination' => 'Omnia destination to export to.',
    ),
    'examples' => array(
      'drush -l http://example.com nexx-export-taxonomy category channel' => 'Export drupal categories vocabulary to omnia channel',
      'drush -l http://example.com nexx-export-taxonomy people actors' => 'Export drupal people vocabulary to omnia actors',
    ),
  );

  return $items;
}

/**
 * Implements the nexx-export-taxonomy drush command.
 *
 * @param string $source_vocabulary
 *   The drupal vocabulary to export.
 * @param string $destination
 *   The omnia destination to export to.
 */
function drush_nexx_integration_nexx_export_taxonomy($source_vocabulary, $destination) {
  $type_manager = \Drupal::entityTypeManager();

  $vocabulary = $type_manager->getStorage("taxonomy_vocabulary")->load($source_vocabulary);
  if (empty($vocabulary)) {
    drush_set_error(dt("Given vocabulary !vocabulary does not exist.", array('!vocabulary' => $source_vocabulary)));
    exit;
  }

  $terms = $type_manager
    ->getStorage("taxonomy_term")
    ->loadTree($source_vocabulary, 0, NULL, TRUE);

  foreach ($terms as $term) {
    nexx_integration_taxonomy_term_update($term);
  }
}