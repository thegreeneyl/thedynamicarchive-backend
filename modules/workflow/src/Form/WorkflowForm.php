<?php

namespace Drupal\workflow\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\group\Entity\Group;

/**
 * Configure workflow settings for this site.
 *
 */
class WorkflowForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'workflow_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory()->getEditable('workflow.settings');
 
    $form['workflow_cleanup'] = [
      '#type' => 'details',
      '#title' => t('Clean-Up'),
      '#open' => TRUE,
    ];
    $form['workflow_cleanup']['orphans'] = [
      '#type' => 'submit',
      '#value' => t('Delete all Orphan-Nodes'),
      '#submit' => ['::submitOphanDelete'],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    parent::submitForm($form, $form_state);
  }

  /**
   * Clears the caches.
   */
  public function submitOphanDelete(array &$form, FormStateInterface $form_state) {
    
    $t = 0;
    $n = 0;

    $terms = $this->workflowGetTerms();
    foreach ($terms as $term) {
      $group = Group::load($term->field_component->target_id);
      if ($group == NULL) {
        $nodes = $this->workflowGetNodesByTaxonomyTermIds($term->id());
        if (is_array($nodes) && count($nodes)) foreach ($nodes as $node) {
          $node->delete();
          $n ++;
        }
        $term->delete();
        $t ++;
      }
    }

    $nodes = $this->workflowGetNodes();
    if (is_array($nodes) && count($nodes)) foreach ($nodes as $node) {
      $term = Term::load($node->field_group->target_id);
      if ($term == NULL) {
        $node->delete();
        $n ++;
      }
    }

    \Drupal::logger('workflow')
      ->info('Deleted @terms Groups and @nodes Nodes', [
      '@terms' => $t,
      '@nodes' => $n,
    ]);

    $this->messenger()->addStatus($this->t('Deleted @terms Groups and @nodes Nodes', [
      '@terms' => $t,
      '@nodes' => $n,
    ]));
  }


function workflowGetNodesByTaxonomyTermIds($termIds){
  $termIds = (array) $termIds;
  if(empty($termIds)){
    return NULL;
  }

  $query = \Drupal::database()->select('taxonomy_index', 'ti');
  $query->fields('ti', array('nid'));
  $query->condition('ti.tid', $termIds, 'IN');
  $result = $query->execute();

  if($nodeIds = $result->fetchCol()){
    return Node::loadMultiple($nodeIds);
  }

  return NULL;
}

function workflowGetNodes(){

  $query = \Drupal::entityQuery('node');
  $query->condition('field_group', NULL, 'IS NOT NULL');
  $nids = $query->execute();

  return Node::loadMultiple($nids);
}

function workflowGetTerms(){
  $query = \Drupal::entityQuery('taxonomy_term');
  $query->condition('vid', "group");
  $tids = $query->execute();

  $terms = Term::loadMultiple($tids);
  return $terms;
}

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['workflow.settings'];
  }

}
