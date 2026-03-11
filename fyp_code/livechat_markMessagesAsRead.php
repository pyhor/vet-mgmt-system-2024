<?php
include "connect_to_db.php";



$query = "UPDATE chat SET chatreceivedate = CURRENT_DATE(), chatreceivetime = CURRENT_TIME() "
  . "WHERE chatid in ({$_POST["chatids"]})";

$run = mysqli_query($connect, $query);

if ($run != true) {
  http_response_code(400);
  echo "Query failed";
  return;
}
?>