# Acquia Lift REST Connector
This is a starter class for interacting with the Acquia Lift REST API.

### Installation
Requires Composer to install
Run the command within the directory
```
:$ composer install
```
### Configuration
Visit the Admin -> Configuration -> System -> Acquia Lift Configuration page and enter the Access Key ID and the Secret Access Key values. You can obtain the values from the Lift Profile Management site within the profile page for the desired user. 

### Usage
Contains a class called `LiftEventService` with a method called `fetchEvents` which will also contain an `events` object containing all events for the current user.
The current user is determined by a cookie value set by Lift.

For more information on the visitor_query endpoint see: <http://docs.lift.acquia.com/profilemanager/#visitor_query>

#### Example:
In the following example we are showing how to parse through the data to obtain the contentId which is a viewed node and grab the values for the field_topics.
```php
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
```
