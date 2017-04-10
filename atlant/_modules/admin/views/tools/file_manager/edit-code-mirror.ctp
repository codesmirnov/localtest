<?
  $html->resource(array(
  	'/_modules/admin/js/code-mirror-2.0/lib/codemirror.js',
  	'/_modules/admin/js/code-mirror-2.0/mode/clike/clike.js',
  	'/_modules/admin/js/code-mirror-2.0/mode/javascript/javascript.js',
  	'/_modules/admin/js/code-mirror-2.0/mode/xml/xml.js',
  	'/_modules/admin/js/code-mirror-2.0/mode/css/css.js',
  	'/_modules/admin/js/code-mirror-2.0/mode/htmlmixed/htmlmixed.js',
  	'/_modules/admin/js/code-mirror-2.0/mode/php/php.js',
  	'/_modules/admin/js/code-mirror-2.0/mode/css/css.css',
  	'/_modules/admin/js/code-mirror-2.0/mode/xml/xml.css',
  	'/_modules/admin/js/code-mirror-2.0/mode/javascript/javascript.css',
  	'/_modules/admin/js/code-mirror-2.0/mode/clike/clike.css',
  	'/_modules/admin/js/code-mirror-2.0/lib/codemirror.css'
  )); 
?>

<form method="post">
	<fieldset>
		<h3><?=$filename;?></h3>
		<div class="reducer">
			<div class="input submit">
				<?=$form->submit('Сохранить', array('div' => false));?>
				<? if ($fileperm < 777): ?>
					<p class="error">
						Сохраниния файла невозможно. Измените права доступа. <?=$fileperm;?>
					</p>
				<? endif; ?>
			</div>
		</div>
	</fieldset>

	<textarea id="code" name="data[content]"><?=htmlspecialchars($file_content);?></textarea>

	<script>
	  var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
	    lineNumbers: true,
	    matchBrackets: true,
	    mode: "<?=$mode;?>",
	    indentUnit: 8,
	    indentWithTabs: true,
	    enterMode: "keep",
	    tabMode: "shift"
	  });
	</script>
</form>