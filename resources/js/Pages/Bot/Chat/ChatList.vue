<template>
    <div class="flex flex-col col-span-6">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Chat Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Chat Id
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="chat in chats" :key="chat.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ chat.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ chat.real_chat_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                  <span
                                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                      v-bind:class="{ 'bg-green-100 text-green-800': chat.is_available, 'bg-red-100 text-red-800': !chat.is_available }">
                                    {{ chat.is_available ? 'Active' : 'Inactive'}}
                                  </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <jet-button type="button" class="ml-2" @click="editChat(chat)">
                                    Edit
                                </jet-button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JetButton from '@/Components/Button';

export default {
    emits: ['editChat'],

    components: {
        JetButton,
    },

    props: [
        'bot',
        'chats'
    ],

    methods: {
        editChat(chat) {
            this.$emit('editChat', chat)
        }
    }
};
</script>
