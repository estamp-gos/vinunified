<?php
// /patchwebsite/customerpanel/logout.php

session_start();
session_destroy();
header('Location: ' . SITE_URL . 'login.php');
exit;