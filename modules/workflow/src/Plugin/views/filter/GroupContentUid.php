<?php
    /**
     * @file
     * Definition of Drupal\workflow\Plugin\views\filter\GroupContentUid.
     */

namespace Drupal\workflow\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filter by group content uid.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("workflow_group_content_uid")
 */
class GroupContentUid extends FilterPluginBase {

  public function adminSummary() {}

  protected function operatorForm(&$form, FormStateInterface $form_state) {}

  public function canExpose() {
    return FALSE;
  }

  public function query() {
    $table = $this->ensureMyTable();
    $snippet = "($table.uid = ***CURRENT_USER*** AND ***CURRENT_USER*** <> 0 AND ***VIEW_OWN_UNPUBLISHED_NODES*** = 1) OR ***BYPASS_NODE_ACCESS*** = 1";
    if ($this->moduleHandler->moduleExists('content_moderation')) {
      $snippet .= ' OR ***VIEW_ANY_UNPUBLISHED_NODES*** = 1';
    }
    $this->query->addWhereExpression($this->options['group'], $snippet);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();

    $contexts[] = 'user';

    return $contexts;
  }

}
