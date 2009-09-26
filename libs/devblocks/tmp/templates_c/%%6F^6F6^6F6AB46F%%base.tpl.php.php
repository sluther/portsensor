<?php /* Smarty version 2.6.14, created on 2009-09-26 01:39:06
         compiled from base.tpl.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'math', 'base.tpl.php', 17, false),)), $this); ?>
<html>
<head>
<title>PortSensor Installer</title>
<link rel="stylesheet" href="install.css" type="text/css">
</head>

<body>
<H1>Installing PortSensor Server 3.0</H1>
<table cellpadding="2" cellspacing="2">
	<tr>
		<td>Progress: </td>
		<?php unset($this->_sections['progress']);
$this->_sections['progress']['start'] = (int)0;
$this->_sections['progress']['loop'] = is_array($_loop=@TOTAL_STEPS) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['progress']['name'] = 'progress';
$this->_sections['progress']['show'] = true;
$this->_sections['progress']['max'] = $this->_sections['progress']['loop'];
$this->_sections['progress']['step'] = 1;
if ($this->_sections['progress']['start'] < 0)
    $this->_sections['progress']['start'] = max($this->_sections['progress']['step'] > 0 ? 0 : -1, $this->_sections['progress']['loop'] + $this->_sections['progress']['start']);
else
    $this->_sections['progress']['start'] = min($this->_sections['progress']['start'], $this->_sections['progress']['step'] > 0 ? $this->_sections['progress']['loop'] : $this->_sections['progress']['loop']-1);
if ($this->_sections['progress']['show']) {
    $this->_sections['progress']['total'] = min(ceil(($this->_sections['progress']['step'] > 0 ? $this->_sections['progress']['loop'] - $this->_sections['progress']['start'] : $this->_sections['progress']['start']+1)/abs($this->_sections['progress']['step'])), $this->_sections['progress']['max']);
    if ($this->_sections['progress']['total'] == 0)
        $this->_sections['progress']['show'] = false;
} else
    $this->_sections['progress']['total'] = 0;
if ($this->_sections['progress']['show']):

            for ($this->_sections['progress']['index'] = $this->_sections['progress']['start'], $this->_sections['progress']['iteration'] = 1;
                 $this->_sections['progress']['iteration'] <= $this->_sections['progress']['total'];
                 $this->_sections['progress']['index'] += $this->_sections['progress']['step'], $this->_sections['progress']['iteration']++):
$this->_sections['progress']['rownum'] = $this->_sections['progress']['iteration'];
$this->_sections['progress']['index_prev'] = $this->_sections['progress']['index'] - $this->_sections['progress']['step'];
$this->_sections['progress']['index_next'] = $this->_sections['progress']['index'] + $this->_sections['progress']['step'];
$this->_sections['progress']['first']      = ($this->_sections['progress']['iteration'] == 1);
$this->_sections['progress']['last']       = ($this->_sections['progress']['iteration'] == $this->_sections['progress']['total']);
?>
		<td <?php if ($this->_sections['progress']['iteration'] <= $this->_tpl_vars['step']): ?>class='progress_complete'<?php else: ?>class='progress_incomplete'<?php endif; ?>>
			&nbsp;
		</td>
		<?php endfor; endif; ?>
		<td>(<?php echo smarty_function_math(array('equation' => "(x/y)*100",'x' => $this->_tpl_vars['step'],'y' => @TOTAL_STEPS,'format' => "%d"), $this);?>
%)</td>
	</tr>
</table>

<?php if (! empty ( $this->_tpl_vars['template'] )):  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['template'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  endif; ?>
</body>

</html>