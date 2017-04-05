<?php
/**
 * @file
 * Contains a class specific for testing the ALR connectivity and data
 */

/**
 * use the LiftEventService class
 */
namespace Drupal\acquia_lift_rest\Test;
use Drupal\acquia_lift_rest\LiftEventService;

/**
 * basic class for testing the events output and data
 */
class AcquiaLiftTest {
	public function runTest() {

		$liftEvents = new LiftEventService();

		$nids = [];

		foreach ($liftEvents->fetchEvents()->events as $key => $event) {

		if ($event->contentId !== NULL && !in_array($event->contentId, $nids)) {
		  $nids[] = $event->contentId;
		}
		}

		var_dump($nids);die;

	    $build = array(
	      '#type' => 'markup',
	      '#markup' => t('Hello World!'),
	    );
	    return $build;
	}
}
