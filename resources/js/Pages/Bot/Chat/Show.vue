<template>
    <jet-form-section @submitted="">
        <template #title>
            Chats
        </template>

        <template #description>
            Mange chats for the bot
        </template>

        <template #form>
            <chat-list :chats='bot.chats' @editChat="openModal"/>
            <jet-dialog-modal :show="chatModalOpened" @close="closeModal">
                <template #title>
                    {{ chat.id ? 'Update chat' : 'Create chat' }}
                </template>

                <template #content>
                    <div class="col-span-6 sm:col-span-4 mb-3 font-medium text-sm text-gray-700" v-if="chat.id">
                        <span class="pr-4">Status</span><span class="block"
                                                              v-bind:class="{ 'text-green-500': chat.is_available, 'text-red-500': !chat.is_available }">{{
                            chat.is_available ? 'Active' : 'Inactive'
                        }}</span>
                    </div>
                    <div class="col-span-6 sm:col-span-4 mb-3 font-medium text-sm text-gray-700 col-span-6"
                         v-if="chat.id && !chat.is_available">
                        <span class="pr-4">Last Error Message</span><span
                        class="block text-gray-500">{{ chat.last_error_message }}</span>
                    </div>
                    <div class="col-span-6 sm:col-span-4 mb-3">
                        <jet-label for="name" value="Chat Name"/>
                        <jet-input @change="setIsFormSaved(false)" id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus/>
                        <jet-input-error :message="form.errors.name" class="mt-2"/>
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <jet-label for="real_chat_id" value="Chat Id"/>
                        <jet-input @change="setIsFormSaved(false)" id="real_chat_id" type="text" class="mt-1 block w-full" v-model="form.real_chat_id"
                                   autofocus/>
                        <jet-input-error :message="form.errors.real_chat_id" class="mt-2"/>
                    </div>
                </template>

                <template #footer>
                    <jet-secondary-button class="mb-2" @click="closeModal">
                        Cancel
                    </jet-secondary-button>

                    <jet-danger-button v-if="chat.id" class="ml-2 mb-2" @click="deleteChat"
                                       :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete
                    </jet-danger-button>

                    <jet-success-button v-if="chat.id" class="ml-2 mb-2" @click="callAction"
                                        :class="{ 'opacity-25': form.processing }" :disabled="form.processing || !isFormSaved">
                        Test
                    </jet-success-button>

                    <jet-button type="button" class="ml-2 mb-2" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing" @click="chatForm">
                        Save
                    </jet-button>
                </template>
            </jet-dialog-modal>
        </template>

        <template #actions>
            <jet-button type="button" :class="{ 'opacity-25': chatModalOpened }" :disabled="chatModalOpened"
                        @click="openModal">
                Create
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
import JetButton from '@/Components/Button';
import JetSuccessButton from '@/Components/SuccessButton';
import JetDangerButton from '@/Components/DangerButton';
import JetFormSection from '@/Components/FormSection';
import JetInput from '@/Components/Input';
import JetInputError from '@/Components/InputError';
import JetLabel from '@/Components/Label';
import JetDialogModal from '@/Components/DialogModal';
import ChatList from './ChatList';

export default {
    components: {
        JetButton,
        JetSuccessButton,
        JetDangerButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetDialogModal,
        ChatList,
    },

    props: [
        'bot'
    ],

    data() {
        return {
            chatModalOpened: false,
            chat: null,
            isFormSaved: true,
            form: this.$inertia.form({
                real_chat_id: '',
                name: '',
            }),
        };
    },

    methods: {
        openModal(chat = null) {
            this.chat = chat;
            this.chatModalOpened = true;
            this.form.real_chat_id = chat.real_chat_id;
            this.form.name = chat.name;
        },

        closeModal() {
            this.chat = null;
            this.chatModalOpened = false;
        },

        setIsFormSaved(type = true) {
            this.isFormSaved = type;
        },

        chatForm() {
            if (this.chat?.id) {
                this.form.put(route('bots.chats.update', [this.$props.bot, this.chat]), {
                    errorBag: 'updateChat',
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                });

            } else {
                this.form.post(route('bots.chats.store', this.$props.bot), {
                    errorBag: 'storeChat',
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                });
            }
            this.setIsFormSaved();
        },

        deleteChat() {
            this.form.delete(route('bots.chats.destroy', [this.$props.bot, this.chat]), {
                errorBag: 'deleteChat',
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
            });
        },

        callAction() {
            this.form.post(route('bots.chats.call', [this.$props.bot, this.chat]), {
                errorBag: 'callChat',
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
            });
        }
    },
};
</script>
