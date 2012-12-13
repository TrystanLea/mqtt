<?php
  /*

  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org
 
  */

  function mqtt_get()
  {
    $result = db_query("SELECT * FROM mqtt");
    $row = db_fetch_array($result);

    if (!$row)
    {
      db_query("INSERT INTO mqtt ( userid, apikey, mhost, mport, mnode, mtype, mqos, mtopic, muser, mpass, mexpression, mfields, remotedomain, remoteapikey, remotesend) VALUES ( '0' , '' , '127.0.0.1' , '1883', '18', 'sub', '0', '#', '', '', '', '', 'emoncms.org', 'YOURAPIKEY', 'false');");
      $result = db_query("SELECT * FROM mqtt");
      $row = db_fetch_array($result);
    }
    return $row;
  }

  function mqtt_set($userid,$apikey,$mhost,$mport,$mnode,$mtype,$mqos,$mtopic,$muser,$mpass,$mexpression,$mfields,$remotedomain,$remoteapikey,$remotesend)
  {
    db_query("UPDATE mqtt SET `userid` = '$userid', `apikey` = '$apikey', `mhost` = '$mhost' , `mport` = '$mport' , `mnode` = '$mnode' , `mtype` = '$mtype' , `mqos` = '$mqos' , `mtopic` = '$mtopic' , `muser` = '$muser' , `mpass` = '$mpass' ,`mexpression` = '$mexpression' ,`mfields` = '$mfields' ,`remotedomain` = '$remotedomain', `remoteapikey` = '$remoteapikey', `remotesend` = '$remotesend' ");
  }

  function mqtt_running()
  { 
    $time = time();
    db_query("UPDATE mqtt SET `running` = '$time' ");
  }
