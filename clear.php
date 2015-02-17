<?php
	include('./Config/config.php');
	file_put_contents(DATA_TW_LAST_ID,   "");
	file_put_contents(DATA_IN_LAST_ID,   "");
	file_put_contents(DATA_UNIDENTIFIED, "");
	file_put_contents(LOG_TW_REQUESTS,   "");
	file_put_contents(LOG_IN_REQUESTS,   "");
	file_put_contents(LOG_ERRORS,        "");
	file_put_contents(DATA_ROW,          "");
?>