
let sender;
let visitor;
$(document).ready(function() {

    $('.chat-row').click(function () {
        // Get the sender from the clicked row's data attribute
        sender = $(this).data('sender');
        // Get the chat container element
        let chatContainer = $('#messages');
        // Toggle the display of the chat container
        chatContainer.toggle();

        // Scroll to the chat container
        if (chatContainer.is(':visible')) {
           /*  $('html, body').animate({
                scrollTop: chatContainer.offset().top
            }, 500); */ 

            fetchAndDisplayMessages(sender || visitor);
            
            setInterval(function() {
                fetchAndDisplayMessages(sender || visitor);
            }, 2000); 
        }
    });
    $('#chat-icon').click(function() {
        visitor = $(this).data('visitor');
        let chatContainer = $('#messages');
        // Toggle the display of the chat container
        chatContainer.toggle();

        // Scroll to the chat container
        if (chatContainer.is(':visible')) {
            $('html, body').animate({
                scrollTop: chatContainer.offset().top
            }, 500); 
            if(offline == 0){
                fetchAndDisplayMessages(visitor || sender);
                setInterval(function() {
                    fetchAndDisplayMessages(sender || visitor);
                  }, 2000);
            }
        }    
    });

            
    
    $('#onlineChatForm').submit(function(e) {
        e.preventDefault(); 
        
       if(offline == 1){
            sendEmailWithFormData();
        }else{

            let message = $('#message').val();
            
            $.ajax({
                type: 'POST',
                url: front_ajax,
                data: message,
                success: function(response) {
                    // Handle success response if needed
                    console.log('Data sent successfully.');
                    // Clear the input field
                    $('#message').val('')
                    
                    fetchAndDisplayMessages(sender || visitor);
                    
                    let chatContent = $('.chat-content');
                    let chatContainer = $('#chat-messages');
                    chatContent.scrollTop(chatContainer[0].scrollHeight);
                },
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                error: function(xhr, status, error) {
                    // Handle error if needed
                    //console.log(status, error);
                    console.error('Failed to send data.');
                    
                }
            });
            
            return false;
        }
            
        });
        function fetchAndDisplayMessages(sender) {
        $.ajax({
            type: 'GET',
            url: front_ajax,
            dataType: 'json',
            data: {
                sender: sender
            },
            success: function(response) {
                console.log('Received messages and responses:', response);
                
                 // Combine messages and responses into a single array
                let combinedMessages = response.filter(item => item.message);
                let combinedResponses = response.filter(item => item.response);
                let sortedCombined = [...combinedMessages, ...combinedResponses].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                
                let chatContainer = $('#chat-messages');
                let chatContent = $('.chat-content');
                chatContainer.empty();
    
                sortedCombined.forEach(function(item) {
                    let createdAt = item.created_at;
                    let content = item.message || item.response;
                    let from = item.from || 'Administrator';
                    let chatHeader;
                    
                    if (from === 'Administrator') {
                        chatHeader = $('<div class="message-header" style="background-color: ' + advisorColor + ';">');
                    } else {
                        chatHeader = $('<div class="message-header" style="background-color: ' + clientColor + ';">');
                    }
    
                    let messageElement = $('<div class="message">');
                    let chatBody = $('<div class="message-body">');
    
                    chatHeader.append('<p>' + from + '</p>');
                    chatBody.append('<p>' + content + '</p>');
                    chatBody.append('<p class="created-at">' + createdAt + '</p>');
    
                    messageElement.append(chatHeader);
                    messageElement.append(chatBody);
    
                    chatContainer.append(messageElement);
                });
    
                chatContent.scrollTop(chatContainer[0].scrollHeight);
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch messages.');
            }
        });
    }
    
    
    
    
    $('#adminResponseForm').submit(function(e) {
        e.preventDefault(); // Prevent the default form submission

        let response = $('#adminMessage').val();
        let recipient = sender;
        let data = {
            response: response,
            sender: recipient,
        }
       
        $.ajax({
            type: 'POST',
            url: back_ajax,
            data: data,
            success: function(response) {
                // Handle success response if needed
                console.log('Data sent successfully.');
                // Clear the input field
                $('#adminMessage').val('')

                fetchAndDisplayMessages(visitor);

                let chatContainer = $('#chat-admin');
                let chatContent = $('.chat-content');
                chatContent.scrollTop(chatContainer[0].scrollHeight);
            },
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            error: function(xhr, status, error) {
                // Handle error if needed
                //console.log(status, error);
                console.error('Failed to send data.');
                
            }
        });
        
        return false;
    });

    function sendEmailWithFormData() {
        // Gather input values
        let message = $('#message').val();
        let name = $('#name').val();
        let firstname = $('#firstname').val();
        let phone = $('#phone').val();
        let email = $('#email').val();
    
        // Construct email content
        let emailContent = `
            Name: ${name}
            First Name: ${firstname}
            Phone: ${phone}
            Email: ${email}
            Message: ${message}
        `;
    
        $.ajax({
            type: 'POST',
            url: 'http://localhost:8888/PrestaShopFormation/prestashop_edition_basic_version_8.0.4/offlinemails',
            data: {
                content: emailContent
            },
            //contentType: 'application/JSON',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function(response) {
                if (response.success) {
                    console.log('Email sent successfully.');
                    // Display success message in the chat interface
                    appendMessageToChat('System', 'Email sent successfully.');
                    $('#message').val('');
                    $('#name').val('');
                    $('#firstname').val('');
                    $('#phone').val('');
                    $('#email').val('');
                } else {
                    console.error('Failed to send email.');
                    // Display error message in the chat interface
                    appendMessageToChat('System', 'Failed to send email. Please fill in all required fields.');
                }
               
            },
            error: function(xhr, status, error) {
                console.error('Failed to send email.');
            }
        });
    }

    // Function to append a message to the chat interface
    function appendMessageToChat(sender, message) {
        // Create a new chat message element
        let newMessage = document.createElement('div');
        newMessage.className = 'chat-message';
        newMessage.innerHTML = '<strong>' + sender + ':</strong> ' + message;

        // Append the new message to the chat messages container
        let chatContainer = document.getElementById('chat-content');
        chatContainer.appendChild(newMessage);

        // Scroll to the bottom of the chat container to show the new message
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    
});

