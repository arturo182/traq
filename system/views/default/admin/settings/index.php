<div class="traq_settings content">
	<h2 id="page_title"><?php echo l('traq_settings'); ?></h2>
	<form action="<?php echo Request::full_uri(); ?>" method="post">
		<?php if (isset($errors)) { show_errors($errors); } ?>
		<fieldset class="tabular section box">
			<legend><?php echo l('traq'); ?></legend>
			<div class="group">
				<label><?php echo l('traq_title'); ?></label>
				<?php echo Form::text('settings[title]', array('value' => settings('title'))); ?>
			</div>
			<div class="group">
				<label><?php echo l('default_language'); ?></label>
				<?php echo Form::select('settings[locale]', locale_select_options(), array('value' => settings('locale'))); ?>
			</div>
			<div class="group">
				<label><?php echo l('theme'); ?></label>
				<?php echo Form::select('settings[theme]', theme_select_options(), array('value' => settings('theme'))); ?>
			</div>
		</fieldset>

		<fieldset class="tabular section box">
			<legend><?php echo l('users'); ?></legend>
			<div class="group">
				<label><?php echo l('allow_registration'); ?></label>
				<?php echo Form::select('settings[allow_registration]',
					array(array('label' => l('yes'), 'value' => 1), array('label' => l('no'), 'value' => 0)),
					array('value' => settings('allow_registration'))
					);
				?>
			</div>
		</fieldset>

		<fieldset class="tabular section box">
			<legend><?php echo l('date_and_time'); ?></legend>
			<div class="group">
				<label><?php echo l('date_time_format'); ?></label>
				<?php echo Form::text('settings[date_time_format]', array('value' => settings('date_time_format'))); ?>
			</div>
			<div class="group">
				<label><?php echo l('date_format'); ?></label>
				<?php echo Form::text('settings[date_format]', array('value' => settings('date_format'))); ?>
			</div>
			<div class="group">
				<label><?php echo l('timeline_day_format'); ?></label>
				<?php echo Form::text('settings[timeline_day_format]', array('value' => settings('timeline_day_format'))); ?>
			</div>
			<div class="group">
				<label><?php echo l('timeline_time_format'); ?></label>
				<?php echo Form::text('settings[timeline_time_format]', array('value' => settings('timeline_time_format'))); ?>
			</div>
		</fieldset>
		<div class="actions">
			<input type="submit" value="<?php echo l('save'); ?>" />
		</div>
	</form>
</div>