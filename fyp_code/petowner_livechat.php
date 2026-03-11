

<button id="liveChat_button">
  <img src="images/livechat icon.png">
</button>

<div id="liveChat_menu">
  <!-- Window top bar -->
  <div>
    <div>Live Chat</div>
    <button id="liveChat_menu_closeButton">
      <img src="images/close button.png">
    </button>
  </div>

  <div>
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

      <div id="liveChat_newChatInput">
        <div>New chat</div>

        <form id="newChatInput_form" method="POST">
          <label>
            <div>Topic</div>
            <input type="text" name="topic" maxlength="100" required>
          </label>
          <label>
            <div>Description</div>
            <input type="text" name="description" required>
          </label>
          <input type="hidden" name="id" value="<?php echo $_SESSION["petownerid"]; ?>">
          <div><button type="submit">Start</button></div>
        </form>

        <div id="newChatInput_response"></div>
      </div>
    </div>
  </div>
</div>



<script>
  let liveChat_menu = document.querySelector("div#liveChat_menu")
  let liveChat_button = document.querySelector("button#liveChat_button")
  let chatList = liveChat_menu.querySelector("div#liveChat_chatList")
  let chatHistory = liveChat_menu.querySelector("div#liveChat_chatHistory")
  let messageInput = liveChat_menu.querySelector("div#liveChat_messageInput")
  let newChatInput = liveChat_menu.querySelector("div#liveChat_newChatInput")
  let newChatInput_form = liveChat_menu.querySelector("form#newChatInput_form")
  let newChatInput_response = liveChat_menu.querySelector("div#newChatInput_response")

  let chats = sessionStorage.getItem("chats")
  let currentChat_index = 0

  let chatHistory_ongoingRequest

  let liveChat_menu_firstOpen = true

  // Get today's chats from database if not found in session storage
  if (chats === null) {
    chats = []
    currentChat_index = -1 // New chat

    // Get chats from database
    let formData = new FormData()
   
    formData.set("id", <?php echo $_SESSION["petownerid"]; ?>)

    let request = new XMLHttpRequest()

    request.addEventListener("readystatechange", () => {
      if (request.readyState !== 4) {
        return
      }

      if (request.status === 400) {
        chatList.innerHTML = "Error, check console log"
        console.error("Failed to get chats. Reason: " + request.responseText)
      }
      else if (request.status === 200) {
        let chats_fromDatabase = JSON.parse(request.responseText)

        if (chats_fromDatabase.length === 0) {
          return
        }

        chats.push(...chats_fromDatabase)
        sessionStorage.setItem("chats", JSON.stringify(chats))
        currentChat_index = 0

        updateChatList()
        updateLiveChatUI(currentChat_index)
      }
    })

    request.open("POST", "livechat_getUserChats.php")
    request.send(formData)
  }
  else {
    chats = JSON.parse(chats)
  }

  updateChatList()
  updateLiveChatUI(currentChat_index)



  // Open/close livechat
  // ===
  liveChat_button.addEventListener("click", () => {
    liveChat_menu.style.display = "revert"
    liveChat_button.style.display = "none"

    // Can't use scrollIntoView() when element is `display:none`
    if (liveChat_menu_firstOpen) {
      if (currentChat_index !== -1) {
        chatHistory.lastElementChild.scrollIntoView()
      }

      liveChat_menu_firstOpen = false
    }
  })

  let liveChat_menu_closeButton = liveChat_menu.querySelector("button#liveChat_menu_closeButton")

  liveChat_menu_closeButton.addEventListener("click", () => {
    console.log(document.getAnimations())
    liveChat_menu.style.display = "none"
    liveChat_button.style.display = "revert"

  })



  newChatInput_form.addEventListener("submit", (event) => {
    let formData = new FormData(newChatInput_form)
    let request = new XMLHttpRequest()

    request.addEventListener("readystatechange", () => {
      if (request.readyState !== 4) {
        return
      }

      if (request.status === 400) {
        newChatInput_response.innerHTML = "Failed to start new chat. Reason: "
          + request.responseText
      }
      else if (request.status === 200) {
        chats.push(JSON.parse(request.responseText))
        sessionStorage.setItem("chats", JSON.stringify(chats))

        // Update UI
        updateChatList()
        updateLiveChatUI(chats.length - 1)

        // Clear newChatInput_form
        newChatInput_form.reset()
      }
    })

    request.open("POST", "livechat_startNewChat.php")
    request.send(formData)

    event.preventDefault()
  })



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

    // Always add "new chat"
    let newChat_div = document.createElement("div")
    newChat_div.innerHTML = "New chat"
    newChat_div.addEventListener("click", () => {
      updateLiveChatUI(-1)
    })

    chatList.append(newChat_div)
  }



  // When clicking on existing chat, the right side of livechat changes to chat app UI,
  // showing current chat's chat history and text input to send new messages

  // When clicking on "start new chat", the right side of livechat changes to an input form for
  // starting a new chat
  function updateLiveChatUI(selectedChat_index) {
    // Selected new chat
    if (selectedChat_index === -1) {
      chatHistory.style.display = "none"
      messageInput.style.display = "none"
      newChatInput.style.display = "revert"

      // Highlight "new chat" in chatList
      if (currentChat_index !== -1) {
        chatList.children[currentChat_index].classList.remove("selected")
      }

      chatList.children[chats.length].classList.add("selected")
      currentChat_index = selectedChat_index
      return
    }

    // Selected existing chat
    chatHistory.style.display = "revert"
    messageInput.style.display = "revert"
    newChatInput.style.display = "none"

    // Highlight selected existing chat in chatList
    if (currentChat_index === -1) {
      chatList.children[chats.length].classList.remove("selected")
    }
    else {
      chatList.children[currentChat_index].classList.remove("selected")
    }

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
    formData.set("petownerid", <?php echo $_SESSION["petownerid"]; ?>)
    formData.set("nurseid", selectedChat.nurseId)
    formData.set("topic", selectedChat.topic)
    formData.set("latest_chatid", selectedChat.chatHistory.at(-1).chatid)

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
  // While displaying the messages, messages from the nurse will be marked as read
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
      if (message.role === "nurse" && message.chatreceivedate === null) {
        chatIds_toMarkAsRead.push(message.chatid)
      }
    }

    // Auto-scroll chatHistory
    chatHistory.lastElementChild.scrollIntoView()

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
  let messageInput_form = liveChat_menu.querySelector("form#messageInput_form")
  let messageInput_response = liveChat_menu.querySelector("div#messageInput_response")

  messageInput_form.addEventListener("submit", (event) => {
    let chat = chats[currentChat_index]
    let formData = new FormData(messageInput_form)

    formData.set("topic", chat.topic)
    formData.set("petownerid", <?php echo $_SESSION["petownerid"]; ?>)
    formData.set("nurseid", chat.nurseId)
    formData.set("role", "petowner")

    let request = new XMLHttpRequest()

    messageInput_response.style.display = "none"

    request.addEventListener("readystatechange", () => {
      if (request.readyState !== 4) {
        return
      }

      if (request.status === 400) {
        messageInput_response.innerHTML = "Failed to send message. Reason: "
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