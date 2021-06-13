<template>
    <jet-action-section>
        <template #title>
            Delete Bot
        </template>

        <template #description>
            Permanently delete this bot.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Once a bot is deleted, all of its resources and data will be permanently deleted. Before deleting this bot, please download any data or information regarding this bot that you wish to retain.
            </div>

            <div class="mt-5">
                <jet-danger-button @click="confirmBotDeletion">
                    Delete bot
                </jet-danger-button>
            </div>

            <jet-confirmation-modal :show="confirmingBotDeletion" @close="confirmingBotDeletion = false">
                <template #title>
                    Delete Bot
                </template>

                <template #content>
                    Are you sure you want to delete this bot? Once a bot is deleted, all of its resources and data will be permanently deleted.
                </template>

                <template #footer>
                    <jet-secondary-button @click="confirmingBotDeletion = false">
                        Cancel
                    </jet-secondary-button>

                    <jet-danger-button class="ml-2" @click="deleteBot" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete Bot
                    </jet-danger-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </jet-action-section>
</template>

<script>
    import JetActionSection from '@/Components/ActionSection'
    import JetConfirmationModal from '@/Components/ConfirmationModal'
    import JetDangerButton from '@/Components/DangerButton'
    import JetSecondaryButton from '@/Components/SecondaryButton'

    export default {
        props: ['bot'],

        components: {
            JetActionSection,
            JetConfirmationModal,
            JetDangerButton,
            JetSecondaryButton,
        },

        data() {
            return {
                confirmingBotDeletion: false,
                deleting: false,

                form: this.$inertia.form()
            }
        },

        methods: {
            confirmBotDeletion() {
                this.confirmingBotDeletion = true
            },

            deleteBot() {
                this.form.delete(route('bots.destroy', this.bot), {
                    errorBag: 'deleteBot'
                });
            },
        },
    }
</script>
