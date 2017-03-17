<?php

namespace Drupal\acquia_lift_rest;

//todo: this can probably be removed if installed more properly
require drupal_get_path('module', 'acquia_lift_rest') .'/vendor/autoload.php';

// Key and secret are available from Lift Profile Manager
define(LIFT_KEY, ""); 
define(LIFT_SECRET, "");

use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use Acquia\Hmac\Key;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;

/**
 * Class LiftEventService
 *
 * @package Drupal\acquia_lift_rest
 */
class LiftEventService {

	protected $userId;
	protected $liftAccount;
	protected $liftUrl;

	/**
	 * Constructor
	 *
	 * On creation grab the cookie value for the current user
	 * and pop it in a value.
	 *
	 * Grab the account id from Lift Configuration
	 */
	public function __construct() {
		
		$this->userId = $_COOKIE['tc_ptid'];

		$lift_config = \Drupal::config('acquia_lift.settings');
		$this->liftAccount = $lift_config->get('credential.account_id');

		$this->liftUrl = "https://us-east-1-api.lift.acquia.com/dashboard/rest/" . $this->liftAccount . "/visitor_query";
	}

	/**
	 * Fetch event data for the current user and return
	 */
	public function fetchEvents() {

		$client = \Drupal::httpClient();

		$request = new Request('GET', $this::buildQuery());

		try {
			$response = $client->send($request, $this::buildStack());
		} catch (ClientException $e) {
			print "message: " . $e->getMessage();
			$response = $e->getResponse();
		}
		
		$body = $response->getBody();

		$visitor_data = json_decode($body);

		return $visitor_data;
	}

	/**
	 * build the handler stack
	 */
	protected function buildStack() {
		$key = new Key(LIFT_KEY, LIFT_SECRET);
		$middleware = new HmacAuthMiddleware($key);

		$stack = HandlerStack::create();
		$stack->push($middleware);

		$options = [
			'handlers' => $stack,
		];

		return $options;
	}

	/**
	 * build the query string for the request
	 */
	protected function buildQuery() {

		$get_query = array(
			'identifier' => $this->userId,
			'identifierType' => 'tracking',
			'personTables' => 'event',
		);

		$query = http_build_query($get_query);	
		return $this->liftUrl.'?'.$query;
	}


}
