<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent private pages from being cached by the browser.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

function ensure_logged_in()
{
    if (!isset($_SESSION['username']) || !isset($_SESSION['usertype'])) {
        header('Location: login.php');
        exit();
    }
}

function ensure_farmer()
{
    ensure_logged_in();
    if ($_SESSION['usertype'] !== 'farmer') {
        header('Location: login.php');
        exit();
    }
}
