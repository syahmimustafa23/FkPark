<?php
// auth.php
session_start();
require_once "db.php";

function require_login(): array {
  if (empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
  }

  global $conn;
  $uid = (int)$_SESSION["user_id"];

  $st = $conn->prepare("SELECT user_id, username, user_type, full_name FROM users WHERE user_id = ? LIMIT 1");
  $st->bind_param("i", $uid);
  $st->execute();
  $u = $st->get_result()->fetch_assoc();

  if (!$u) {
    session_destroy();
    header("Location: login.php");
    exit;
  }
  return $u;
}

function require_role(string $role): array {
  $u = require_login();
  if ($u["user_type"] !== $role) {
    http_response_code(403);
    echo "403 Forbidden";
    exit;
  }
  return $u;
}

function require_any_role(array $roles): array {
  $u = require_login();
  if (!in_array($u["user_type"], $roles, true)) {
    http_response_code(403);
    echo "403 Forbidden";
    exit;
  }
  return $u;
}
