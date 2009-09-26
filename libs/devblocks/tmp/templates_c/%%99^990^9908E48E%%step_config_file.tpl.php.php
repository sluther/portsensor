<?php /* Smarty version 2.6.14, created on 2009-09-26 01:40:27
         compiled from steps/step_config_file.tpl.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'steps/step_config_file.tpl.php', 21, false),)), $this); ?>
<h2>Save framework.config.php</h2>

<form action="index.php" method="POST">
<input type="hidden" name="step" value="<?php echo @STEP_SAVE_CONFIG_FILE; ?>
">
<input type="hidden" name="overwrite" value="1">
<input type="hidden" name="db_driver" value="<?php echo $this->_tpl_vars['db_driver']; ?>
">
<input type="hidden" name="db_server" value="<?php echo $this->_tpl_vars['db_server']; ?>
">
<input type="hidden" name="db_name" value="<?php echo $this->_tpl_vars['db_name']; ?>
">
<input type="hidden" name="db_user" value="<?php echo $this->_tpl_vars['db_user']; ?>
">
<input type="hidden" name="db_pass" value="<?php echo $this->_tpl_vars['db_pass']; ?>
">

<?php if ($this->_tpl_vars['failed']): ?>
<span class='bad'>The framework.config.php file does not appear to have updated settings.  Please try again.</span><br>
<br>
<?php endif; ?>

Since your environment did not support the writing of your <b>framework.config.php</b> file automatically, 
you'll need to overwrite the existing contents of the file with the following:<br>
<br>
<i><?php echo $this->_tpl_vars['config_path']; ?>
</i>:<br>
<textarea cols="80" rows="10" name="result"><?php echo ((is_array($_tmp=$this->_tpl_vars['result'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'htmlall') : smarty_modifier_escape($_tmp, 'htmlall')); ?>
</textarea><br>
<br>
<input type="submit" value="Test My Changes&gt;&gt;">
</form>