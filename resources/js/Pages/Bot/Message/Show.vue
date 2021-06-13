<template>
    <jet-form-section @submitted="">
        <template #title>
            Messages
        </template>

        <template #description>
            Mange messages for the bot
        </template>

        <template #form>
            <message-list :messages='bot.messages' @editMessage="openModal"/>
            <jet-dialog-modal :show="messageModalOpened" @close="closeModal">
                <template #title>
                    {{ message.id ? 'Update message' : 'Create message' }}
                </template>

                <template #content>
                    <div class="col-span-6 sm:col-span-4 mb-3">
                        <jet-label for="name" value="Message name" />
                        <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus />
                        <jet-input-error :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-4 mb-3">
                        <jet-label for="text" value="Message text" />
                        <textarea id="text" name="text" v-model="form.text" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"/>
                        <jet-input-error :message="form.errors.text" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <label for="parse_mode" class="block text-sm font-medium text-gray-700">Parse Mode</label>
                        <select id="parse_mode" v-model="form.parse_mode" name="parse_mode" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full">
                            <option value="HTML">HTML</option>
                            <option value="Markdown">Markdown</option>
                            <option value="MarkdownV2">MarkdownV2</option>
                        </select>
                        <jet-input-error :message="form.errors.parse_mode" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <jet-secondary-button @click="closeModal">
                        Cancel
                    </jet-secondary-button>

                    <jet-danger-button v-if="message.id" class="ml-2" @click="deleteMessage" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete
                    </jet-danger-button>

                    <jet-button type="button" class="ml-2" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing" @click="messageForm">
                        Save
                    </jet-button>
                </template>
            </jet-dialog-modal>
        </template>

        <template #actions>
            <jet-button type="button" :class="{ 'opacity-25': messageModalOpened }" :disabled="messageModalOpened"
                        @click="openModal">
                Create
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
import JetButton from '@/Components/Button';
import JetDangerButton from '@/Components/DangerButton'
import JetFormSection from '@/Components/FormSection';
import JetInput from '@/Components/Input';
import JetInputError from '@/Components/InputError';
import JetLabel from '@/Components/Label';
import JetDialogModal from '@/Components/DialogModal';
import MessageList from './MessageList';

export default {
    components: {
        JetButton,
        JetDangerButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetDialogModal,
        MessageList,
    },

    props: [
        'bot'
    ],

    data() {
        return {
            messageModalOpened: false,
            message: null,
            form: this.$inertia.form({
                name: '',
                text: '',
                parse_mode: 'HTML',
            }),
        };
    },

    methods: {
        openModal(message = null) {
            this.message = message;
            this.messageModalOpened = true;
            this.form.name = message.name;
            this.form.text = message.text;
            this.form.parse_mode = message.parse_mode || 'HTML';
        },

        closeModal() {
            this.message = null;
            this.messageModalOpened = false;
        },

        messageForm() {
            if (this.message?.id) {
                this.form.put(route('bots.messages.update', [this.$props.bot, this.message]), {
                    errorBag: 'updateMessage',
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                });

            } else {
                this.form.post(route('bots.messages.store', this.$props.bot), {
                    errorBag: 'storeMessage',
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                });
            }
        },

        deleteMessage() {
            this.form.delete(route('bots.messages.destroy', [this.$props.bot, this.message]), {
                errorBag: 'deleteMessage',
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
            });
        }
    },
};
</script>
