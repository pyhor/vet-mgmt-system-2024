<?php
// Remove HTTP request timeout
set_time_limit(0);



include "connect_to_db.php";



// Get today's chat that this nurse has been assigned to
$query = "SELECT * FROM chat WHERE nurseid = {$_POST["id"]} AND chatsentdate = CURRENT_DATE()"
  . "GROUP BY topic ORDER BY chatid";

while (true) {
  $run = mysqli_query($connect, $query);

  if ($run != true) {
    http_response_code(400);
    echo "Query failed";
    return;
  }

  $results = mysqli_fetch_all($run, MYSQLI_ASSOC);

  // Filter out already received chats
  $currentNumOfChats = (int) $_POST["currentNumOfChats"];

  if ($currentNumOfChats > 0) {
    $results = array_slice($results, $currentNumOfChats);
  }

  // Long-polling: Only return if there are new chats
  if (count($results) > 0) {
    break;
  }

  sleep(1);
}



// Formats query result to the structure livechat client js uses
$chats = array();

foreach ($results as $result) {
  $chat = new stdClass();
  $chat->topic = $result["topic"];
  $chat->petOwnerId = $result["petownerid"];
  $chat->startingChatId = ((int) $result["chatid"]) - 1;
  $chat->chatHistory = array();

  $chats[] = $chat;
}

echo json_encode($chats);
?>