<div
    x-data="{
        messages: [],
        remove(message) {
            this.messages = this.messages.filter(m => m !== message)
        }
    }"
    @notify.window="let m = $event.detail; messages.push(m); setTimeout(() => remove(m), 5000)"
    class="fixed bottom-4 right-4 z-50 flex flex-col space-y-2 w-full max-w-xs"
>
    <template x-for="message in messages" :key="message">
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-8"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-8"
            class="p-4 rounded-lg shadow-lg border-l-4 flex items-center justify-between"
            :class="{
                'bg-green-50 border-green-500 text-green-800 dark:bg-green-900/30 dark:text-green-400': message.type === 'success',
                'bg-red-50 border-red-500 text-red-800 dark:bg-red-900/30 dark:text-red-400': message.type === 'error',
                'bg-blue-50 border-blue-500 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': message.type === 'info',
                'bg-yellow-50 border-yellow-500 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': message.type === 'warning'
            }"
        >
            <div class="flex items-center">
                <span x-text="message.text"></span>
            </div>
            <button @click="remove(message)" class="ml-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </template>
</div>
