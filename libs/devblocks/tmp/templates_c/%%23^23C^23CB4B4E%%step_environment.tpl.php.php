<?php /* Smarty version 2.6.14, created on 2009-09-26 01:39:06
         compiled from steps/step_environment.tpl.php */ ?>
<h2>Checking Server Environment</h2>

<form action="index.php" method="POST">
<b>PHP Version... </b> 
<?php if (! $this->_tpl_vars['results']['php_version']): ?>
	<span class="bad"><?php echo $this->_tpl_vars['translate']->_('installer.failed'); ?>
!  PHP 5.0.0 or later is required.</span>
<?php else: ?>
	<span class="good"><?php echo $this->_tpl_vars['translate']->_('installer.passed'); ?>
! (PHP <?php echo $this->_tpl_vars['results']['php_version']; ?>
)</span>
<?php endif; ?>
<br>
<br>

<b>PHP Extension (Session)... </b> 
<?php if (! $this->_tpl_vars['results']['ext_session']): ?>
	<span class="bad">Error! PHP must have the 'Sessions' extension enabled.</span>
<?php else: ?>
	<span class="good">Passed!</span>
<?php endif; ?>
<br>
<br>

<b>PHP Extension (mbstring)... </b> 
<?php if (! $this->_tpl_vars['results']['ext_mbstring']): ?>
	<span class="bad">Error! PHP must have the 'mbstring' extension enabled.</span>
<?php else: ?>
	<span class="good">Passed!</span>
<?php endif; ?>
<br>
<br>

<b>PHP Extension (SimpleXML)... </b> 
<?php if (! $this->_tpl_vars['results']['ext_simplexml']): ?>
	<span class="bad">Error! PHP must have the 'SimpleXML' extension enabled.</span>
<?php else: ?>
	<span class="good">Passed!</span>
<?php endif; ?>
<br>
<br>



<b>PHP.INI Memory_Limit... </b> 
<?php if (! $this->_tpl_vars['results']['memory_limit']): ?>
	<span class="bad">Failure! memory_limit should be 8M or higher in your php.ini file.</span>
<?php else: ?>
	<span class="good">Passed!</span>
<?php endif; ?>
<br>
<br>

<?php if (! $this->_tpl_vars['fails']): ?>
	<input type="hidden" name="step" value="<?php echo @STEP_DATABASE; ?>
">
	<input type="submit" value="Next Step &gt;&gt;">
<?php else: ?>
	<input type="hidden" name="step" value="<?php echo @STEP_ENVIRONMENT; ?>
">
	<input type="submit" value="Try Again">
<?php endif; ?>
</form>