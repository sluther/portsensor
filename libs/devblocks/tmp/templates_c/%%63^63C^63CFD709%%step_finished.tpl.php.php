<?php /* Smarty version 2.6.14, created on 2009-09-26 01:40:58
         compiled from steps/step_finished.tpl.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'devblocks_url', 'steps/step_finished.tpl.php', 12, false),)), $this); ?>
<h2>Congratulations!  Setup Complete.</h2>

<form action="index.php" method="POST">
<input type="hidden" name="step" value="<?php echo @STEP_FINISHED; ?>
">
<input type="hidden" name="form_submit" value="1">

<H3>PortSensor Server is ready for business!</H3>
<b>Login:</b> admin<br>
<b>Password:</b> superuser<br>
<br>

<a href="<?php $this->_tag_stack[] = array('devblocks_url', array()); $_block_repeat=true;smarty_block_devblocks_url($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_devblocks_url($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>">Log in!</a><br>
<br>

</form>