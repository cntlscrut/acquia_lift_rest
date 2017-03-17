<?php

/**
 * example function for pulling topics taxonomy
 * based on an existing field called field_topics
 */

use Drupal\acquia_lift_rest\LiftEventService;

/**
 * test function for test the lift connector class
 */ 
function example_topics_fetch() {

	$liftEvents = new LiftEventService();

	$nids = [];

	foreach ($liftEvents->fetchEvents()->events as $key => $event) {

		if ($event->contentId !== NULL && !in_array($event->contentId, $nids)) {
			$nids[] = $event->contentId;
		}
	}

	$nodes = node_load_multiple($nids);

	$user_topics = [];

	foreach ($nodes as $nid => $node) {
		if ($node->hasField('field_topics')) {
			$topics = Node::load($nid)->get('field_topics')->getValue();
			foreach ($topics as $key => $topic) {
				if ($topic['target_id'] !== NULL && !in_array($topic['target_id'], $user_topics)) {
					$user_topics[] = $topic['target_id'];
				}
			}
		}
	}

 	var_dump($user_topics);
 	return $user_topics;
}
