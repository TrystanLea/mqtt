<?php
  /*

  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org

  */
    
    //require "../input/input_model.php";
    //require "../feed/feed_model.php";
    //require "../input/process_model.php";

    define('EMONCMS_EXEC', 1);
    declare(ticks = 1);

    pcntl_signal(SIGTERM, "signal_handler");
    pcntl_signal(SIGINT, "signal_handler");
  
    function signal_handler($signal) {
        switch($signal) {
            case SIGTERM:
                print "Caught SIGTERM\n";
                exit;
            case SIGKILL:
                print "Caught SIGKILL\n";
                exit;
            case SIGINT:
                print "Caught SIGINT\n";
                exit;
        }
    }  
  
    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));    
    
    $fp = fopen("importlockmqtt", "w");
    if (! flock($fp, LOCK_EX | LOCK_NB)) { echo "Already running\n"; die; }
    
    chdir(dirname(__FILE__));
    
    require "../../settings.php";
    include "../../db.php";
    require "SAM/php_sam.php";
    db_connect();
    
    include "mqtt_model.php";
    mqtt_running();
    
    $settings = mqtt_get();
    $apikey = $settings['apikey'];
    $mhost = $settings['mhost'];
    $mport = $settings['mport'];
    $mnode = $settings['mnode'];
    $mtype = $settings['mtype'];
    $mqos = $settings['mqos'];
    $mtopic = $settings['mtopic'];
    $muser = $settings['muser'];
    $mpass = $settings['mpass'];
    $mfields = $settings['mfields'];
    $mexpression = $settings['mexpression'];
    
    $remotedomain = $settings['remotedomain'];
    $remoteapikey = $settings['remoteapikey'];
    
    $sent_to_remote = false;
    $result = file_get_contents("http://".$remotedomain."/time/local.json?apikey=".$remoteapikey);
    if (substr($result,1,1)=='t') {echo "Remote upload enabled - details correct \n"; $sent_to_remote = true; }
    
    //create a new connection object
    $conn = new SAMConnection();
     
    //start initialise the connection
    $conn->connect(SAM_MQTT, array( SAM_HOST => $mhost,
                                    SAM_PORT => $mport));
     
    //subscribe to topic /moderation/+
    $subUp = $conn->subscribe('topic://'.$mtopic) OR die('could not subscribe');
     
    $start = time();
    $ni = 0; 
    $remotedata = "[";
    $start_time = time();
    $remotetimer = time();

    while($conn)
    {
        //receive latest message on topic $subUp
        $msgUp = $conn->receive($subUp);
        $topic = $msgUp->header->SAM_MQTT_TOPIC;

        $body = $msgUp->body;
        
        // Setup as Running
        if (time()-$start>10) {
            $start = time();
            
            $settings = mqtt_get();
            if ($settings['apikey'] !=$apikey) $apikey = $settings['apikey'];
            if ($settings['mhost'] !=$mhost) {$mhost = $settings['mhost']; echo $mhost;}
            
            if ($settings['remotedomain'] !=$remotedomain || $settings['remoteapikey'] !=$remoteapikey)
            { 
              $result = file_get_contents("http://".$remotedomain."/time/local.json?apikey=".$remoteapikey);
              if (substr($result,1,1)=='t') {echo "Remote upload enabled - details correct \n"; $sent_to_remote = true; }
            }
            mqtt_running();
        }

        // Forward data to remote emoncms
        if (time()-$remotetimer>10 && $sent_to_remote == true)
        {
            $remotetimer = time();
            
            $remotedata .= "]";
            //echo "Sending remote data";
            //echo $remotedata."\n";
            echo "/input/bulk.json?apikey=".$remoteapikey."&data=".$remotedata;
            getcontent($remotedomain,80,"/input/bulk.json?apikey=".$remoteapikey."&data=".$remotedata);
            $ni = 0; 
            $remotedata = "[";
            $start_time = time();
        }

/*
        $xml = simplexml_load_string($body);
        $topic = explode('/',$intopic);
        
        $action = $topic[4];

        # changed use to update a new entry with rackfish details
        $userid=$xml->fbId;
        $filename=$xml->fileName;
        $path=$xml->filePath;
*/

// <msg><src>CC128-v1.29</src><dsb>00852</dsb><time>15:43:23</time><tmpr>22.2</tmpr><sensor>5</sensor><id>00508</id^Ztype>1</type><ch1><watts>00500</watts></ch1></msg>

        // Build data to eventually send to emoncms
        $data = $body;
        if ($data && $data!="\n")
        {
        
            $xml = @simplexml_load_string($data);
            //echo "SAM RX:".$data;
        
            //$url = "/emoncms/input/post?apikey=".$apikey."&node=".$values[1]."&csv=".$msubs;
            //getcontent("localhost",80,$url);
            
            if (isset($xml->sensor)) {

                $url = "/emoncms/input/post?apikey=".$apikey."&node=".$mnode."&csv=".$msubs;
                getcontent("localhost",80,$url);

                if ($sent_to_remote == true)
                {
                  if ($ni!=0) $remotedata .= ",";
                  $td = intval(time() - $start_time);
                  $wattval = ltrim($xml->ch1->watts,'0');
                  if (empty($wattval)) {$wattval = 0;}
                  //writetodb(17,$xml->sensor,$wattval);
                  
                  $remotedata .= '['.$td.','.$mnode.',';
                  for ($i=0; $i<$xml->sensor;$i++) {
                      $remotedata .= 'null,';
                      }
                  $remotedata .= $wattval.']'; $ni++;
                }
            }
            
    }
}

function writetodb($sensor,$wattval) {
    $name = "node".$nodeid."_".($i-1);
    $id = get_input_id($session['userid'],$name);
    $value = $wattval;
    $time = $start_time + intval($node[0]);
    
    if ($id==0) {
        $id = create_input_timevalue($session['userid'],$name,$nodeid,$time,$value);
    } else {				
        set_input_timevalue($id,$time,$value);
    }
    $inputs[] = array('id'=>$id,'time'=>$time,'value'=>$value);
    new_process_inputs($session['userid'],$inputs);

}

function getcontent($server, $port, $file)
{
    echo "Output content\r\n";
    $cont = "";
    $ip = gethostbyname($server);
    $fp = fsockopen($ip, $port);
    if (!$fp) {
        return "Unknown";
    } else {
        $com = "GET $file HTTP/1.1\r\nAccept: */*\r\nAccept-Language: de-ch\r\nAccept-Encoding: gzip, deflate\r\nUser-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)\r\nHost: $server:$port\r\nConnection: Keep-Alive\r\n\r\n";
        fputs($fp, $com);
        fclose($fp);
    }
}
