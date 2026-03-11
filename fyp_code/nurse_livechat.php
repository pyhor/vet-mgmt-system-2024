<?php
session_start();

if (!isset($_SESSION['nurseusername'])) {
  header('Location: nurse_login.php'); // Redirect if not logged in
  exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/layoutTemplate.css">
  <link rel="stylesheet" href="css/nurse_livechat_layout.css">
  <link rel="icon" type="image/x-icon" href="images/download__4_-modified__1_-removebg-preview.png">
  <title>Nurse Live Chat | VCMS </title>
</head>
<body>
  <nav>
    <?php include 'nurse_nav.php'; ?>
  </nav>

  <div class="main-content">
    <div id="liveChat">
      <div id="liveChat_chatList"></div>

      <div id="liveChat_chatBox">
        <div id="liveChat_chatHistory"></div>

        <div id="liveChat_messageInput">
          <div id="messageInput_response"></div>

          <form id="messageInput_form" method="POST">
            <input type="text" name="message" placeholder="Message" required>
            <button type="submit">Send</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <?php include 'footer.php'; ?>
  </footer>



  <script>
    let liveChat_div = document.querySelector("div#liveChat")
    let chatList = liveChat_div.querySelector("div#liveChat_chatList")
    let chatHistory = liveChat_div.querySelector("div#liveChat_chatHistory")
    let messageInput = liveChat_div.querySelector("div#liveChat_messageInput")

    let chats = sessionStorage.getItem("chats")
    let currentChat_index = 0

    let chatHistory_ongoingRequest

    if (chats === null) {
      chats = []
    }
    else {
      chats = JSON.parse(chats)

      updateChatList()
      updateLiveChatUI(currentChat_index)
    }



    // Start long-polling to get new chats from database
    let newChats_formData = new FormData()
    newChats_formData.set("id", <?php echo $_SESSION["nurseid"]; ?>)
    newChats_formData.set("currentNumOfChats", chats.length)

    let newChats_request = new XMLHttpRequest()

    newChats_request.addEventListener("readystatechange", () => {
      if (newChats_request.readyState !== 4) {
        return
      }

      if (newChats_request.status === 400) {
        chatList.innerHTML += "Error, check console log"
        console.error("Failed to get new chats. Reason: " + newChats_request.responseText)
      }
      else if (newChats_request.status === 200) {
        let chatList_wasEmpty = false

        if (chats.length === 0) {
          chatList_wasEmpty = true
        }

        chats.push(...(JSON.parse(newChats_request.responseText)))
        sessionStorage.setItem("chats", JSON.stringify(chats))

        updateChatList()

        if (chatList_wasEmpty) {
          updateLiveChatUI(0)
          chatList_wasEmpty = false
        }

        // Request again
        newChats_formData.set("currentNumOfChats", chats.length)

        newChats_request.open("POST", "livechat_receiveNewChats.php")
        newChats_request.send(newChats_formData)
      }
    })

    newChats_request.open("POST", "livechat_receiveNewChats.php")
    newChats_request.send(newChats_formData)



    // Updates chat list at the left side of livechat UI
    function updateChatList() {
      chatList.replaceChildren()

      for (let chat of chats) {
        let chat_div = document.createElement("div")
        chat_div.innerHTML = chat.topic
        chat_div.addEventListener("click", () => {
          updateLiveChatUI(Array.from(chatList.children).indexOf(chat_div))
        })

        chatList.append(chat_div)
      }
    }



    // Updates the right side of livechat UI when selecting a different chat
    function updateLiveChatUI(selectedChat_index) {
      // Highlight selected chat in chatList
      chatList.children[currentChat_index].classList.remove("selected")
      chatList.children[selectedChat_index].classList.add("selected")

      currentChat_index = selectedChat_index

      getChatMessages(selectedChat_index)
    }



    // Display current chat's chat history and starts long-polling for new messages
    function getChatMessages(selectedChat_index) {
      let selectedChat = chats[selectedChat_index]

      // Display already received messages
      updateChatHistory()

      // Check for new messages
      // Abort current long-polling request
      if (chatHistory_ongoingRequest !== undefined) {
        chatHistory_ongoingRequest.abort()
      }

      let formData = new FormData()
      formData.set("nurseid", <?php echo $_SESSION["nurseid"]; ?>)
      formData.set("petownerid", selectedChat.petOwnerId)
      formData.set("topic", selectedChat.topic)

      if (selectedChat.chatHistory.length !== 0) {
        formData.set("latest_chatid", selectedChat.chatHistory.at(-1).chatid)
      }
      else {
        formData.set("latest_chatid", selectedChat.startingChatId)
      }

      let request = new XMLHttpRequest()

      request.addEventListener("readystatechange", () => {
        if (request.readyState !== 4) {
          return
        }

        if (request.status === 400) {
          chatHistory.innerHTML = "Failed to receive messages. Reason: " + request.responseText
        }
        else if (request.status === 200) {
          chats[selectedChat_index].chatHistory.push(...(JSON.parse(request.responseText)))
          sessionStorage.setItem("chats", JSON.stringify(chats))

          updateChatHistory()

          // Request again
          if (currentChat_index === selectedChat_index) {
            formData.set("latest_chatid", chats[selectedChat_index].chatHistory.at(-1).chatid)

            request.open("POST", "livechat_receiveChatMessages.php")
            request.send(formData)
          }
        }
      })

      request.open("POST", "livechat_receiveChatMessages.php")
      request.send(formData)

      chatHistory_ongoingRequest = request
    }



    // Displays each message as individual text bubbles
    // While displaying the messages, messages from the petowner will be marked as read
    function updateChatHistory() {
      let chatMessages = chats[currentChat_index].chatHistory
      let chatIds_toMarkAsRead = []
      let firstMessage = true

      chatHistory.replaceChildren()

      for (let message of chatMessages) {
        let template_content = "<div class=\""

        if (message.role === "petowner") {
          template_content += "petowner"
        }
        else {
          template_content += "nurse"
        }

        template_content += "\"><div>"

        if (firstMessage) {
          template_content += `<div class="topic">${chats[currentChat_index].topic}</div>`
          firstMessage = false
        }

        template_content += `<div class="message">${message.chatsentcontent}</div></div>`

        let datetime = new Date(`${message.chatsentdate} ${message.chatsenttime}`)
        template_content += `<div>${String(datetime.getHours()).padStart(2, "0")}:`
          + `${String(datetime.getMinutes()).padStart(2, "0")} `
          + `${datetime.getDate()}-${datetime.getMonth() + 1}-${datetime.getFullYear()}</div>`

        template_content += "</div>"

        let template = document.createElement("template")
        template.innerHTML = template_content

        chatHistory.append(template.content)

        // Mark as read
        if (message.role === "petowner" && message.chatreceivedate == null) {
          chatIds_toMarkAsRead.push(message.chatid)
        }
      }

      // Auto-scroll chatHistory
      chatHistory.scrollTop = chatHistory.scrollHeight

      // Mark as read
      if (chatIds_toMarkAsRead.length === 0) {
        return
      }

      let formData = new FormData()
      formData.set("chatids", chatIds_toMarkAsRead.map((x) => Number(x)))

      let request = new XMLHttpRequest()

      request.addEventListener("readystatechange", () => {
        if (request.readyState !== 4) {
          return
        }

        if (request.status === 400) {
          console.log("Mark as read reqeust failed. Reason: " + request.responseText)
        }
      })

      request.open("POST", "livechat_markMessagesAsRead.php")
      request.send(formData)
    }


    // Input form for sending messages to currently selected chat
    // ===
    let messageInput_form = liveChat.querySelector("form#messageInput_form")
    let messageInput_response = liveChat.querySelector("div#messageInput_response")

    messageInput_form.addEventListener("submit", (event) => {
      let chat = chats[currentChat_index]
      let formData = new FormData(messageInput_form)

      formData.set("topic", chat.topic)
      formData.set("petownerid", chat.petOwnerId)
      formData.set("nurseid", <?php echo $_SESSION["nurseid"]; ?>)
      formData.set("role", "nurse")

      let request = new XMLHttpRequest()

      messageInput_response.style.display = "none"

      request.addEventListener("readystatechange", () => {
        if (request.readyState !== 4) {
          return
        }

        if (request.status === 400) {
          messageInput_response.innerHTML = "failed to send message. Reason: "
            + request.responseText

          messageInput_response.style.display = "revert"
        }
        else if (request.status === 200) {
          messageInput_form.reset()
        }
      })

      request.open("POST", "livechat_sendChatMessage.php")
      request.send(formData)

      event.preventDefault()
    })
  </script>
</body>
</html>

<style>
  footer {
    font-family: Itim, cursive;
    font-weight: 200;
    background-color: #5b6e54;
    color: #ffffff;
    text-align: center;
    padding: 10px 0;
    width: 100%; /* Changed from 32cm to 100% */
    /* position: fixed; /* Added to stick to bottom */
    bottom: 0; /* Added to position at bottom */
    left: 0; /* Added to ensure proper alignment */
  }

  footer p {
    margin: 0;
    font-size: 14px;
  }
</style>