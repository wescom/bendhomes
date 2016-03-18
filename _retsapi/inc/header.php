<?php

// set your timezone
date_default_timezone_set('America/New_York');

// pull in the packages managed by Composer
include("RETSABSPATH.php");
include(RETSABSPATH."/vendor/autoload.php");

// setup your configuration
$config = new \PHRETS\Configuration;
$config->setLoginUrl('http://rets172lax.raprets.com:6103/CentralOregon/COAR/Login.aspx');
$config->setUsername('BBUL');
$config->setPassword('JGrady');

// optional.  value shown below are the defaults used when not overridden
$config->setRetsVersion('1.7.2'); // see constants from \PHRETS\Versions\RETSVersion
// $config->setUserAgent('PHRETS/2.0');
$config->setUserAgent('BBulletin/1.72');
// $config->setUserAgentPassword($rets_user_agent_password); // string password, if given
$config->setHttpAuthenticationMethod('digest'); // or 'basic' if required
$config->setOption('use_post_method', false); // boolean
$config->setOption('disable_follow_location', false); // boolean

// get a session ready using the configuration
$rets = new \PHRETS\Session($config);

// print_r($rets);

// make the first request
$connect = $rets->Login();

// print_r($rets);

$universalqueries = array(
  'Property' => array(
    'BUSI' => '(ListingRid=0+)',
    'COMM' => '(ListingRid=0+)',
    'FARM' => '(ListingRid=0+)',
    'LAND' => '(ListingRid=0+)',
    'MULT' => '(ListingRid=0+)',
    'RESI' => '(ListingRid=0+)'
  ),
  'ActiveAgent' => array(
    'MEMB' => '(MemberNumber=0+)'
    // 'MEMB' => '(IsActive=1)'
  ),
  'Agent' => array(
    'MEMB' => '(MemberNumber=0+)'
    // 'MEMB' => '(IsActive=1)'
  ),
  'MemberAssociation' => array(
    'ASSC' => '(MemberNumber=0+)'
  ),
  'Office' => array(
    'OFFI' => '(OfficeNumber=0+)'
  ),
  'OfficeAssociation' => array(
    'ASSC' => '(OfficeAssociationKey=0+)'
  ),
  'OpenHouse' => array(
    'OPEN' => '(ListingRid=0+)'
  ),
);

$universalkeys = array(
  'Property' => array(
    'BUSI' => 'ListingRid',
    'COMM' => 'ListingRid',
    'FARM' => 'ListingRid',
    'LAND' => 'ListingRid',
    'MULT' => 'ListingRid',
    'RESI' => 'ListingRid'
  ),
  'ActiveAgent' => array(
    'MEMB' => 'MemberNumber'
    // 'MEMB' => '(IsActive=1)'
  ),
  'Agent' => array(
    'MEMB' => 'MemberNumber'
    // 'MEMB' => '(IsActive=1)'
  ),
  'MemberAssociation' => array(
    'ASSC' => 'MemberNumber'
  ),
  'Office' => array(
    'OFFI' => 'OfficeNumber'
  ),
  'OfficeAssociation' => array(
    'ASSC' => 'OfficeAssociationKey'
  ),
  'OpenHouse' => array(
    'OPEN' => 'ListingRid'
  ),
);



?>
