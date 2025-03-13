@extends('layouts.app')

@section('title', 'Chat dengan AI Travel Assistant')

@push('styles')
<style>
    .chat-container {
        height: calc(100vh - 220px);
        min-height: 500px;
    }
    
    .message-bubble {
        max-width: 80%;
        border-radius: 1rem;
        padding: 0.75rem 1rem;
        margin-bottom: 0.75rem;
        position: relative;
        word-wrap: break-word;
    }
    
    .user-message {
        background-color: #4f46e5; /* Indigo-600 */
        color: white;
        border-top-right-radius: 0.25rem;
        margin-left: auto;
    }
    
    .admin-message {
        background-color: #f3f4f6; /* Gray-100 */
        color: #1f2937; /* Gray-800 */
        border-top-left-radius: 0.25rem;
    }
    
    .typing-indicator {
        background-color: #f3f4f6;
        width: auto;
        border-radius: 1rem;
        padding: 0.5rem 1rem;
        display: none;
        align-items: center;
        position: relative;
        animation: slidein 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    
    @keyframes slidein {
        from { transform: translateY(10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .typing-dot {
        height: 8px;
        width: 8px;
        border-radius: 50%;
        background-color: #9ca3af; /* Gray-400 */
        margin: 0 2px;
        display: inline-block;
        animation: typing-dot 1.4s infinite ease-in-out both;
    }
    
    .typing-dot:nth-child(1) { animation-delay: 0s; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    
    @keyframes typing-dot {
        0%, 80%, 100% { transform: scale(0.8); }
        40% { transform: scale(1.2); }
    }
    
    .animate-message {
        animation: message-appear 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    
    @keyframes message-appear {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .suggestion-item {
        transition: all 0.2s ease;
    }
    
    .suggestion-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    /* Scrollbar Styling */
    .messages-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .messages-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .messages-container::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    
    .messages-container::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    /* Mobile Responsiveness */
    @media (max-width: 640px) {
        .chat-container {
            height: calc(100vh - 180px);
        }
        
        .message-bubble {
            max-width: 90%;
        }
    }
</style>
@endpush

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Chat Header -->
            <div class="bg-indigo-600 p-4 text-white">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center mr-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold">AI Travel Assistant</h3>
                        <p class="text-xs text-indigo-200">Online - Siap membantu 24/7</p>
                    </div>
                </div>
            </div>
            
            <!-- Chat Body -->
            <div class="chat-container flex flex-col">
                <!-- Messages Container -->
                <div id="messages-container" class="messages-container flex-1 p-4 overflow-y-auto">
                    <!-- Welcome Message -->
                    <div class="message-bubble admin-message">
                        <p>Halo, {{ Auth::user()->name }}! 👋</p>
                        <p class="mt-2">Saya adalah AI Travel Assistant yang siap membantu Anda merencanakan perjalanan wisata. Anda dapat menanyakan tentang:</p>
                        <ul class="list-disc pl-5 mt-2">
                            <li>Rekomendasi destinasi wisata</li>
                            <li>Informasi tentang tempat menarik</li>
                            <li>Tips perjalanan dan aktivitas</li>
                            <li>Estimasi biaya dan informasi lainnya</li>
                        </ul>
                        <p class="mt-2">Apa yang bisa saya bantu hari ini?</p>
                    </div>
                    
                    <!-- Existing Messages -->
                    @foreach($messages as $message)
                        <div class="message-bubble {{ $message->is_from_admin ? 'admin-message' : 'user-message' }}">
                            {{ $message->message }}
                            <div class="text-xs {{ $message->is_from_admin ? 'text-gray-500' : 'text-indigo-200' }} mt-1 text-right">
                                {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Typing Indicator -->
                <div id="typing-indicator" class="typing-indicator ml-4 mb-3">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
                
                <!-- Suggested Messages (shown for first-time users or after inactivity) -->
                <div id="suggested-messages" class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Pertanyaan yang sering ditanyakan:</p>
                    <div class="flex space-x-2 overflow-x-auto pb-2">
                        <button type="button" class="suggestion-item px-3 py-1 bg-white border border-gray-300 rounded-full text-xs text-gray-700 whitespace-nowrap">Rekomendasi tempat untuk liburan keluarga</button>
                        <button type="button" class="suggestion-item px-3 py-1 bg-white border border-gray-300 rounded-full text-xs text-gray-700 whitespace-nowrap">Destinasi populer di Bali</button>
                        <button type="button" class="suggestion-item px-3 py-1 bg-white border border-gray-300 rounded-full text-xs text-gray-700 whitespace-nowrap">Liburan dengan budget 2 juta</button>
                        <button type="button" class="suggestion-item px-3 py-1 bg-white border border-gray-300 rounded-full text-xs text-gray-700 whitespace-nowrap">Tips travelling ke Lombok</button>
                    </div>
                </div>
                
                <!-- Message Input -->
                <div class="p-4 border-t border-gray-200">
                    <form id="message-form" class="flex items-center space-x-2">
                        @csrf
                        <input type="text" id="message-input" name="message" class="flex-1 border-gray-300 rounded-full focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ketik pesan Anda..." required autocomplete="off">
                        <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Travel Inspiration Section -->
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Inspirasi Perjalanan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="text-indigo-600 mb-2">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">Destinasi Populer</h3>
                    <p class="text-gray-600 text-sm">Tanyakan tentang destinasi wisata terkenal di Indonesia dan dunia.</p>
                    <button type="button" class="mt-3 text-indigo-600 text-sm font-medium hover:text-indigo-800 suggestion-trigger">Tanya sekarang</button>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="text-indigo-600 mb-2">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">Tips Budget</h3>
                    <p class="text-gray-600 text-sm">Dapatkan saran perjalanan sesuai dengan budget yang Anda miliki.</p>
                    <button type="button" class="mt-3 text-indigo-600 text-sm font-medium hover:text-indigo-800 suggestion-trigger">Tanya sekarang</button>
                </div>
                
                <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="text-indigo-600 mb-2">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">Rencana Perjalanan</h3>
                    <p class="text-gray-600 text-sm">Buat itinerary perjalanan yang sesuai dengan durasi dan preferensi Anda.</p>
                    <button type="button" class="mt-3 text-indigo-600 text-sm font-medium hover:text-indigo-800 suggestion-trigger">Tanya sekarang</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const messagesContainer = document.getElementById('messages-container');
        const typingIndicator = document.getElementById('typing-indicator');
        const suggestedMessages = document.getElementById('suggested-messages');
        
        // Scroll to bottom of messages container
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Initially scroll to bottom
        scrollToBottom();
        
        // Show typing indicator
        function showTypingIndicator() {
            typingIndicator.style.display = 'flex';
            scrollToBottom();
        }
        
        // Hide typing indicator
        function hideTypingIndicator() {
            typingIndicator.style.display = 'none';
        }
        
        // Add message to chat
        function addMessage(message, isUser = false) {
            const messageElement = document.createElement('div');
            messageElement.classList.add('message-bubble', 'animate-message');
            
            if (isUser) {
                messageElement.classList.add('user-message');
            } else {
                messageElement.classList.add('admin-message');
            }
            
            const now = new Date();
            const timeString = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            
            messageElement.innerHTML = `
                ${message}
                <div class="text-xs ${isUser ? 'text-indigo-200' : 'text-gray-500'} mt-1 text-right">
                    ${timeString}
                </div>
            `;
            
            messagesContainer.appendChild(messageElement);
            scrollToBottom();
        }
        
        // Handle form submission
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;
            
            // Add user message to chat
            addMessage(message, true);
            
            // Clear input
            messageInput.value = '';
            
            // Show typing indicator
            showTypingIndicator();
            
            // Send message to server
            fetch('{{ route("chat.sendMessage") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                // Hide typing indicator after a delay to simulate typing
                setTimeout(() => {
                    hideTypingIndicator();
                    
                    // Add AI response
                    addMessage('Terima kasih atas pertanyaan Anda. Saya sedang memproses permintaan Anda dan akan memberikan respon segera.');
                    
                    // Hide suggestions after first message
                    suggestedMessages.style.display = 'none';
                }, 1500);
            })
            .catch(error => {
                console.error('Error:', error);
                hideTypingIndicator();
                addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', false);
            });
        });
        
        // Handle suggestion clicks
        const suggestionItems = document.querySelectorAll('.suggestion-item, .suggestion-trigger');
        suggestionItems.forEach(item => {
            item.addEventListener('click', function() {
                let text = '';
                
                if (this.classList.contains('suggestion-trigger')) {
                    // Get text based on parent heading
                    const heading = this.parentElement.querySelector('h3').textContent;
                    
                    if (heading.includes('Destinasi')) {
                        text = 'Rekomendasikan destinasi wisata populer di Indonesia';
                    } else if (heading.includes('Budget')) {
                        text = 'Bagaimana cara traveling dengan budget 3 juta rupiah?';
                    } else if (heading.includes('Rencana')) {
                        text = 'Buatkan saya itinerary 3 hari di Yogyakarta';
                    }
                } else {
                    // Use the suggestion text
                    text = this.textContent;
                }
                
                // Set input value
                messageInput.value = text;
                
                // Focus input
                messageInput.focus();
            });
        });
        
        // Check for new messages every 5 seconds
        let lastMessageId = {{ $messages->count() > 0 ? $messages->last()->id : 0 }};
        
        function checkNewMessages() {
            fetch(`{{ route('chat.getNewMessages') }}?last_id=${lastMessageId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        // Add new message to chat
                        addMessage(message.message, !message.is_from_admin);
                        lastMessageId = message.id;
                    });
                }
            })
            .catch(error => console.error('Error checking for new messages:', error));
        }
        
        // Start polling for new messages
        setInterval(checkNewMessages, 5000);
    });
</script>
@endpush