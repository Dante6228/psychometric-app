<?php

$password = "contraseña123";

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Hash: $hash\n";
