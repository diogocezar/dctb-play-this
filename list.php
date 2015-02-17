<?php
	include("TocaEssa.php");
	$tocaEssa = new TocaEssa();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>TocaEssa - Lista de Músicas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="Content/css/resets.css">
	<link rel="stylesheet" href="Content/css/style.css">
	</head>
	<body>
		<div class="content">
			<h1>Toca Essa</h1>
			<h2>Exibindo as músicas disponíveis:</h2>
			<div id="list-musics">
				<?php echo $tocaEssa->listMusics(); ?>
			</div>
			<div id="box-insta">
				<h1>Copie e cole a seguinte descrição no seu Instagram</h1>
				<div id="generated"></div>
				<div id="options">
					<span id="close">Fechar</span>
				</div>
			</div>
		</div>
		<script type="text/javascript">var in_hashtag = '<?php echo IN_HASHTAG ?>';</script>
		<script type="text/javascript" src="Content/js/jquery/jquery.js"></script>
		<script type="text/javascript" src="Content/js/objects/list.js"></script>
	</body>
</html>