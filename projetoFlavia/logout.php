<?php
session_start();
session_destroy();

/* limpa cache também */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

/* volta pro login */
header("Location: login.php");
exit;
?>