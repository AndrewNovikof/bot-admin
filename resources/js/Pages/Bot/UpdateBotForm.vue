<template>
    <jet-form-section @submitted="updateBot">
        <template #title>
            Bot
        </template>

        <template #description>
            The bot's and owner information.
        </template>

        <template #form>
            <div class="col-span-6">
                <jet-label value="Bot Owner" />

                <div class="flex items-center mt-2" v-if="bot.user">
                    <img class="w-12 h-12 rounded-full object-cover" :src="bot.user.profile_photo_url" :alt="bot.user.name">

                    <div class="ml-4 leading-tight">
                        <div>{{ bot.user.name }}</div>
                        <div class="text-gray-700 text-sm">{{ bot.user.email }}</div>
                    </div>
                </div>
            </div>

            <template v-if="bot.id">
                <div class="col-span-6 font-medium text-sm text-gray-700">
                    <span class="pr-4">Status</span><span class="block" v-bind:class="{ 'text-green-500': bot.is_available, 'text-red-500': !bot.is_available }">{{ bot.is_available ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="col-span-6 font-medium text-sm text-gray-700 col-span-6" v-if="!bot.is_available">
                    <span class="pr-4">Last Error Message</span><span class="block text-gray-500">{{ bot.last_error_message }}</span>
                </div>
                <div class="col-span-6 font-medium text-sm text-gray-700">
                    <span class="pr-4">Team Name</span><span class="block text-gray-500">{{ bot.team.name }}</span>
                </div>
                <div class="col-span-6 font-medium text-sm text-gray-700">
                    <span class="pr-4">Real Id</span><span class="block text-gray-500">{{ bot.real_id }}</span>
                </div>
                <div class="col-span-6 font-medium text-sm text-gray-700">
                    <span class="pr-4">Real Frist Name</span><span class="block text-gray-500">{{ bot.real_first_name }}</span>
                </div>
                <div class="col-span-6 font-medium text-sm text-gray-700">
                    <span class="pr-4">Real User Name</span><span class="block text-gray-500">{{ bot.real_user_name }}</span>
                </div>
            </template>

            <div class="col-span-6">
                <jet-label for="name" value="Name" />

                <jet-input id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            :disabled="! permissions.canUpdateBot" />

                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <div class="col-span-6">
                <jet-label for="name" value="Token" />

                <jet-input id="token"
                           type="text"
                           class="mt-1 block w-full"
                           v-model="form.token"
                           :disabled="! permissions.canUpdateBot" />

                <jet-input-error :message="form.errors.token" class="mt-2" />
            </div>
        </template>

        <template #actions v-if="permissions.canUpdateBot">
            <jet-action-message v-if="form.recentlySuccessful" :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetActionMessage from '@/Components/ActionMessage'
    import JetButton from '@/Components/Button'
    import JetFormSection from '@/Components/FormSection'
    import JetInput from '@/Components/Input'
    import JetInputError from '@/Components/InputError'
    import JetLabel from '@/Components/Label'

    export default {
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
        },

        props: ['bot', 'permissions'],

        data() {
            return {
                form: this.$inertia.form({
                    name: this.bot.name,
                    token: this.bot.token,
                })
            }
        },

        methods: {
            updateBot() {
                this.form.put(route('bots.update', this.bot), {
                    errorBag: 'updateBot',
                    preserveScroll: true
                });
            },
        },
    }
</script>
