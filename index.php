<?php
	include('./Config/config.php');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>TocaEssa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="Content/css/resets.css">
	<link rel="stylesheet" href="Content/css/style.css">
	</head>
	<body>
		<div class="content">
			<h1>Toca Essa</h1>
			<div id="profile"></div>
			<div id="img-in"></div>
			<div id="loading" class="big">Carregando...</div>
			<div id="social" class="big"></div>
			<div id="who" class="big"></div>
			<div id="now" class="big"></div>
			<div id="player"></div>
			<div id="controls">
				<div id="box-controls">
					<div id="play" class="click">Tocar</div>
					<div id="stop" class="click">Parar</div>
					<div id="back" class="click">Anterior</div>
					<div id="next" class="click">Próxima</div>
					<div id="next-list" class="click">Próxima da Lista de Pedidos</div>
					<div id="up-vol" class="click">Aumentar Volume</div>
					<div id="down-vol" class="click">Diminuir Volume</div>
					<div id="mute" class="click">Mudo</div>
					<div id="volume" class="click"></div>
				</div>
				<span class="progress-label">Duração:</span>
				<div id="music-duration"></div>
				<span class="progress-label">Tempo Decorrido:</span>
				<div id="music-elapsed"></div>
				<div id="box-progress">
					<progress value="0%" max="100" id="progress">0%</progress> 
				</div>
			</div>
		</div>
	</body>
	<script type="text/javascript">var time_reload_tw = <?php echo TIME_RELOAD_TW ?>;</script>
	<script type="text/javascript">var time_reload_in = <?php echo TIME_RELOAD_IN ?>;</script> 
	<script type="text/javascript" src="Content/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="Content/js/objects/oracle.js"></script>
	<script type="text/javascript" src="Content/js/jwplayer/jwplayer.js"></script>
</html>