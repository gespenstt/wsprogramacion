<?php
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseACL;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseException;
use Parse\ParseAnalytics;
use Parse\ParseFile;
use Parse\ParseCloud;
/**
 * api actions.
 *
 * @package    WSProgramacion
 * @subpackage api
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class apiActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
      //Parse\ParseClient::initialize($app_id, $rest_key, $master_key)
      Parse\ParseClient::initialize( "YOpjIC8Zy0Pp3yMTJNa8iKG8PhpPFpQ3gfRDzBoB", "0u4TuZ7mCC49bGCv1Y89QELpmwYJXx8NlsuQJna7", "4VnN9ADUyDTVBpOLWzBQCNSFHVeQ2ACL7tSKg1mH" );
      
      $object = Parse\ParseObject::create("TestObject");
      // Set values:
$object->set("elephant", "php");
$object->set("today", new DateTime());
$object->setArray("mylist", [1, 2, 3]);
$object->setAssociativeArray(
  "languageTypes", array("php" => "awesome", "ruby" => "wtf")
);

// Save:
$object->save();
  }
}
