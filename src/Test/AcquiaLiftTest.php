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
		var_dump($liftEvents->fetchPerson());


	    $build = array(
	      '#type' => 'markup',
	      '#markup' => t(print_r($liftEvents->fetchPerson(), TRUE)),
	    );
	    return $build;
	}
}
