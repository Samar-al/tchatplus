<head>
    <title>Chat</title>
    
   
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>  
<body>
<div class="flex-content" style="display: flex; justify-content: {if $chat_position == 'left'}flex-start{else}flex-end{/if};">
    <div id="chat">
    {if !$isBackOffice}
        <div class="chat-icon" id="chat-icon" data-visitor="{$visitor}">
            <img src="../modules/tchatplus/views/img/chat.png" alt="Chat Icon">
        </div>
    {/if}   
        <div id="messages" class="chat-container" style="background-color: {$main_color}; display: none;">
            <div class="chat-header">
                <h2>{$header_text|escape:'htmlall':'UTF-8'}</h2>
            </div>
            <div class="chat-content" id="chat-content">
                <div class="welcome-message">
                    <p>{$welcome_text|escape:'htmlall':'UTF-8'}</p>
                    {if $offline == 1 }
                     <p>We open from {$opening_hours|escape:'htmlall':'UTF-8'} to {$closing_hours|escape:'htmlall':'UTF-8'}</p>
                    {/if}
                    {if $offline == 1 && $name == 1}
                        <input type="text" id="name" name="name" value="" placeholder="Enter your name..." required>
                    {/if}
                    {if $offline == 1 && $firstname == 1}
                        <input type="text" id="firstname" name="firstname" value="" placeholder="Enter your firstname..." required>
                    {/if}
                    {if $offline == 1 && $phone == 1}
                        <input type="tel" id="phone" name="number" value="" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="Enter your number..." required>
                    {/if}
                    {if $offline == 1 && $email == 1}
                        <input type="email" id="email" name="email" placeholder="Enter your email..." required>
                    {/if}    
                </div>
                <div id="chat-messages" class="message-container">      
                </div>
                <div id="chat-admin" class="message-container">      
                </div>
            </div>
            {if !$isBackOffice}
                <form id="onlineChatForm" name="onlineChatForm" method="post">
                    {if $offline == 1 && $message == 0}
                        <input type="submit" value="Envoyer">
                    {else}
                        <input type="text" id="message" name="message" autocomplete="off" value="" autofocus placeholder="Type message...">
                        <input type="submit" name="submitAddtchat_plus" value="Envoyer">
                    {/if}
                </form>
            {/if}
            {if $isBackOffice}
                <form id="adminResponseForm">
                    <input type="text" id="adminMessage" name="adminMessage" autocomplete="off" value="" autofocus placeholder="Type your response..." style="width: 95%;">
                    <input type="submit" value="Send">
                </form>
            {/if}
        </div>
    </div>
</div>
 <script>
     let clientColor = '{$client_color}';
     let advisorColor = '{$advisor_color}';
     let offline = '{$offline}';
   
    </script>
</body>