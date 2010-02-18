<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

// Check user permission
if(!$user->group['create_tickets'])
{
	$_SESSION['last_page'] = $uri->geturi();
	header("Location: ".$uri->anchor('user','login'));
}

// Include reCaptcha
require(TRAQPATH.'inc/recaptchalib.php');

addcrumb($uri->geturi(),l('new_ticket'));

// Do the New Ticket stuff...
include(TRAQPATH.'inc/ticket.class.php');
$ticket = new Ticket;

if(isset($_POST['summary']))
{
	// Check reCaptcha
	if(settings('recaptcha_enabled'))
	{
		$resp = recaptcha_check_answer(settings('recaptcha_privkey'),$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		
		if(!$resp->is_valid) {
			$recaptcha_error = $resp->error;
			$errors['recaptcha'] = true;
		}
	}
	
	// Set guest name cookie
	if(!$user->loggedin)
		setcookie('guestname',$_POST['name'],time()+50000,'/');
	
	$data = array(
		'summary' => $_POST['summary'],
		'body' => $_POST['body'],
		'type' => $_POST['type'],
		'priority' => $_POST['priority'],
		'severity' => $_POST['severity'],
		'milestone_id' => $_POST['milestone'],
		'version_id' => $_POST['version'],
		'component_id' => $_POST['component'],
		'assigned_to' => $_POST['assign_to'],
		'private' => $_POST['private'],
		'user_name' => $_POST['name']
	);
	if($ticket->check($data) && !count($errors))
	{
		$ticket->create($data);
		header("Location: ".$uri->anchor($project['slug'],'ticket-'.$ticket->ticket_id));
	}
	else
	{
		$errors = $ticket->errors;
		if(isset($recaptcha_error))
			$errors['recaptcha'] = l('error_recaptcha');
	}
}

($hook = FishHook::hook('handler_newticket')) ? eval($hook) : false;

require(template('new_ticket'));
?>