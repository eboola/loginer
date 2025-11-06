<?php
session_start();
if (!isset($_SESSION["username"]) && $_SESSION['loggedin'] === true) {
    echo("<h3>Welcome," .  htmlspecialchars($_SESSION['username']) .  "!</h3>");
}