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
                                Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Text
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="message in messages" :key="message.id">
                            <td class="px-6 py-4 ">
                                <div class="overflow-ellipsis whitespace-nowrap overflow-hidden text-sm text-gray-900">{{ message.name }}</div>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <div class="overflow-ellipsis whitespace-nowrap overflow-hidden text-sm text-gray-900">{{ message.text }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <jet-button type="button" class="ml-2" @click="editMessage(message)">
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
    emits: ['editMessage'],

    components: {
        JetButton,
    },

    props: [
        'bot',
        'messages'
    ],

    methods: {
        editMessage(message) {
            this.$emit('editMessage', message)
        }
    }
};
</script>
