
	<meta name="viewport" content="width=505" />

	<title><?= TITLE ?></title>

	<?php if (!BASE_URL_REWRITE): ?>
		<base href="<?php e(BASE_URL) ?>"/>
	<?php endif; ?>

	<link rel="stylesheet" href="<?php e(BASE_URL) ?>static/style/basis.css" type="text/css">
	<link rel="stylesheet" href="<?php e(BASE_URL) ?>static/style/style.css" type="text/css">
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.pack.js"></script>
	<script type="text/javascript" src="<?php e(BASE_URL) ?>static/js/jquery-core.js"></script>
	<style>

.flip-h {
    -moz-transform: scaleX(-1);
    -o-transform: scaleX(-1);
    -webkit-transform: scaleX(-1);
    transform: scaleX(-1);
    filter: FlipH;
    -ms-filter: "FlipH";
}

</style>

