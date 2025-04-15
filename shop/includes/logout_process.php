<?php

session_start();


$_SESSION = array();


session_destroy();


header('Location: ../index');
exit;
?>

<script src="js/url-cleaner.js"></script>