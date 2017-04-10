<?
  $html->resource(array(
		'/_modules/admin/js/elrte-1.2/js/elrte.full.js',
		'/_modules/admin/js/elrte-1.2/js/i18n/elrte.ru.js',
		'/_modules/admin/js/elrte-1.2/js/codemirror/js/codemirror.js',
		'/_modules/admin/js/elrte-1.2/js/elrte.codehightlight.js',
		'/_modules/admin/js/elfinder-1.1/js/elfinder.min.js',
		'/_modules/admin/js/elfinder-1.1/js/i18n/elfinder.ru.js',
		'/_modules/admin/js/elfinder-1.1/css/elfinder.css',
		'/_modules/admin/js/elrte-1.2/css/elrte.min.css',
		'/_modules/admin/js/elrte-1.2/css/smoothness/jquery-ui-1.8.7.custom.css'
  )); 
?>

<script type="text/javascript" charset="utf-8">
	$().ready(function() {

		$('#elRTE a').hover(
			function () {
				$('#elRTE a').animate({
					'background-position' : '0 -45px'
				}, 300);
			},
			function () {
				$('#elRTE a').delay(400).animate({
					'background-position' : '0 0'
				}, 300);
			}
		);

		$('#elRTE a').delay(800).animate({'background-position' : '0 0'}, 300);

		var f = $('#finder').elfinder({
			url : '/_atlant/js/elfinder-1.1/connectors/php/connector.php',
			lang : 'en',
			docked : true,
			height: 490
		})
	})
</script>

<div id="finder">finder</div>