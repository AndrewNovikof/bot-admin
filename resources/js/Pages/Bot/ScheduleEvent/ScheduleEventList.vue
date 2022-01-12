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
                                Cron Expression
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Next sending date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Enabled
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="scheduleEvent in scheduleEvents" :key="scheduleEvent.id">
                            <td class="px-6 py-4 ">
                                <div class="overflow-ellipsis whitespace-nowrap overflow-hidden text-sm text-gray-900">{{ scheduleEvent.name }}</div>
                            </td>
                            <td class="px-6 py-4 ">
                                <div class="overflow-ellipsis whitespace-nowrap overflow-hidden text-sm text-gray-900">{{ scheduleEvent.cron_expression }}</div>
                            </td>
                            <td class="px-6 py-4 ">
                                <div class="overflow-ellipsis whitespace-nowrap overflow-hidden text-sm text-gray-900">{{ scheduleEvent.next_due_date }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                  <span
                                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                      v-bind:class="{ 'bg-green-100 text-green-800': scheduleEvent.is_available, 'bg-red-100 text-red-800': !scheduleEvent.is_available }">
                                    {{ scheduleEvent.is_available ? 'Active' : 'Inactive'}}
                                  </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        v-bind:class="{ 'bg-green-100 text-green-800': scheduleEvent.is_enabled, 'bg-red-100 text-red-800': !scheduleEvent.is_enabled }">
                                      {{ scheduleEvent.is_enabled ? 'Enabled' : 'Disabled'}}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <jet-button type="button" class="ml-2" @click="editScheduleEvent(scheduleEvent)">
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
    emits: ['editScheduleEvent'],

    components: {
        JetButton,
    },

    props: [
        'scheduleEvents'
    ],

    methods: {
        editScheduleEvent(scheduleEvent) {
            this.$emit('editScheduleEvent', scheduleEvent)
        }
    }
};
</script>
