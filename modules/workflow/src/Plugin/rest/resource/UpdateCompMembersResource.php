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
		$currentuser = \Drupal::currentUser();
		$test = \Drupal\user\Entity\User::load(6);
		if ($group->getMember($currentuser) || true) { //$test->hasPermission('Bypass group access control')) {
			$members = [];
			if (array_key_exists("members", $data)) { 
				$membership = $group->getMembers();
				foreach ($membership as $k => $m) {
					$member = $m->getUser();
					$found = false;
					foreach ($data['members'] as $key => $user) if ($user["uid"] == $member->uid[0]->value) {
						$found = true;
						break;
					}
					if (!$found) $unmembers[] = $group->removeMember($member);
				}
				foreach ($data['members'] as $key => $user) {
					if (array_key_exists("uid", $user) && $account = \Drupal\user\Entity\User::load($user["uid"])) {
						if (!$group->getMember($account)) $group->addMember($account);
						$members[] = $account;
					} else {
						//$account->found = "not";
						$members[] = $user["uid"]." not found";
					}
				}
			}
    	
			$response = ['ID' => $group_id, 'component' => $group, 'members' => $members];
		} else $response = ['message' => 'No Permission to update Component '.$data["id"].'.'];

    } else $response = ['message' => 'Component '.$data["id"].' not found', 'data' => $data];

	return new ModifiedResourceResponse($response);
    //return $response;//new ResourceResponse($response);
  }

}