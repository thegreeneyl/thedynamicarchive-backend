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

/**
 * Provides a Copy Component Resource
 *
 * @RestResource(
 *   id = "copycomponent_resource",
 *   label = @Translation("Copy Component Resource"),
 *   uri_paths = {
 *     "https://www.drupal.org/link-relations/create" = "/component/copy",
 *   }
 * )
 */

class CopyComponentResource extends ResourceBase {


  public function getNodesByTaxonomyTermIds($termIds){
	  $termIds = (array) $termIds;
	  if(empty($termIds)){
	    return NULL;
	  }

	  $query = \Drupal::database()->select('taxonomy_index', 'ti');
	  $query->fields('ti', array('nid'));
	  $query->condition('ti.tid', $termIds, 'IN');
	  $query->condition('ti.status', 1, '=');
      $query->orderBy('ti.weight', 'DESC');
      $query->orderBy('ti.created', 'ASC');
	  //$query->distinct(TRUE);
	  $result = $query->execute();

	  if($nodeIds = $result->fetchCol() && is_array($nodeIds)){
	    return Node::loadMultiple($nodeIds);
	  }

	  return NULL;
	}
  public function getTermsByGroupIds($grouId){

	  $query = \Drupal::entityQuery('taxonomy_term');
	  $query->condition('vid', "group");
	  $query->condition('field_component', $grouId);
  	  $query->condition('status', 1, '=');
	  $query->sort('weight','ASC');
      $query->sort('tid', 'ASC');

	  $tids = $query->execute();
	  if (!is_array($tids)) return NULL;
	  
	  $terms = Term::loadMultiple($tids);
	  return $terms;
	}

  /**
   * Responds to entity POST requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function post($data) {
  	//$data = json_decode(stripslashes($data));

  	if (array_key_exists("id", $data)) {
	  	$group_id = intval($data["id"]);

	    //$node = Node::load($node_id);
	    if (is_numeric($group_id)) $group = Group::load($group_id);
	}
    if ($group != null) {
		$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    	$new_group = $group->createDuplicate();
    	$new_group->uid = \Drupal::currentUser()->id();
    	$new_group->field_component_reference->target_id = $group_id;
    	$new_group->field_published->value = 0;
		$new_group->created = REQUEST_TIME;
		$new_group->changed = REQUEST_TIME;
		$new_group->save();

		if (array_key_exists("copy", $data) && $data["copy"]) {
			//$vid = 'group';
			
			$terms = $this->getTermsByGroupIds($new_group->id()); 
			foreach ($terms as $term) if ($term->get('field_component')->target_id == $new_group->id()) $term->delete();
			
			$terms = $this->getTermsByGroupIds($group_id); //\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid,0,NULL,TRUE);

			$new_nodes = [];

			foreach ($terms as $term) {
				//if ($term->get('field_component')->target_id == $new_group->id()) $term->delete();
				//if ($term->get('field_component')->target_id == $group_id) {
					
			    	$new_term = $term->createDuplicate();
			    	$new_term->uid = \Drupal::currentUser()->id();
			    	$new_term->field_component->target_id = $new_group->id();
			    	$new_term->field_original_group->target_id = $term->id();
				    //$new_term->setCreatedTime(REQUEST_TIME);
				    //$new_term->setChangedTime(REQUEST_TIME);
					$new_term->save();
					
					$nodes = $this->getNodesByTaxonomyTermIds($term->id());
					//$response = $nodes;
					foreach ($nodes as $node) {
				    	$new_node = $node->createDuplicate();
				    	$new_node->uid = \Drupal::currentUser()->id();
				    	$new_node->field_group->target_id = $new_term->id();
			    		$new_node->field_component = NULL; //->target_id = $new_group->id();
				    	$new_node->field_original_node->target_id = $node->id();
				    	$new_node->setCreatedTime(REQUEST_TIME);
				    	$new_node->setChangedTime(REQUEST_TIME);
				    	//if (array_key_exists("field_vimeo_file_browse", $new_node)) 
				    	$new_node->field_vimeo_file_browse = null;
				    	$new_nodes[] = $new_node;

						//$response = $new_node;
					}
					
				//}
			}

			foreach($new_nodes as $new_node) {
				\Drupal::logger('workflow')
		          ->info('save node “@node”', [
		          '@node' => $new_node->getTitle()
		        ]);
				$new_node->save();

			    $bundle = $new_node->bundle();
			    $plugin_id = 'group_node:' . $bundle;
				$new_group->addContent($new_node,$plugin_id);
			}
		} 
		/*
	    // view michine name user_quick_view	
	    $data = [];    
	    $viewId = 'rest_component_outline';
	    $displayId = 'rest_export_nested_1';
	    $arguments = [$new_group->id[0]->value];

	    // Get the view
	    $result = $this->getView($viewId, $displayId, $arguments);

	    if(is_object($result)) {
	        $json = $result->jsonSerialize();
	        $data = Json::decode($json);
	    }

		$response = $data;
		*/
		$response = ['ID' => $new_group->ID(), 'component' => $new_group];
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