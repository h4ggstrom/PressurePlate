<?php
  $hashedPassword = password_hash("basededonnées", PASSWORD_DEFAULT);
  echo $hashedPassword;
?>