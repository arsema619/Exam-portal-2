<?php
session_start();

/* exam duration (example: 30 minutes) */
$exam_duration = 30 * 60;

/* start exam timer if not started */
if (!isset($_SESSION["exam_start"])) 

/* calculate remaining time */
$start_time = $_SESSION["exam_start"];
$remaining_time = ($start_time + $exam_duration) - time();

/* if time finished */
if ($remaining_time <= 0) 

?>
