<?php

namespace Drupal\acquia_lift_rest;

//todo: this can probably be removed if installed more properly
require drupal_get_path('module', 'acquia_lift_rest') .'/vendor/autoload.php';

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

	protected $liftKey;
	protected $liftSecret;
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
		//$this->userId = '13NuhMeONpSCgc103gPFiZ'; // this user id will fail
		//$this->userId = '2ArmUu3MwzwsbA4nfoRQXz'; // this user id will return data

		$lift_config = \Drupal::config('acquia_lift.settings');
		$this->liftAccount = $lift_config->get('credential.account_id');

		$this->liftUrl = "https://us-east-1-api.lift.acquia.com/dashboard/rest/" . $this->liftAccount . "/visitor_query";

		$userConfig = \Drupal::config('config.acquia_lift_rest');
		$this->liftKey = $userConfig->get('user_key_title');
		$this->liftSecret = $userConfig->get('secret_key_title');
	}

	/**
	 * Fetch event data for the current user and return
	 */
	public function fetchEvents() {

		$client = \Drupal::httpClient();

		$request = new Request('GET', $this::buildQuery('event'));

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
	 * fetch person data from lift
	 */
	public function fetchPerson() {
		$client = \Drupal::httpClient();

		$request = new Request('GET', $this::buildQuery('person'));

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
		$key = new Key($this->liftKey, $this->liftSecret);
		$middleware = new HmacAuthMiddleware($key);

		$stack = HandlerStack::create();
		$stack->push($middleware);

		$options = [
			'handlers' => $stack,
		];

		return $options;
	}

	/**
	 * build the query string for the person request
	 * @param string $type - can be for either person, event, touch
	 */
	protected function buildQuery($type) {

		$get_query = array(
			'identifier' => $this->userId,
			'identifierType' => 'tracking',
			'personTables' => $type,
		);

		$query = http_build_query($get_query);	
		return $this->liftUrl.'?'.$query;
	}
}
