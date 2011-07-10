<?=form_open($form_base)?>
	<table class="mainTable padTable" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th style="width:50%;" class="header">Preference</th><th>Setting</th>
			</tr>
		</thead>
		<tbody>
			<tr class="even">
				<td><strong><label for="username"><?php echo lang('username'); ?></label></strong></td>
				<td><input type="text" name="username" value="<?= $settings['username'] ?>" id="username" class="input fullfield" size="20" maxlength="120"></td>
			</tr>
			<tr class="odd">
				<td><strong><label for="password"><?php echo lang('password'); ?></label></strong></td>
				<td><input type="password" name="password" value="<?= $settings['password']; ?>" id="password" class="input fullfield" size="20" maxlength="120"></td>
			</tr>
			<tr class="even">
				<td><strong><label for="app_key"><?php echo lang('application_key'); ?></label></strong></td>
				<td><input type="text" name="app_key" value="<?= $settings['app_key'] ?>" id="app_key" class="input fullfield" size="20" maxlength="120"></td>
			</tr>
		</tbody>
	</table>
	<input type="submit" name="submit" value="Submit" class="submit">
<?= form_close(); ?>
