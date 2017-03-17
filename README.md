# Acquia Lift REST Connector
This is a starter class for interacting with the Acquia Lift REST API.

### Installation
Requires Composer to install
Run the command within the directory
```
:$ composer install
```

### Usage
Contains a class called `LiftEventService` with a method called `fetchEvents` which will also contain an `events` object containing all events for the current user.
The current user is determined by a cookie value set by Lift.

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
function rf_lift_js_api_fetch() {

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
