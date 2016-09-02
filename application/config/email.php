<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| EMAIL CONFING
| -------------------------------------------------------------------
| Configuration of outgoing mail server.
| */
$config['protocol']='smtp';
$config['smtp_host']='mail.fleetsmarts.net';
$config['smtp_port']='25';
$config['smtp_timeout']='30';
$config['smtp_user']='system@fleetsmarts.net';
$config['smtp_pass']='retret13';
$config['charset']='utf-8';
$config['newline']="\r\n";
$config['mailtype']="html";

/* End of file email.php */
/* Location: ./system/application/config/email.php */