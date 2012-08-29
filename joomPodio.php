<?php
/**
 *
 */
// no direct access
defined('_JEXEC') or die;



if ( isset($_POST) && count($_POST) > 0 ) {


}


jimport('joomla.plugin.plugin');

class plgSystemPodio_leads extends JPlugin {

  /**
  * Constructor.
  *
  * @access protected
  * @param object $subject The object to observe
  * @param array   $config  An array that holds the plugin configuration
  * @since 1.0
  */
  public function __construct( &$subject, $config ) {
    parent::__construct( $subject, $config );

    $this->podio = new JPodio($this->params->get('CLIENT_ID'), $this->params->get('CLIENT_SECRET') );
  }


  /**
  * Do something onAfterInitialise
  */
  function onAfterInitialise() {
  }


  /**
  * Do something onAfterRoute
  */
  function onAfterRoute() {
  }

  /**
  * Do something onAfterDispatch
  */
  function onAfterDispatch() {
  }

  /**
  * Do something onAfterRender
  */
  function onAfterRender() {

    // make sure we're on the right page
    $menuID = $this->params->get('menuID');
    if ( $itemID > 0 && $itemID != '*' ) {
      // get the url
      $uri = clone(JURI::getInstance());
      $app = JFactory::getApplication();
      $router = $app->getRouter();
      $result = $router->parse($uri);
      if ( $result['Itemid'] != $this->params->get('menuID')  ) {
        return false;
      }
    }

    // check if it's a posted form
    if ( $this->params->get('type') == 'POST' ) {
      if ( !isset($_POST) || !(count($_POST) > 0) ) {
        return false;
      }
    }

    // execute additional custom php code
    if ( $this->params->get('extra_code') ) {
       if ( !eval($this->params->get('extra_code')) ) {
         return false;
       }
    }

    $this->podio->app_id    =  $this->params->get('LeadTracker_ID');
    $this->podio->app_token =  $this->params->get('LeadTracker_Token');

    // authenticate to podio
    if ( $this->podio->authApp($this->podio->app_id, $this->podio->app_token) ) {

      // take the configured map array. fields to post to podio
      eval('$fields = '.$this->params->get('map_array'));

      // other fields
      $fields['entrydate'] = array('start' => date('Y-m-d H:i:s'), 'end' => date('Y-m-d H:i:s'));

      $this->podio->trackLead($fields);
    }
  }


}




class JPodio {

  public $client_id;
  public $client_secret;

  function __construct($client_id='', $client_secret='') {
    if ( $client_id ) {
      $this->client_id = $client_id;
    }
    if ( $client_secret ) {
      $this->client_secret = $client_secret;
    }
    require_once JPATH_ROOT.'/plugins/system/podio_leads/podio_api/PodioAPI.php';
    $this->api = Podio::instance($this->client_id, $this->client_secret);
  }


  // authenticates to Podio as app
  function authApp($app_id, $app_token) {
    $this->app_id    = $app_id;
    $this->app_token = $app_token;
    try {
      $this->api->authenticate('app', array('app_id' => $this->app_id, 'app_token' => $this->app_token));

      // Authentication was a success, now you can start making API calls.
      return true;
    }
    catch (PodioError $e) {
      // Something went wrong. Examine $e->body['error_description'] for a description of the error.
      return false;
    }
  }

  function trackLead($fields) {
    if ( !isset($fields['email']) || $fields['email'] == '' ) {
      return false;
    }

    if ( $item_id = $this->_checkItem($fields, 14904601/*email*/, $fields['email'] ) ) {
      $item = $this->_updateItem($item_id, $fields);
    } else {
      $item = $this->_addItem($fields);
    }
    return $item;
  }


  private function _addItem($fields) {
    try {
      $this->api->item->create($this->app_id, array('fields' => $fields));
    }
    catch (PodioError $e) {
      var_dump($fields);
      // Something went wrong. Examine $e->body['error_description'] for a description of the error.
      echo 'Error create: '. $e->body['error_description'];
      return false;
    }

  }


  private function _updateItem($item_id, $fields) {
    try {
      $this->api->item->update($item_id, array('fields' => $fields));
    } catch (PodioError $e) {
      echo 'Error updateing: '.$e->body['error_description'];
      return false;
    }
  }


  private function _checkItem($fields, $field, $value='') {
     $items = $this->api->item->getItems($this->app_id, array(
       $field => $value, // email field id
     ));
     if ( count($items['items']) > 0 ) {
       // TODO handle case when there's more then one item
       return $items['items'][0]['item_id'];
     }
     return false;
  }


}