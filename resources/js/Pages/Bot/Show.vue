<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bot Settings
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-bot-form :bot="bot" :permissions="permissions" />

                <template v-if="bot.chats?.length && bot.messages?.length">
                    <jet-section-border />
                    <show-schedule-event :bot='bot'/>
                </template>

                <jet-section-border />
                <show-chats :bot='bot'/>


                <template v-if="bot.chats?.length">
                    <jet-section-border />
                    <show-messages :bot='bot'/>
                </template>


                <template v-if="permissions.canDeleteBot">
                    <jet-section-border />

                    <delete-bot-form class="mt-10 sm:mt-0" :bot="bot" />
                </template>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import DeleteBotForm from './DeleteBotForm'
    import JetSectionBorder from '@/Components/SectionBorder'
    import UpdateBotForm from './UpdateBotForm'
    import ShowChats from './Chat/Show'
    import ShowMessages from './Message/Show'
    import ShowScheduleEvent from './ScheduleEvent/Show'

    export default {
        props: [
            'bot',
            'permissions',
        ],

        components: {
            AppLayout,
            DeleteBotForm,
            JetSectionBorder,
            UpdateBotForm,
            ShowChats,
            ShowMessages,
            ShowScheduleEvent,
        },
    }
</script>
