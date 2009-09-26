<?php /* Smarty version 2.6.14, created on 2009-09-26 01:39:08
         compiled from steps/step_database.tpl.php */ ?>
<h2>Database Setup</h2>

<form action="index.php" method="POST">
<input type="hidden" name="step" value="<?php echo @STEP_DATABASE; ?>
">

<?php if ($this->_tpl_vars['failed']): ?>
<span class='bad'>Database Connection Failed!  Please check your settings and try again.</span><br>
<br>
<?php endif; ?>

<b>Database Driver:</b> (from PHP environment)<br>
<select name="db_driver">
	<?php $_from = $this->_tpl_vars['drivers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['driver']):
?>
	<option value="<?php echo $this->_tpl_vars['k']; ?>
" <?php if ($this->_tpl_vars['k'] == $this->_tpl_vars['db_driver']): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['driver']; ?>

	<?php endforeach; endif; unset($_from); ?>
</select><br>
<br>

<b>Database Server:</b><br>
<input type="text" name="db_server" value="<?php echo $this->_tpl_vars['db_server']; ?>
"><br>
<br>

<b>Database Name:</b><br>
<input type="text" name="db_name" value="<?php echo $this->_tpl_vars['db_name']; ?>
"><br>
<br>

<b>Database User:</b><br>
<input type="text" name="db_user" value="<?php echo $this->_tpl_vars['db_user']; ?>
"><br>
<br>

<b>Database Password:</b><br>
<input type="text" name="db_pass" value="<?php echo $this->_tpl_vars['db_pass']; ?>
"><br>
<br>

<input type="submit" value="Test Settings &gt;&gt;">
</form>