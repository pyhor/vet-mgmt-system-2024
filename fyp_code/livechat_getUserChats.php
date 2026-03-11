<?php
include "connect_to_db.php";



// Get today's chats started from this petowner
$query = "SELECT * FROM chat WHERE petownerid = {$_POST["id"]} AND chatsentdate = CURRENT_DATE() "
  . "GROUP BY topic ORDER BY chatid";

$run = mysqli_query($connect, $query);

if ($run != true) {
  http_response_code(400);
  echo "Query failed";
  return;
}

$results = mysqli_fetch_all($run, MYSQLI_ASSOC);



// Formats query result to the structure livechat client js uses
$chats = array();

foreach ($results as $result) {
  $chatHistory_query = "SELECT chatid, chatsentdate, chatsenttime, chatreceivedate, "
    . "chatreceivetime, chatsentcontent, role FROM chat WHERE chatid = {$result["chatid"]}";

  $chatHistory_run = mysqli_query($connect, $chatHistory_query);

  $chat = new stdClass();
  $chat->topic = $result["topic"];
  $chat->nurseId = $result["nurseid"];
  $chat->chatHistory = mysqli_fetch_all($chatHistory_run, MYSQLI_ASSOC);

  $chats[] = $chat;
}

echo json_encode($chats);
?>