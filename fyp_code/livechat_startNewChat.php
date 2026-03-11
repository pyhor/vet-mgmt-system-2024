<?php
include "connect_to_db.php";



// Validate topic
// petowners cannot have duplicate topics on the same day
$topic_query = "SELECT DISTINCT topic FROM chat WHERE "
  . "petownerid = {$_POST["id"]} AND chatsentdate = CURRENT_DATE()";

$topic_run = mysqli_query($connect, $topic_query);

if ($topic_run != true) {
  http_response_code(400);
  echo "Topic query failed";
  return;
}

$topics = array_map(fn($x) => $x[0], mysqli_fetch_all($topic_run));

if (in_array($_POST["topic"], $topics)) {
  http_response_code(400);
  echo "Duplicate topic";
  return;
}



// Check for nurses that are online
$nursesOnline = json_decode(file_get_contents("livechat_nursesOnline.json"));

if (count($nursesOnline) == 0) {
  http_response_code(400);
  echo "Nurses are currently unavailable, please try again later";
  return;
}

// Find nurses with fewest number of chats
$leastNumOfChats = $nursesOnline[0]->numOfChats;
$nurses_leastNumOfChats_index = [];

for ($i = 0; $i < count($nursesOnline); $i++) {
  $nurse = $nursesOnline[$i];

  if ($nurse->numOfChats == $leastNumOfChats) {
    $nurses_leastNumOfChats_index[] = $i;
  }
  else if ($nurse->numOfChats < $leastNumOfChats) {
    $leastNumOfChats = $nurse->numOfChats;
    $nurses_leastNumOfChats_index = [$i];
  }
}

$chosenNurse_index = $nurses_leastNumOfChats_index[array_rand($nurses_leastNumOfChats_index)];
$chosenNurse_id = $nursesOnline[$chosenNurse_index]->id;



$query = "INSERT INTO "
  . "chat(chatsentdate, chatsenttime, chatsentcontent, "
  . "topic, petownerid, "
  . "nurseid, role) "
  . "VALUES (CURRENT_DATE(), CURRENT_TIME(), \"{$_POST["description"]}\", "
  . "\"{$_POST["topic"]}\", {$_POST["id"]}, "
  . "{$chosenNurse_id}, \"petowner\")";

$run = mysqli_query($connect, $query);

if ($run != true) {
  http_response_code(400);
  echo "Query failed";
}



// Get the first message of the newly created chat and format the query result to the structure
// livechat client js uses
$chatHistory_query = "SELECT chatid, chatsentdate, chatsenttime, chatreceivedate, chatreceivetime, "
  . "chatsentcontent, role FROM chat WHERE chatid = " . mysqli_insert_id($connect);

$chatHistory_run = mysqli_query($connect, $chatHistory_query);

$chat = new stdClass();
$chat->topic = $_POST["topic"];
$chat->nurseId = $chosenNurse_id;
$chat->chatHistory = mysqli_fetch_all($chatHistory_run, MYSQLI_ASSOC);

echo json_encode($chat);



// Increase chosen nurse's counter
$nursesOnline[$chosenNurse_index]->numOfChats++;
file_put_contents("livechat_nursesOnline.json", json_encode($nursesOnline));
?>