<h2>Checking Server Environment</h2>

<form action="index.php" method="POST">
<b>PHP Version... </b> 
{if !$results.php_version}
	<span class="bad">{$translate->_('installer.failed')}!  PHP 5.0.0 or later is required.</span>
{else}
	<span class="good">{$translate->_('installer.passed')}! (PHP {$results.php_version})</span>
{/if}
<br>
<br>

<b>PHP Extension (Session)... </b> 
{if !$results.ext_session}
	<span class="bad">Error! PHP must have the 'Sessions' extension enabled.</span>
{else}
	<span class="good">Passed!</span>
{/if}
<br>
<br>

<b>PHP Extension (mbstring)... </b> 
{if !$results.ext_mbstring}
	<span class="bad">Error! PHP must have the 'mbstring' extension enabled.</span>
{else}
	<span class="good">Passed!</span>
{/if}
<br>
<br>

<b>PHP Extension (SimpleXML)... </b> 
{if !$results.ext_simplexml}
	<span class="bad">Error! PHP must have the 'SimpleXML' extension enabled.</span>
{else}
	<span class="good">Passed!</span>
{/if}
<br>
<br>

{*
<b>PHP.INI File_Uploads... </b> 
{if !$results.file_uploads}
	<span class="bad">Failure!  file_uploads must be enabled in your php.ini file.</span>
{else}
	<span class="good">Passed!</span>
{/if}
<br>
<br>
*}

{*
<b>PHP.INI Upload_Tmp_Dir... </b> 
{if !$results.upload_tmp_dir}
	<span class="warning">Warning! upload_tmp_dir should be set in your php.ini file.</span>
{else}
	<span class="good">Passed!</span>
{/if}
<br>
<br>
*}

<b>PHP.INI Memory_Limit... </b> 
{if !$results.memory_limit}
	<span class="bad">Failure! memory_limit should be 8M or higher in your php.ini file.</span>
{else}
	<span class="good">Passed!</span>
{/if}
<br>
<br>

{if !$fails}
	<input type="hidden" name="step" value="{$smarty.const.STEP_DATABASE}">
	<input type="submit" value="Next Step &gt;&gt;">
{else}
	<input type="hidden" name="step" value="{$smarty.const.STEP_ENVIRONMENT}">
	<input type="submit" value="Try Again">
{/if}
</form>