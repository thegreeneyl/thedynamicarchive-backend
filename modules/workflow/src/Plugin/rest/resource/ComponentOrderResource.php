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
		/*
	    // view michine name user_quick_view	
	    $content = [];    
	    $viewId = 'rest_component_content';
	    $displayId = 'rest_export_nested_1';
	    $arguments = [$group->id[0]->value];

	    // Get the view
	    $result = $this->getView($viewId, $displayId, $arguments);

	    if(is_object($result)) {
	        $json = $result->jsonSerialize();
	        $content = Json::decode($json);
	    }
		*/
		$response = [];
		if (array_key_exists("groups", $data) && count($data["groups"])) { /*&& 
			array_key_exists("groups", $content) && count($content["groups"])) {*/

			//$terms = $content["groups"]; //\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid,0,NULL,TRUE);
			$terms = $this->getTermsByGroupIds($group_id);
			$response = $terms;
			foreach ($terms as $term) {
				foreach ($data["groups"] as $key => $targetterm) 
				  if ($targetterm['tid'] == $term->id() && $term->weight->value != $key) {
			        $term->weight->value = $key;
					\Drupal::logger('workflow')
					    ->info('Componente Order: @key => @tid', [
					    '@key' => $key,
					    '@tid' => $term->id()
					]);
					$term->save();
				}
					//$nodes = $term["content"];
				$nodes = $this->getNodesByTaxonomyTermIds($term->id());
				//$response[$term->id()] = $nodes;
					//$response = $nodes;
				foreach ($nodes as $key => $node) {
					// \Drupal::logger('workflow')
					//     ->info('Componente Order Node: @key => @tid', [
					//     '@key' => $node->id(),
					//     '@tid' => $term->id()
					// ]);
					foreach ($data["groups"] as $key => $targetterm) foreach ($targetterm["content"] as $nkey => $targetnode) {
						// \Drupal::logger('workflow')
						//     ->info('Componente Order Node: @key => @tid, target: @tkey => @ttid', [
						//     '@key' => $node->id(),
						//     '@tid' => $term->id(),
						//     '@tkey' => $targetnode["nid"],
						//     '@ttid' => $targetterm["tid"]
						// ]);
						if ($targetnode["nid"] == $node->id() && $targetterm["tid"] != $term->id()) {
			        		$node->field_group->target_id = $targetterm["tid"];
							// \Drupal::logger('workflow')
							//     ->info('Componente Order Node: @key => @tid', [
							//     '@key' => $node->id(),
							//     '@tid' => $targetterm["tid"]
							// ]);
							\Drupal::logger('workflow')
						    ->info('Componente Order Node: @key => @tid, prev: @tkey => @ttid', [
						    '@key' => $node->id(),
						    '@tid' => $node->field_group->target_id,
						    '@tkey' => $targetnode["nid"],
						    '@ttid' => $term->id()
						]);
							$node->save();
						}
					}
					
				}
			}
		} 

		/*
		$group->changed = REQUEST_TIME;
		$group->save();
		
	    // view michine name user_quick_view	
	    $data = [];    
	    $viewId = 'rest_component_outline';
	    $displayId = 'rest_export_nested_1';
	    $arguments = [$group->id[0]->value];
		
	    // Get the view
	    $result = $this->getView($viewId, $displayId, $arguments);
		
	    if(is_object($result)) {
	        $json = $result->jsonSerialize();
	        $data = Json::decode($json);
	    }
		*/
		//$response = $terms;
		
		//$response = ['ID' => $new_group->ID(), 'component' => $new_group];
    } else $response = ['message' => 'Component '.$data["id"].' not found'];

	return new ModifiedResourceResponse($response);
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