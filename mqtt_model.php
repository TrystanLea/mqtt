<?php
  /*

  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org
 
  */

class Mqtt
{
  private $mysqli;

  public function __construct($mysqli)
  {
      $this->mysqli = $mysqli;
  }

  public function get()
  {
    $result = $this->mysqli->query("SELECT * FROM mqtt");
    $row = $result->fetch_array();

    if (!$row)
    {
      $this->mysqli->query("INSERT INTO mqtt ( userid, apikey, mhost, mport, mnode, mtype, mqos, mtopic, muser, mpass, mexpression, mfields, remotedomain, remoteapikey, remotesend) VALUES ( '0' , '' , '127.0.0.1' , '1883', '18', 'sub', '0', '#', '', '', '', '', 'emoncms.org', 'YOURAPIKEY', 'false');");
      $result = $this->mysqli->query("SELECT * FROM mqtt");
      $row = $result->fetch_array();
    }
    return $row;
  }

  public function set($userid,$apikey,$mhost,$mport,$mnode,$mtype,$mqos,$mtopic,$muser,$mpass,$mexpression,$mfields,$remotedomain,$remoteapikey,$remotesend)
  {
    $this->mysqli->query("UPDATE mqtt SET `userid` = '$userid', `apikey` = '$apikey', `mhost` = '$mhost' , `mport` = '$mport' , `mnode` = '$mnode' , `mtype` = '$mtype' , `mqos` = '$mqos' , `mtopic` = '$mtopic' , `muser` = '$muser' , `mpass` = '$mpass' ,`mexpression` = '$mexpression' ,`mfields` = '$mfields' ,`remotedomain` = '$remotedomain', `remoteapikey` = '$remoteapikey', `remotesend` = '$remotesend' ");
  }

  public function running()
  { 
    $time = time();
    $this->mysqli->query("UPDATE mqtt SET `running` = '$time' ");
  }
}
