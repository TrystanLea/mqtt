<?php
  /*

  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  ---------------------------------------------------------------------
  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org
 
  */

  global $user;
?>

<h2>MQTT</h2>

<div style="width:300px; float:left;">
<form action="set" method="GET" >
<p><b>MQTT connected to account: <?php echo $user->get_username($settings['userid']); ?></b></p>


<p>Host: IP Address / Domain<br><input type="text" name="mhost" value="<?php echo $settings['mhost']; ?>" /></p>
<p>Port:</p>
<p>
<input style="margin-bottom:6px;" type="radio" name="mport" value="1883" <?php if ($settings['mport']==1883) echo "checked" ?> > 1883 &nbsp; &nbsp;
<input style="margin-bottom:6px;" type="radio" name="mport" value="8883" <?php if ($settings['mport']==8883) echo "checked" ?> > 8883 
</p>

<p>Node:<br><input type="text" name="mnode" value="<?php echo $settings['mnode']; ?>" /></p>

<p>Topic:<br><input type="text" name="mtopic" value="<?php echo $settings['mtopic']; ?>" /></p>

</div>

<div style="width:300px; float:left;" >
<p><b>Not currently used</b></p>

<p>Type:</p>
<p>
<input style="margin-bottom:6px;" type="radio" name="mtype" value="pub" <?php if ($settings['mtype']=="pub") echo "checked" ?> > Pub 
&nbsp; &nbsp;
<input style="margin-bottom:6px;" type="radio" name="mtype" value="sub" <?php if ($settings['mtype']=="sub") echo "checked" ?> > Sub 
</p>

<p>QoS:</p>
<p>
<input style="margin-bottom:6px;" type="radio" name="mqos" value="0" <?php if ($settings['mqos']==0) echo "checked" ?> > 0 
&nbsp; &nbsp;
<input style="margin-bottom:6px;" type="radio" name="mqos" value="1" <?php if ($settings['mqos']==1) echo "checked" ?> > 1 
&nbsp; &nbsp;
<input style="margin-bottom:6px;" type="radio" name="mqos" value="2" <?php if ($settings['mqos']==2) echo "checked" ?> > 2 
</p>

<!--<p>User:<br><input type="text" name="muser" value="<?php echo $settings['muser']; ?>" /></p>
<p>Pass:<br><input type="text" name="mpass" value="<?php echo $settings['mpass']; ?>" /></p>-->
<p>Fields:<br><input type="text" name="mfields" value="<?php echo $settings['mfields']; ?>" /></p>
<p>Expression:<br><input type="text" name="mexpression" value="<?php echo $settings['mexpression']; ?>" /></p>

</div>

<div style="width:300px; float:left;" >
<p><b>Forward data to remote emoncms</b></p>

<p>Domain name<br><input type="text" name="remotedomain" value="<?php echo $settings['remotedomain']; ?>" /></p>
<p>Write apikey<br><input type="text" name="remoteapikey" value="<?php echo $settings['remoteapikey']; ?>" /></p>
<?php if ($settings['remotesend']) echo "<p><b>Authentication successful</b></p>"; else echo "<p><b>Incorrect remote server details</b></p>"; ?>

<br><input type="submit" class="btn" value="Save" />

</form>
</div>
