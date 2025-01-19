<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Customer Service Bot</title>
    <style>
        /* Your existing styles */
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            AI Customer Service Bot
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message bot-message">
                <div class="message-content">
                    Hello! I'm your AI assistant. How can I help you today?
                </div>
            </div>
            <div class="typing-indicator" id="typingIndicator">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="userInput" placeholder="Type your message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        const knowledgeBase = {
            greetings: {
                patterns: ['hi', 'hello', 'hey', 'greetings'],
                responses: [
                    "Hello! How can I assist you today?",
                    "Hi there! What can I help you with?",
                    "Greetings! How may I be of service?",
                    "Hello! I'm here to help. What do you need?"
                ]
            },
            farewell: {
                patterns: ['bye', 'goodbye', 'see you', 'take care'],
                responses: [
                    "Goodbye! Have a wonderful day!",
                    "Take care! Feel free to return if you need more help.",
                    "Bye for now! Don't hesitate to ask if you have more questions.",
                    "See you later! Thanks for chatting with me!"
                ]
            },
            login: {
                patterns: ['login', 'sign in', 'access account', 'cant login'],
                responses: [
                    "Let me help you with logging in. Here's what you need to do:\n1. Visit our login page\n2. Enter your email/username\n3. Input your password\n4. Click 'Sign In'\n\nIf you're having trouble, I can help with password reset or other login issues.",
                    "To access your account:\n1. Go to the login section\n2. Provide your credentials\n3. Verify your identity if required\n\nAre you experiencing any specific login issues?"
                ]
            }
            // Add other topics as needed
        };

        let conversationContext = {
            lastTopic: null,
            questionCount: 0
        };

        // Display typing indicator
        function showTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            indicator.style.display = 'block';
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Hide typing indicator
        function hideTypingIndicator() {
            document.getElementById('typingIndicator').style.display = 'none';
        }

        // Add a message to the chat
        function addMessage(message, isUser = false) {
            const chatMessages = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'user-message' : 'bot-message'}`;
            messageDiv.innerHTML = `
                <div class="message-content">
                    ${message.replace(/\n/g, '<br>')}
                </div>
            `;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Find best match in the knowledge base
        function findBestMatch(input) {
            const words = input.toLowerCase().split(' ');
            let bestMatch = null;
            let maxMatchCount = 0;

            for (const [topic, data] of Object.entries(knowledgeBase)) {
                const matchCount = data.patterns.reduce((count, pattern) => {
                    return count + words.filter(word => word.includes(pattern)).length;
                }, 0);

                if (matchCount > maxMatchCount) {
                    maxMatchCount = matchCount;
                    bestMatch = topic;
                }
            }

            return maxMatchCount > 0 ? bestMatch : null;
        }

        // Get response from Hugging Face API
        async function getAIResponse(query) {
            const apiKey = ""; // Replace with your Hugging Face API key
            const apiUrl = "https://huggingface.co/microsoft/phi-4"; // Example model endpoint

            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${apiKey}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ inputs: query })
                });

                const data = await response.json();

                if (data && data[0] && data[0].generated_text) {
                    return data[0].generated_text.trim();
                } else {
                    return "Sorry, I couldn't understand that.";
                }
            } catch (error) {
                console.error("Error fetching AI response:", error);
                return "There was an issue connecting to the AI service. Please try again.";
            }
        }

        // Generate the chatbot's response
        async function generateResponse(userInput) {
            const topic = findBestMatch(userInput);

            if (topic) {
                const responses = knowledgeBase[topic].responses;
                const response = responses[Math.floor(Math.random() * responses.length)];

                if (topic === conversationContext.lastTopic) {
                    conversationContext.questionCount++;
                    if (conversationContext.questionCount > 1) {
                        return response + "\n\nWould you like to explore a different topic? I can help with various other services as well.";
                    }
                } else {
                    conversationContext.lastTopic = topic;
                    conversationContext.questionCount = 1;
                }

                return response;
            } else {
                // Get response from AI if no match in knowledge base
                const aiResponse = await getAIResponse(userInput);
                return aiResponse;
            }
        }

        // Send user message and get response
        async function sendMessage() {
            const userInput = document.getElementById('userInput');
            const message = userInput.value.trim();

            if (message) {
                addMessage(message, true);
                userInput.value = '';

                showTypingIndicator();

                try {
                    const response = await generateResponse(message);
                    hideTypingIndicator();
                    addMessage(response);
                } catch (error) {
                    hideTypingIndicator();
                    addMessage("I apologize, but I encountered an error while processing your request. Please try again.");
                }
            }
        }

        // Allow Enter key to send message
        document.getElementById('userInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html>
