<?php

namespace Drupal\workflow\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\group\Entity;
use Drupal\group\Entity\Group;
use Drupal\group\Entity\GroupContent;
use Drupal\group\Entity\GroupContentType;
use Drupal\views\Views;
use Symfony\Component\Serializer;  
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Component\Serialization\Json;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Provides a Component Content-Order Resource
 *
 * @RestResource(
 *   id = "componentorder_resource",
 *   label = @Translation("Component Content-Order Resource"),
 *   uri_paths = {
 *     "https://www.drupal.org/link-relations/create" = "/component/content-order",
 *   }
 * )
 */

class ComponentOrderResource extends ResourceBase {


  public function getNodesByTaxonomyTermIds($termIds){
	  $termIds = (array) $termIds;
	  if(empty($termIds)){
	    return NULL;
	  }

	  $query = \Drupal::database()->select('taxonomy_index', 'ti');
	  $query->fields('ti', array('nid'));
	  $query->condition('ti.tid', $termIds, 'IN');
	  //$query->condition('ti.status', 1, '=');
      $query->orderBy('ti.weight', 'DESC');
      $query->orderBy('ti.created', 'ASC');
	  //$query->distinct(TRUE);
	  $result = $query->execute();

	  if($nodeIds = $result->fetchCol()){
	    return Node::loadMultiple($nodeIds);
	  }

	  return NULL;
	}	
  public function getTermsByGroupIds($grouId){

	  $query = \Drupal::entityQuery('taxonomy_term');
	  $query->condition('vid', "group");
	  $query->condition('field_component', $grouId);
  	  //$query->condition('status', 1, '=');
	  $query->sort('weight','ASC');
      $query->sort('tid', 'ASC');

	  $tids = $query->execute();

	  $terms = Term::loadMultiple($tids);
	  return $terms;
	}

  /**
   * Responds to entity POST requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function post($data) {
  	//$data = json_decode(stripslashes($data));
    if ($data == NULL) {
      throw new BadRequestHttpException('No content received.');
    }
    $response = $data;
    
  	if (array_key_exists("id", $data)) {
	  	$group_id = intval($data["id"]);

	    if (is_numeric($group_id)) $group = Group::load($group_id);
	}

    if ($group != null) {

		$response = [];
		$connection = \Drupal::database();
		if (array_key_exists("groups", $data) && count($data["groups"])) { /*&& 
			array_key_exists("groups", $content) && count($content["groups"])) {*/

			//$terms = $content["groups"]; //\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid,0,NULL,TRUE);
			$terms = $this->getTermsByGroupIds($group_id);

			$tweight = 0;

			foreach ($data["groups"] as $key => $targetterm) {
			  	if (array_key_exists($targetterm['tid'], $terms)) {
			  		$term = $terms[$targetterm['tid']];

				  	if ($term->weight[0]->value != $tweight) {

				        $term->weight->value = $tweight;
						\Drupal::logger('workflow')
						    ->info('Componente Order update Term Weight: @tid => @weight', [
						    '@weight' => $tweight,
						    '@tid' => $term->id()
						]);
						$term->save();
					}
					
					$query = $connection->select('taxonomy_index', 'ti');
					$query->fields('ti', ['nid', 'weight']);
					$query->condition('ti.tid', $term->id());
					//$query->distinct(TRUE);
					$result = $query->execute();
					$nodes = [];

					if($nodeIds = $result->fetchAll()){
					    foreach($nodeIds as $n) $nodes[$n->nid] = array(
					    	'nid'=> intval($n->nid), 
					    	'weight' => intval($n->weight), 
					    	'tid' => intval($term->id())
					    );
					}

					$response['groups'][] = array(
						'content' => [], 
						'tid' => intval($term->id()),
					);


					$weight = ceil(count($targetterm["content"]) / 2) -1;

					foreach ($targetterm["content"] as $node) {
						$test = true;
						if (!array_key_exists(intval($node["nid"]), $nodes)) {
							$thenode = Node::load($node['nid']);
							if (array_key_exists($thenode->field_group->target_id, $terms)) {
								\Drupal::logger('workflow')
							    ->info('Componente Order Move Node @key: from @tid to @ttid', [
								    '@key' => $thenode->id(),
								    '@tid' => $thenode->field_group->target_id,
								    '@ttid' => $targetterm["tid"]
								]);

				        		$thenode->field_group->target_id = $targetterm["tid"];
								$thenode->save();
								$nodes[$node["nid"]] = $node;
							} else $test = false;
						}
						if ($test) {

							$response['groups'][$key]['content'][] = array('nid' => $node['nid']); //,'weight' => $weight);

							if ($nodes[$node["nid"]]['weight'] != $weight) {
						        $connection->update('taxonomy_index')
						          ->fields(['weight' => $weight])
						          ->condition('tid', $targetterm["tid"])
						          ->condition('nid', $node["nid"])
						          ->execute();
							}

							$weight--;
						}
					}

					$tweight++;
				}
			}
		} 

    } else $response = ['message' => 'Component '.$data["id"].' not found'];

	return new ModifiedResourceResponse(array($response));
    //return $response;//new ResourceResponse($response);
  }

	 /**
	 * Return the rendered view with contextual filter.
	 * @param string $viewId - The view machine name.
	 * @param string $viewId - The display machine name.
	 * @param array $arguments - The arguments to pass.
	 * 
	 * @return object $result
	 */
	function getView($viewId, $displayId, array $arguments)
	{
	    $result = false;
	    $view = Views::getView($viewId);

	    if (is_object($view)) {
	        $view->setDisplay($displayId);
	        $view->setArguments($arguments);
	        $view->execute();

	        // Render the view
	        $result = \Drupal::service('renderer')->render($view->render());
	    }

	    return $result;
	}
}