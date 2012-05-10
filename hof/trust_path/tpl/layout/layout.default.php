<!DOCTYPE html>
<html>
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<?php e($this->slot('layout/layout.header')) ?>

</head>
<body>
	<a name="top"></a>

	<?php e($this->content) ?>

	<?php e($this->slot('layout/layout.footer')) ?>
</body>
</html>