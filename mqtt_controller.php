<?php
  /*

  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org
 
  */

  // no direct access
  defined('EMONCMS_EXEC') or die('Restricted access');

  function mqtt_controller()
  {
    global $mysqli, $user, $session, $route;

    include "Modules/mqtt/mqtt_model.php";
    $mqtt = new Mqtt($mysqli);

    if ($route->action == "view" && $session['write'])
    { 
      $settings = $mqtt->get();
      if ($route->format == 'html') $result = view("Modules/mqtt/mqtt_view.php", array('settings'=>$settings));
       //if ((time()-$settings['running'])<30) 
         //$result = array('success',"MQTT interface script is up and running");
       //else
         //$result = array('important',"No data has been recieved from MQTT in the last 30s. Check if the MQTT interface script is running, if not you may need to configure cron");
    }

    if ($route->action == "set" && $session['write'])
    { 
      $userid = $session['userid'];
      $apikey = $user->get_apikey_write($userid);
      $mhost = urldecode(get('mhost'));
      $mport = intval(get('mport'));
      $mnode = intval(get('mnode'));
      $mtype = urldecode(get('mtype'));
      $mqos = intval(get('mqos'));
      $mtopic = urldecode(get('mtopic'));
      $muser = urldecode(get('muser'));
      $mpass = urldecode(get('mpass'));
      $mexpression = urldecode(get('mexpression'));
      $mfields = urldecode(get('mfields'));
      $remotedomain = urldecode(get('remotedomain'));
      $remoteapikey = $mysqli->real_escape_string(preg_replace('/[^.\/A-Za-z0-9]/', '', get('remoteapikey')));

      $remotesend = false;
      if ($remotedomain && $remoteapikey) {
        $result = file_get_contents("http://".$remotedomain."/time/local.json?apikey=".$remoteapikey);
        if (substr($result,1,1)=='t') { $remotesend = true; }
      }

      $mqtt->set($userid,$apikey,$mhost,$mport,$mnode,$mtype,$mqos,$mtopic,$muser,$mpass,$mexpression,$mfields,$remotedomain,$remoteapikey,$remotesend);

      $result = "MQTT settings updated"; 
    }

    return array('content'=>$result);
  }

