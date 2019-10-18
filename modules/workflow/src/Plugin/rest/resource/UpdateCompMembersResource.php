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
 * Provides a Update Component Members Resource
 *
 * @RestResource(
 *   id = "updatecompmembers_resource",
 *   label = @Translation("Update Component Members Resource"),
 *   uri_paths = {
 *     "https://www.drupal.org/link-relations/create" = "/component/members",
 *   }
 * )
 */

class UpdateCompMembersResource extends ResourceBase {


	public function workflowAddMembersToGroup($groupId) {
	  $account = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
	  $group = \Drupal\group\Entity\Group::load($groupId);
	  $group->addMember($account);
	  $group->save();
	}

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

	  if($nodeIds = $result->fetchCol()){
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
		if (array_key_exists("members", $data)) { 
		}
    	
		$response = ['ID' => $group_id, 'component' => $group];
    } else $response = ['message' => 'Component '.$data["id"].' not found', 'data' => $data];

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