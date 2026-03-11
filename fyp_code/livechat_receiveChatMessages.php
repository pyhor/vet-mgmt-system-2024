<?php
// Remove HTTP request timeout
set_time_limit(0);



include "connect_to_db.php";



while (true) {
  // Get new messages from this specific chat

  // Topic can be duplicate across different days, so latest_chatid is used to ensure it's from
  // today's chats
  $query = "SELECT chatid, chatsentdate, chatsenttime, chatreceivedate, chatreceivetime, "
    . "chatsentcontent, role FROM chat WHERE "
    . "petownerid = {$_POST["petownerid"]} AND nurseid = {$_POST["nurseid"]} AND "
    . "topic = \"{$_POST["topic"]}\" AND chatid > {$_POST["latest_chatid"]}";

  $run = mysqli_query($connect, $query);

  if ($run == false) {
    http_response_code(400);
    echo "Query failed";
    return;
  }

  $newMessages = mysqli_fetch_all($run, MYSQLI_ASSOC);

  // Long-polling: Only return if there are new messages
  if (count($newMessages) > 0) {
    echo json_encode($newMessages);
    return;
  }

  sleep(1);
}
?>