<?php

$schema['mqtt'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'name' => array('type' => 'text'),
  'userid' => array('type' => 'int(11)'),
  'apikey' => array('type' => 'text'),
  'mhost' => array('type' => 'text'),
  'mport' => array('type' => 'int(4)'),
  'mnode' => array('type' => 'int(4)'),
  'mtype' => array('type' => 'text'),
  'mqos' => array('type' => 'int(1)'),
  'mtopic' => array('type' => 'text'),
  'muser' => array('type' => 'text'),
  'mpass' => array('type' => 'text'),
  'mexpression' => array('type' => 'text'),
  'mfields' => array('type' => 'text'),
  'running' => array('type' => 'int(11)'),
  'remotedomain' => array('type' => 'text'),
  'remoteapikey' => array('type' => 'text'),
  'remotesend' => array('type' => 'int(11)')
);
