<div class="fixed bottom-4 right-4 z-50" x-data="{ showChat: true }" @scroll-to-bottom.window="document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight">
    <!-- Chat Toggle Button -->
    <button
        @click="showChat = !showChat"
        class="bg-purple-600 hover:bg-purple-700 text-white rounded-full p-4 shadow-lg transition-all duration-300 transform hover:scale-110"
        x-show="!showChat"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
    </button>

    <!-- Chat Window -->
    <div
        class="bg-white rounded-lg shadow-2xl w-96 h-[500px] flex flex-col border border-gray-200"
        x-show="showChat"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
    >
        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-4 rounded-t-lg flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold">ARUMA Hair Salon Assistant</h3>
                    <p class="text-sm text-purple-100">Online • Ready to help</p>
                </div>
            </div>
            <button @click="showChat = false" class="text-white hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages">
            @foreach($messages as $message)
                @if($message['type'] === 'user')
                    <!-- User Message -->
                    <div class="flex justify-end">
                        <div class="text-gray-800 rounded-lg px-4 py-2 max-w-xs">
                            <p class="text-sm">{{ $message['content'] }}</p>
                            <p class="text-xs text-gray-500 text-right mt-1">{{ \Carbon\Carbon::parse($message['timestamp'])->format('g:i A') }}</p>
                        </div>
                    </div>
                @elseif($message['type'] === 'bot')
                    <!-- Bot Message -->
                    <div class="flex justify-start">
                        <div class="text-gray-800 rounded-lg px-4 py-2 max-w-xs">
                            <div class="prose prose-sm max-w-none text-gray-800">
                                {!! \Illuminate\Support\Str::markdown($message['content']) !!}
                            </div>
                            <p class="text-xs text-gray-500 text-right mt-1">{{ \Carbon\Carbon::parse($message['timestamp'])->format('g:i A') }}</p>
                        </div>
                    </div>
                @elseif($message['type'] === 'suggestions')
                    <!-- Suggested Questions -->
                    <div class="flex justify-start">
                        <div class="rounded-lg p-3 max-w-xs">
                            <p class="text-xs text-gray-600 font-semibold mb-2">Or, ask me one of these:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($message['content'] as $suggestion)
                                    <button
                                        wire:click="selectSuggestion('{{ $suggestion }}')"
                                        class="text-xs text-gray-700 hover:text-gray-900 px-2 py-1 rounded-full transition-colors underline"
                                    >
                                        {{ $suggestion }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Typing Indicator -->
            @if($isTyping)
                <div class="flex justify-start">
                    <div class="rounded-lg p-3">
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Chat Input Area -->
        <div class="border-t border-gray-200 p-4 bg-white">
            <div class="flex space-x-2">
                <input
                    type="text"
                    wire:model="userInput"
                    wire:keydown.enter="sendMessage"
                    placeholder="Type your message..."
                    class="flex-1 w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                    autocomplete="off"
                >
                <button
                    wire:click="sendMessage"
                    class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50"
                    wire:loading.attr="disabled"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
            @if($currentStep === 'idle')
            <div class="mt-2 text-center">
                <button
                    wire:click="sendMessage"
                    wire:model.lazy="userInput"
                    x-on:click="$wire.set('userInput', 'I want to book an appointment')"
                    class="text-sm text-purple-600 hover:text-purple-800 underline"
                >
                    Book an appointment
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Auto-scroll to bottom when new messages are added
    document.addEventListener('livewire:updated', function () {
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });
</script>
