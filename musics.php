<?php
	//error_reporting(E_ERROR | E_PARSE );
	include("TocaEssa.php");
	$tocaEssa = new TocaEssa();
	echo $tocaEssa->getMusics();
?>