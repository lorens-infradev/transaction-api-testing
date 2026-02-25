<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaction Chatbot</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-chat: #f8fafc;
            --bg-user: #2563eb;
            --bg-bot: #ffffff;
            --text-user: #ffffff;
            --text-bot: #1e293b;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        #chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        #chat-button {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }

        #chat-button:hover {
            transform: scale(1.05);
            background-color: var(--primary-hover);
        }

        #chat-window {
            width: 380px;
            height: 500px;
            background: var(--bg-chat);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            display: none;
            flex-direction: column;
            overflow: hidden;
            margin-bottom: 15px;
            border: 1px solid var(--border);
        }

        #chat-header {
            background: var(--primary);
            color: white;
            padding: 16px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #chat-messages {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .message {
            max-width: 80%;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.5;
        }

        .bot-message {
            align-self: flex-start;
            background: var(--bg-bot);
            color: var(--text-bot);
            border: 1px solid var(--border);
            border-bottom-left-radius: 2px;
        }

        .user-message {
            align-self: flex-end;
            background: var(--bg-user);
            color: var(--text-user);
            border-bottom-right-radius: 2px;
        }

        #chat-options {
            padding: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            border-top: 1px solid var(--border);
        }

        .option-btn {
            background: white;
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .option-btn:hover {
            background: var(--primary);
            color: white;
        }

        #chat-input-container {
            padding: 12px;
            border-top: 1px solid var(--border);
            display: none;
            gap: 8px;
        }

        #chat-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
            font-size: 14px;
        }

        #send-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-completed { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-failed { background: #fee2e2; color: #991b1b; }

        .loading-dots::after {
            content: '.';
            animation: dots 1.5s steps(5, end) infinite;
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60% { content: '...'; }
            80%, 100% { content: ''; }
        }
    </style>
</head>
<body>

    <div class="text-center" style="max-width: 600px; width: 100%;">
        <h1 style="font-size: 2.5rem; color: #1e293b; margin-bottom: 1rem;">Transaction API Testing</h1>
        <p style="color: #64748b; margin-bottom: 2rem;">Click the bubble in the bottom right to start the chatbot.</p>

        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); text-align: left;">
            <h2 style="font-size: 1.25rem; margin-bottom: 15px; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Test Transaction Numbers</h2>
            <ul style="list-style: none; padding: 0; margin: 0; max-height: 300px; overflow-y: auto;">
                @forelse($transactions as $trx)
                <li style="padding: 10px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <code style="background: #f8fafc; padding: 4px 8px; border-radius: 4px; color: #2563eb; font-weight: 600;">{{ $trx->transaction_number }}</code>
                    <span class="status-badge status-{{ $trx->status }}">{{ $trx->status }}</span>
                </li>
                @empty
                <li style="padding: 10px; color: #64748b; text-align: center;">No transactions found in the database.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div id="chat-widget">
        <div id="chat-window">
            <div id="chat-header">
                <span>Transaction Assistant</span>
                <button onclick="toggleChat()" style="background:none; border:none; color:white; cursor:pointer; font-size: 20px;">&times;</button>
            </div>
            <div id="chat-messages">
                <div class="message bot-message">Hello! I'm your transaction assistant. How can I help you today?</div>
            </div>
            <div id="chat-options">
                <button class="option-btn" onclick="handleOption('check_status')">Check Transaction Status</button>
                <button class="option-btn" onclick="handleOption('other')">Other Concerns</button>
            </div>
            <div id="chat-input-container">
                <input type="text" id="chat-input" placeholder="Enter transaction number..." onkeypress="handleKeypress(event)">
                <button id="send-btn" onclick="sendMessage()">Send</button>
            </div>
        </div>
        <button id="chat-button" onclick="toggleChat()">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
        </button>
    </div>

    <script>
        const chatWindow = document.getElementById('chat-window');
        const chatMessages = document.getElementById('chat-messages');
        const chatOptions = document.getElementById('chat-options');
        const inputContainer = document.getElementById('chat-input-container');
        const chatInput = document.getElementById('chat-input');

        let currentStep = 'initial';

        function toggleChat() {
            chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
        }

        function addMessage(text, type = 'bot') {
            const msg = document.createElement('div');
            msg.className = `message ${type}-message`;
            msg.innerHTML = text;
            chatMessages.appendChild(msg);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            return msg;
        }

        function handleOption(option) {
            if (option === 'check_status') {
                addMessage('Check Transaction Status', 'user');
                chatOptions.style.display = 'none';
                setTimeout(() => {
                    addMessage('Please enter your transaction number:');
                    inputContainer.style.display = 'flex';
                    chatInput.focus();
                    currentStep = 'awaiting_trx';
                }, 500);
            } else {
                addMessage('Other Concerns', 'user');
                setTimeout(() => {
                    addMessage('Currently, I can only help with transaction status. Please select "Check Transaction Status".');
                }, 500);
            }
        }

        function handleKeypress(e) {
            if (e.key === 'Enter') sendMessage();
        }

        async function sendMessage() {
            const val = chatInput.value.trim();
            if (!val) return;

            addMessage(val, 'user');
            chatInput.value = '';

            if (currentStep === 'awaiting_trx') {
                const loadingMsg = addMessage('Searching for transaction<span class="loading-dots"></span>');
                
                try {
                    const response = await fetch(`/api/transactions/${val}`);
                    const result = await response.json();

                    loadingMsg.remove();

                    if (response.ok) {
                        const trx = result.data;
                        const statusClass = `status-${trx.status}`;
                        addMessage(`
                            <strong>Transaction Found!</strong><br>
                            Number: ${trx.transaction_number}<br>
                            Amount: $${trx.amount}<br>
                            Status: <span class="status-badge ${statusClass}">${trx.status}</span><br>
                            Description: ${trx.description}<br>
                            Date: ${new Date(trx.created_at).toLocaleDateString()}
                        `);
                    } else {
                        addMessage(`Sorry, ${result.message || 'something went wrong'}. Please check the number and try again.`);
                    }
                } catch (error) {
                    loadingMsg.remove();
                    addMessage('Oops! I had trouble connecting to the server. Please try again later.');
                }
                
                // Show options again after result
                setTimeout(() => {
                    chatOptions.style.display = 'flex';
                    inputContainer.style.display = 'none';
                    currentStep = 'initial';
                }, 1000);
            }
        }
    </script>
</body>
</html>
