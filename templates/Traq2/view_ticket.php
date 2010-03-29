<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=settings('title')?> / <?=$project['name']?> / <?=l('tickets')?> / <?=$ticket['summary']?> (<?=l('ticket_x',$ticket['ticket_id'])?>)</title>
		<? require(template('headerinc')); ?>
	</head>
	<body>
		<? require(template('header')); ?>
		
		<? require(template('breadcrumbs')); ?>
		
		<div id="ticket">
			<div class="date">
				<p>Opened <?=timesince($ticket['created'])?> ago</p>
				<p>Last modified <?=($ticket['updated'] ? timesince($ticket['updated']).' ago' : 'Never')?></p>
			</div>
			<h1 class="summary"><?=$ticket['summary']?> <small>(<?=l('ticket_x',$ticket['ticket_id'])?>)</small> <? if($user->group['is_admin'] or in_array($user->info['id'],$project['managers'])) { ?>
				<input type="button" onclick="if(confirm('<?=l('delete_ticket_confirm',$ticket['ticket_id'])?>')) { window.location='<?=$uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'],'delete')?>' }" value="<?=l('delete')?>" />
				<? } ?></h1>
			<table class="properties">
				<tr>
					<th id="h_owner"><?=l('reported_by')?>:</th>
					<td headers="h_owner"><?=$ticket['user_name']?></td>
					<th id="h_assignee"><?=l('assigned_to')?>:</th>
					<td headers="h_assignee"><?=$ticket['assignee']['username']?></td>
				</tr>
				<tr>
					<th id="h_type"><?=l('type')?>:</th>
					<td headers="h_type"><?=ticket_type($ticket['type'])?></td>
					<th id="h_priority"><?=l('priority')?>:</th>
					<td headers="h_priority"><?=ticket_priority($ticket['priority'])?></td>
				</tr>
				<tr>
					<th id="h_severity"><?=l('severity')?>:</th>
					<td headers="h_severity"><?=ticket_severity($ticket['severity'])?></td>
					<th id="h_component"><?=l('component')?>:</th>
					<td headers="h_component"><?=$ticket['component']['name']?></td>
				</tr>
				<tr>
					<th id="h_milestone"><?=l('milestone')?>:</th>
					<td headers="h_milestone"><?=$ticket['milestone']['milestone']?></td>
					<th id="h_version"><?=l('version')?>:</th>
					<td headers="h_version"><?=$version['version']?></td>
				</tr>
				<tr>
					<th id="h_status"><?=l('status')?>:</th>
					<td headers="h_status"><?=ticket_status($ticket['status'])?></td>
				</tr>
				<? ($hook = FishHook::hook('template_view_ticket_properties')) ? eval($hook) : false; ?>
			</table>
			<div class="description">
				<h3 id="description"><?=l('description')?></h3>
				<p>
					<?=formattext($ticket['body'])?> 
				</p>
				<h3><?=l('attachments')?></h3>
				<p id="attachments">
					<ul>
					<? foreach($ticket['attachments'] as $attachment) { ?>
						<li>
							<? if($user->group['is_admin'] or in_array($user->info['id'],$project['managerids'])) { ?><form action="<?=$uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'])?>" method="post"><? } ?>
							<strong><a href="<?=$uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'],'attachment-'.$attachment['id'])?>"><?=$attachment['name']?></a></strong> added by <?=$attachment['owner_name']?> <?=timesince($attachment['uploaded'])?> ago.
							<? if($user->group['is_admin'] or in_array($user->info['id'],$project['managerids'])) { ?><input type="hidden" name="action" value="delete_attachment" /><input type="hidden" name="attach_id" value="<?=$attachment['id']?>" /><input type="submit" value="<?=l('delete')?>" /></form><? } ?>
						</li>
					<? } ?>
					</ul>
				</p>
				<? if($user->group['add_attachments']) { ?>
				<p>
					<form action="<?=$uri->anchor($project['slug'],'ticket-'.$ticket['ticket_id'])?>" method="post" enctype="multipart/form-data">
						<input type="hidden" name="action" value="attach_file" />
						<label><?=l('attach_file')?>: <input type="file" name="file" /> <input type="submit" value="<?=l('attach')?>" /></label>
					</form>
				</p>
				<? } ?>
			</div>
		</div>
		
		<h2><?=l('ticket_history')?></h2>
		<div id="ticket_history">
			<? foreach($ticket['changes'] as $change) { ?>
			<div class="ticket_prop_change">
				<h3><?=timesince($change['timestamp'],true)?> ago by <?=$change['user_name']?></h3>
				<div class="ticket_change_actions">
					<? if($user->group['is_admin'] or in_array($user->info['id'],$project['managers'])) { ?>
					<form action="<?=$uri->geturi()?>" method="post">
						<input type="hidden" name="action" value="delete_comment" />
						<input type="hidden" name="comment" value="<?=$change['id']?>" />
						<input type="submit" value="<?=l('delete')?>" />
					</form>
					<? } ?>
				</div>
				<? if(count($change['changes']) > 0) { ?>
				<ul>
					<? foreach($change['changes'] as $row) { ?>
					<li><?=l('ticket_history_'.$row->property.iif($row->action,'_'.$row->action),$row->from,$row->to)?></li>
					<? } ?>
				</ul>
				<? } ?>
				<? if($change['comment'] != '') { ?>
				<div class="change_comment">
					<?=formattext($change['comment'])?>
				</div>
				<? } ?>
			</div>
			<? } ?>
		</div>
		
		<? if($user->group['update_tickets']) { ?>
		<form action="<?=$uri->geturi()?>" method="post">
		<input type="hidden" name="update" value="1" />
		<h2><?=l('update_ticket')?></h2>
		<div id="update_ticket">
			<? if(count($errors)) { ?>
			<div class="message error">
				<? foreach($errors as $error) { ?>
				<?=$error?><br />
				<? } ?>
			</div>
			<? } ?>
			<textarea name="comment"></textarea>
			<fieldset class="properties">
				<legend><?=l('ticket_properties')?></legend>
				<table class="properties">
					<tr>
						<th class="col1"><?=l('type')?></th>
						<td>
							<select name="type">
								<? foreach(ticket_types() as $type) { ?>
								<option value="<?=$type['id']?>"<?=iif($type['id']==$ticket['type'],' selected="selected"')?>><?=$type['name']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('assigned_to')?></thd>
						<td>
							<select name="assign_to">
								<option value="0"></option>
								<? foreach(project_managers() as $manager) { ?>
								<option value="<?=$manager['id']?>"<?=iif($manager['id']==$ticket['assigned_to'],' selected="selected"')?>><?=$manager['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1"><?=l('priority')?></th>
						<td>
							<select name="priority">
								<? foreach(ticket_priorities() as $priority) { ?>
								<option value="<?=$priority['id']?>"<?=iif($priority['id']==$ticket['priority'],' selected="selected"')?>><?=$priority['name']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('severity')?></th>
						<td>
							<select name="severity">
								<? foreach(ticket_severities() as $severity) { ?>
								<option value="<?=$severity['id']?>"<?=iif($severity['id']==$ticket['severity'],' selected="selected"')?>><?=$severity['name']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1"><?=l('milestone')?></th>
						<td>
							<select name="milestone">
								<? foreach(project_milestones() as $milestone) { ?>
								<? if(!$milestone['locked'] or ($milestone['locked'] && $ticket['milestone_id'] == $milestone['id'])) { ?>
								<option value="<?=$milestone['id']?>"<?=iif($milestone['id']==$ticket['milestone_id'],' selected="selected"')?>><?=$milestone['milestone']?></option>
								<? } ?>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('version')?></th>
						<td>
							<select name="version">
								<option value="0"></option>
								<? foreach(project_versions() as $version) { ?>
								<option value="<?=$version['id']?>"<?=iif($version['id']==$ticket['version_id'],' selected="selected"')?>><?=$version['version']?></option>
								<? } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="col1"><?=l('component')?></th>
						<td>
							<select name="component">
								<option value="0"><?=l('none')?></option>
								<? foreach(project_components() as $component) { ?>
								<option value="<?=$component['id']?>"<?=iif($component['id']==$ticket['component_id'],' selected="selected"')?>><?=$component['name']?></option>
								<? } ?>
							</select>
						</td>
						<th class="col2"><?=l('summary')?></th>
						<td><input type="text" name="summary" value="<?=$ticket['summary']?>" /></td>
					</tr>
					<? ($hook = FishHook::hook('template_update_ticket_properties')) ? eval($hook) : false; ?>
					<tr>
						<th class="col1"><?=l('action')?></th>
						<td>
							<? if(!$ticket['closed']) { ?>
							<input type="radio" name="action" value="mark" checked="checked" id="mark" /> <label for="mark"><?=l('mark_as')?></label> <select name="mark_as">
								<? foreach(ticket_status_list() as $status) { ?>
									<option value="<?=$status['id']?>"<?=iif($status['id']==$ticket['status'],' selected="selected"')?>><?=$status['name']?></option>
								<? } ?>
							</select>
							<br />
							<input type="radio" name="action" value="close" id="close" /> <label for="close"><?=l('close_as')?></label> <select name="close_as">
								<? foreach(ticket_status_list(0) as $status) { ?>
									<option value="<?=$status['id']?>"<?=iif($status['id']==$ticket['status'],' selected="selected"')?>><?=$status['name']?></option>
								<? } ?>
							</select>
							<? } else if($ticket['closed']) { ?>
							<input type="radio" name="action" value="reopen" id="reopen" /> <label for="reopn"><?=l('reopen_as')?></label> <select name="reopen_as">
								<? foreach(ticket_status_list() as $status) { ?>
									<option value="<?=$status['id']?>"<?=iif($status['id']==$ticket['status'],' selected="selected"')?>><?=$status['name']?></option>
								<? } ?>
							</select>
							<? } ?>
						</td>
						<th class="col2"><label for="private"><?=l('private_ticket')?></label></th>
						<td><input type="checkbox" name="private" id="private" value="1"<?=iif($ticket['private'],' checked="checked"')?> /></td>
					</tr>
					<? if(!$user->loggedin) { ?>
					<tr>
						<th><?=l('your_name')?></th>
						<td><input type="text" name="name" value="<?=$_COOKIE['guestname']?>" />	</td>
					</tr>
					<? if(settings('recaptcha_enabled')) { ?>
					<tr>
						<th><?=l('recaptcha')?></th>
						<td colspan="2"><?=recaptcha_get_html(settings('recaptcha_pubkey'), $recaptcha_error)?></td>
					</tr>
					<? } ?>
					<? } ?>
					<tr>
						<td></td>
						<td><input type="submit" value="<?=l('update')?>" /></td>
					</tr>
				</table>
			</fieldset>
		</div>
		</form>
		<? } ?>
		<? require(template('footer')); ?>
	</body>
</html>