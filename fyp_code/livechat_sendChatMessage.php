<?php
include "connect_to_db.php";



$query = "INSERT INTO "
  . "chat(chatsentdate, chatsenttime, chatsentcontent, "
  . "topic, petownerid, "
  . "nurseid, role) "
  . "VALUES (CURRENT_DATE(), CURRENT_TIME(), \"{$_POST["message"]}\", "
  . "\"{$_POST["topic"]}\", {$_POST["petownerid"]}, "
  . "{$_POST["nurseid"]}, \"{$_POST["role"]}\")";

$run = mysqli_query($connect, $query);

if ($run != true) {
  http_response_code(400);
  echo "Query failed";
}
?>