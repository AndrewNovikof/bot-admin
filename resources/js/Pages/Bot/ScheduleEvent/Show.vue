<template>
  <jet-form-section @submitted="">
    <template #title>
      Schedule
    </template>

    <template #description>
      Mange schedule for sending messages in the bot's chats
    </template>

    <template #form>
      <schedule-event-list :scheduleEvents='bot.schedule_events' @editScheduleEvent="openModal"/>
      <jet-dialog-modal :show="scheduleEventModalOpened" @close="closeModal">
        <template #title>
          {{ scheduleEvent.id ? 'Update schedule' : 'Create schedule' }}
        </template>

        <template #content>
          <div v-if="scheduleEvent.id" class="col-span-6 font-medium text-sm text-gray-700 mb-3">
            <span class="pr-4">Status</span><span class="block"
                                                  v-bind:class="{ 'text-green-500': scheduleEvent.is_available, 'text-red-500': !scheduleEvent.is_available }">{{
              scheduleEvent.is_available ? 'Active' : 'Inactive'
            }}</span>
          </div>

          <div v-if="scheduleEvent.id && !scheduleEvent.is_available"
               class="col-span-6 font-medium text-sm text-gray-700 col-span-6 mb-3">
            <span class="pr-4">Last Error Message</span><span
              class="block text-gray-500">{{ scheduleEvent.last_error_message }}</span>
          </div>

          <div class="col-span-6 sm:col-span-4 mb-3">
            <jet-label for="is_enabled" value="Enabled"/>
            <input id="is_enabled" @change="setIsFormSaved(false)" type="checkbox"
                       class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none"
                       v-model="form.is_enabled"/>
            <jet-input-error :message="form.errors.is_enabled" class="mt-2"/>
          </div>

          <div v-if="scheduleEvent.id" class="col-span-6 sm:col-span-4 mb-3">
            <jet-label for="cron_expression" value="Cron Expression"/>
            <jet-input id="cron_expression" :disabled="form.cron_expression === null" type="text"
                       @change="handleChangeExpression"
                       class="mt-1 block w-full" v-model="form.cron_expression"/>
            <jet-input-error :message="form.errors.cron_expression" class="mt-2"/>
          </div>

          <div class="col-span-6 sm:col-span-4 mb-3">
            <jet-label for="name" value="Name"/>
            <jet-input id="name" @change="setIsFormSaved(false)" type="text" class="mt-1 block w-full"
                       v-model="form.name" autofocus/>
            <jet-input-error :message="form.errors.name" class="mt-2"/>
          </div>

          <div class="col-span-6 sm:col-span-4 mb-3">
            <label for="chat_id" class="block text-sm font-medium text-gray-700">Chat</label>
            <select id="chat_id" @change="setIsFormSaved(false)" v-model="form.chat_id" name="chat_id"
                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full">
              <option v-for="chat in bot.chats" :value="chat.id" :disabled="chat.is_available !== true"
                      :key="chat.id">{{ chat.name }}
              </option>
            </select>
            <jet-input-error :message="form.errors.chat_id" class="mt-2"/>
          </div>

          <div class="col-span-6 sm:col-span-4 mb-3">
            <label for="message_id" class="block text-sm font-medium text-gray-700">Message</label>
            <select id="message_id" @change="setIsFormSaved(false)" v-model="form.message_id"
                    name="message_id"
                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full">
              <option v-for="message in bot.messages" :value="message.id" :key="message.id">
                {{ message.name }}
              </option>
            </select>
            <jet-input-error :message="form.errors.message_id" class="mt-2"/>
          </div>

          <div class="col-span-6 sm:col-span-4 mb-3">
            <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
            <select id="timezone" @change="setIsFormSaved(false)" v-model="form.settings.timezone.values[0]"
                    name="timezone"
                    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full">
              <option v-for="timezone in timezones" :value="timezone.tzCode" :key="timezone.tzCode">
                {{ timezone.label }}
              </option>
            </select>
          </div>

          <label class="block text-sm font-medium text-gray-700">Settings</label>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.monthlyOn.checked" name="monthlyOn"
                     value="true"
                     @change="handleCheckMonths('monthlyOn')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every month on the <span
                  class="text-red-700">{{
                  form.settings.monthlyOn.values[0] ? form.settings.monthlyOn.values[0] + 'th' : '1th'
                }}</span> at <span
                  class="text-red-700">{{ form.settings.monthlyOn.values[1] || '00:00' }}</span></span>
              <div>
                <jet-input id="monthlyOnValue1" @change="emptyCronExpression"
                           type="number" min="0" max="31"
                           class="mt-1 block w-full disabled:opacity-20"
                           :disabled="form.settings.monthlyOn.checked !== true"
                           v-model="form.settings.monthlyOn.values[0]"/>
                <jet-input id="monthlyOnValue2" @change="emptyCronExpression"
                           type="text" class="mt-1 block w-full disabled:opacity-20"
                           :disabled="form.settings.monthlyOn.checked !== true"
                           v-model="form.settings.monthlyOn.values[1]"/>
              </div>
            </label>
            <jet-input-error :message="form.errors['settings.monthlyOn.checked']" class="mt-2"/>
          </div>


          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.monthly.checked" name="monthly" value="true"
                     @change="handleCheckMonths('monthly')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message on the first day of every month at 00:00</span>
            </label>
            <jet-input-error :message="form.errors['settings.monthly.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.weeklyOn.checked" name="weeklyOn" value="true"
                     @change="handleCheckWeeks('weeklyOn')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every week on <span
                  class="text-red-700">{{
                  getWeekDay(form.settings.weeklyOn.values[0]) || '00:00'
                }}</span> at <span
                  class="text-red-700">{{ form.settings.weeklyOn.values[1] || '00:00' }}</span></span>
              <div>
                <jet-input id="weeklyOnValue1" @change="emptyCronExpression"
                           type="number" min="0" max="6"
                           class="mt-1 block w-full disabled:opacity-20"
                           :disabled="form.settings.weeklyOn.checked !== true"
                           v-model="form.settings.weeklyOn.values[0]"/>
                <jet-input id="weeklyOnValue2" @change="emptyCronExpression"
                           type="text" class="mt-1 block w-full disabled:opacity-20"
                           :disabled="form.settings.weeklyOn.checked !== true"
                           v-model="form.settings.weeklyOn.values[1]"/>
              </div>
            </label>
            <jet-input-error :message="form.errors['settings.weeklyOn.checked']" class="mt-2"/>
          </div>


          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.weekly.checked" name="weekly" value="true"
                     @change="handleCheckWeeks('weekly')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message weekly at midnight</span>
            </label>
            <jet-input-error :message="form.errors['settings.weekly.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.twiceDaily.checked" name="twiceDaily"
                     value="true"
                     @change="handleCheckDays('twiceDaily')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every day at <span
                  class="text-red-700">{{
                  form.settings.twiceDaily.values[0] ? form.settings.twiceDaily.values[0] + ':00' : '00:00'
                }}</span> & <span
                  class="text-red-700">{{
                  form.settings.twiceDaily.values[1] ? form.settings.twiceDaily.values[1] + ':00' : '00:00'
                }}</span></span>
              <div>
                <jet-input id="twiceDailyValue1" @change="emptyCronExpression"
                           type="number" min="1" max="24"
                           class="mt-1 block w-full disabled:opacity-20"
                           :disabled="form.settings.twiceDaily.checked !== true"
                           v-model="form.settings.twiceDaily.values[0]"/>
                <jet-input id="twiceDailyValue2" @change="emptyCronExpression"
                           type="text" min="1" max="24"
                           class="mt-1 block w-full disabled:opacity-20"
                           :disabled="form.settings.twiceDaily.checked !== true"
                           v-model="form.settings.twiceDaily.values[1]"/>
              </div>
            </label>
            <jet-input-error :message="form.errors['settings.twiceDaily.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.dailyAt.checked" name="dailyAt" value="true"
                     @change="handleCheckDays('dailyAt')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every day at <span
                  class="text-red-700">{{ form.settings.dailyAt.values[0] || '00:00' }}</span></span>
              <jet-input id="dailyAt" @change="emptyCronExpression"
                         type="text" class="mt-1 block w-full disabled:opacity-20"
                         :disabled="form.settings.dailyAt.checked !== true"
                         v-model="form.settings.dailyAt.values[0]"/>
            </label>
            <jet-input-error :message="form.errors['settings.dailyAt.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.daily.checked" name="daily" value="true"
                     @change="handleCheckDays('daily')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message daily at midnight</span>
            </label>
            <jet-input-error :message="form.errors['settings.daily.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.everySixHours.checked" name="everySixHours"
                     @change="handleCheckHours('everySixHours')" value="true"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every six hours</span>
            </label>
            <jet-input-error :message="form.errors['settings.everySixHours.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.everyFourHours.checked" name="everyFourHours"
                     @change="handleCheckHours('everyFourHours')" value="true"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every four hours</span>
            </label>
            <jet-input-error :message="form.errors['settings.everyFourHours.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.everyThreeHours.checked"
                     name="everyThreeHours" @change="handleCheckHours('everyThreeHours')" value="true"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every three hours</span>
            </label>
            <jet-input-error :message="form.errors['settings.everyThreeHours.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.everyTwoHours.checked" name="everyTwoHours"
                     @change="handleCheckHours('everyTwoHours')" value="true"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every two hours</span>
            </label>
            <jet-input-error :message="form.errors['settings.everyTwoHours.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.hourlyAt.checked" name="hourlyAt"
                     @change="handleCheckMinutes('hourlyAt')" value="true"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every hour at <span
                  class="text-red-700">{{ form.settings.hourlyAt.values[0] || 0 }}</span> minutes past the hour</span>
              <jet-input id="hourlyAtValue" type="number" max="60" min="0"
                         @change="emptyCronExpression"
                         class="mt-1 block w-full disabled:opacity-20"
                         :disabled="form.settings.hourlyAt.checked !== true"
                         v-model="form.settings.hourlyAt.values[0]"/>
            </label>
            <jet-input-error :message="form.errors['settings.hourlyAt.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.hourly.checked" name="hourly"
                     @change="handleCheckMinutes('hourly')" value="true"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message hourly</span>
            </label>
            <jet-input-error :message="form.errors['settings.hourly.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.everyThirtyMinutes.checked"
                     name="everyThirtyMinutes" @change="handleCheckMinutes('everyThirtyMinutes')"
                     value="true"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every thirty minutes</span>
            </label>
            <jet-input-error :message="form.errors['settings.everyThirtyMinutes.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.everyFifteenMinutes.checked"
                     name="everyFifteenMinutes" @change="handleCheckMinutes('everyFifteenMinutes')"
                     value="true"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every fifteen minutes</span>
            </label>
            <jet-input-error :message="form.errors['settings.everyFifteenMinutes.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.everyTenMinutes.checked"
                     name="everyTenMinutes" value="true" @change="handleCheckMinutes('everyTenMinutes')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every ten minutes</span>
            </label>
            <jet-input-error :message="form.errors['settings.everyTenMinutes.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.everyFiveMinutes.checked"
                     name="everyFiveMinutes" value="true" @change="handleCheckMinutes('everyFiveMinutes')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Send the message every five minutes</span>
            </label>
            <jet-input-error :message="form.errors['settings.everyFiveMinutes.checked']" class="mt-2"/>
          </div>

          <label class="block text-sm font-medium text-gray-700">Limits</label>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.weekdays.checked" name="weekdays" value="true"
                     @change="handleCheckLimits('weekdays')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Limit sending the message to weekdays</span>
            </label>
            <jet-input-error :message="form.errors['settings.weekdays.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.weekends.checked" name="weekends" value="true"
                     @change="handleCheckLimits('weekends')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Limit sending the message to weekends</span>
            </label>
            <jet-input-error :message="form.errors['settings.weekends.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.sundays.checked" name="sundays" value="true"
                     @change="handleCheckLimits('sundays')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Limit sending the message to Sunday</span>
            </label>
            <jet-input-error :message="form.errors['settings.sundays.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.mondays.checked" name="mondays" value="true"
                     @change="handleCheckLimits('mondays')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Limit sending the message to Monday</span>
            </label>
            <jet-input-error :message="form.errors['settings.mondays.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.tuesdays.checked" name="tuesdays" value="true"
                     @change="handleCheckLimits('tuesdays')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Limit sending the message to Tuesday</span>
            </label>
            <jet-input-error :message="form.errors['settings.tuesdays.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.wednesdays.checked" name="wednesdays"
                     value="true"
                     @change="handleCheckLimits('wednesdays')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Limit sending the message to Wednesday</span>
            </label>
            <jet-input-error :message="form.errors['settings.wednesdays.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.thursdays.checked" name="thursdays"
                     value="true"
                     @change="handleCheckLimits('thursdays')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Limit sending the message to Thursday</span>
            </label>
            <jet-input-error :message="form.errors['settings.thursdays.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.fridays.checked" name="fridays" value="true"
                     @change="handleCheckLimits('fridays')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Limit sending the message to Friday</span>
            </label>
            <jet-input-error :message="form.errors['settings.fridays.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.saturdays.checked" name="saturdays"
                     value="true"
                     @change="handleCheckLimits('saturdays')"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Limit sending the message to Saturday</span>
            </label>
            <jet-input-error :message="form.errors['settings.saturdays.checked']" class="mt-2"/>
          </div>

          <div class="p-4 mx-auto bg-white rounded-xl shadow-md mb-3" v-if="form.settings.frequency">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="form.settings.frequency.checked" name="frequency"
                     @change="setIsFormSaved(false)" value="true"
                     class="form-tick appearance-none h-6 w-6 border border-gray-300 rounded-md checked:bg-blue-600 checked:border-transparent focus:outline-none">
              <span class="text-gray-900 font-medium">Limit sending the message only every <span
                  class="text-red-700">{{
                  form.settings.frequency.values[0] || 0
                }}-th</span> time. <span v-if="form.settings.frequency.values[1] > 0 && !form.cron_expression">Next sending will be after next <span
                  class="text-red-700">{{
                  form.settings.frequency.values[1]
                }}</span> times</span>
                                <span v-if="form.settings.frequency.values[1] <= 0 || form.cron_expression">Next sending will be next time</span>
                            </span>
              <div>
                <jet-input id="frequencyValue1" type="number" max="60" min="1"
                           class="mt-1 block w-full disabled:opacity-20"
                           :disabled="form.settings.frequency.checked !== true"
                           v-model="form.settings.frequency.values[0]"/>
                <jet-input id="frequencyValue2" type="number" max="60" min="0"
                           class="mt-1 block w-full disabled:opacity-20"
                           :disabled="form.settings.frequency.checked !== true"
                           v-model="form.settings.frequency.values[1]"/>
              </div>
            </label>
            <jet-input-error :message="form.errors['settings.frequency.checked']" class="mt-2"/>
          </div>
        </template>

        <template #footer>
          <jet-secondary-button @click="closeModal" class="mb-2">
            Cancel
          </jet-secondary-button>

          <jet-danger-button v-if="scheduleEvent.id" class="ml-2 mb-2" @click="deleteScheduleEvent"
                             :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Delete
          </jet-danger-button>

          <jet-success-button v-if="scheduleEvent.id" class="ml-2 mb-2" @click="callScheduleEvent"
                              :class="{ 'opacity-25': form.processing }"
                              :disabled="form.processing || !isFormSaved">
            Test
          </jet-success-button>

          <jet-button type="button" class="ml-2 mb-2" :class="{ 'opacity-25': form.processing }"
                      :disabled="form.processing" @click="scheduleEventForm">
            Save
          </jet-button>
        </template>
      </jet-dialog-modal>
    </template>

    <template #actions>
      <jet-button type="button" :class="{ 'opacity-25': scheduleEventModalOpened }"
                  :disabled="scheduleEventModalOpened"
                  @click="openModal">
        Create
      </jet-button>
    </template>
  </jet-form-section>
</template>

<script>
import JetButton from '@/Components/Button';
import JetDangerButton from '@/Components/DangerButton';
import JetSuccessButton from '@/Components/SuccessButton';
import JetFormSection from '@/Components/FormSection';
import JetInput from '@/Components/Input';
import JetInputError from '@/Components/InputError';
import JetLabel from '@/Components/Label';
import JetDialogModal from '@/Components/DialogModal';
import ScheduleEventList from './ScheduleEventList';
import timezones from './timezones.json';

const default_settings = {
  everyFiveMinutes: {
    checked: false
  },
  everyTenMinutes: {
    checked: false
  },
  everyFifteenMinutes: {
    checked: false
  },
  everyThirtyMinutes: {
    checked: false
  },
  hourly: {
    checked: false
  },
  hourlyAt: {
    checked: false,
    values: [15]
  },
  everyTwoHours: {
    checked: false
  },
  everyThreeHours: {
    checked: false
  },
  everyFourHours: {
    checked: false
  },
  everySixHours: {
    checked: false
  },
  daily: {
    checked: false
  },
  dailyAt: {
    checked: false,
    values: ['14:00']
  },
  twiceDaily: {
    checked: false,
    values: [2, 6]
  },
  weekly: {
    checked: false
  },
  weeklyOn: {
    checked: false,
    values: ['3', '12:23']
  },
  monthly: {
    checked: false
  },
  monthlyOn: {
    checked: false,
    values: ['3', '16:25']
  },
  timezone: {
    values: ['Europe/Moscow']
  },
  weekdays: {
    checked: false
  },
  weekends: {
    checked: false
  },
  sundays: {
    checked: false
  },
  mondays: {
    checked: false
  },
  tuesdays: {
    checked: false
  },
  wednesdays: {
    checked: false
  },
  thursdays: {
    checked: false
  },
  fridays: {
    checked: false
  },
  saturdays: {
    checked: false
  },
  frequency: {
    checked: false,
    values: [1, 1]
  }
};

export default {
  components: {
    JetButton,
    JetDangerButton,
    JetSuccessButton,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel,
    JetDialogModal,
    ScheduleEventList,
  },

  props: [
    'bot'
  ],

  data() {
    return {
      scheduleEventModalOpened: false,
      scheduleEvent: null,
      timezones: timezones,
      isFormSaved: true,
      form: this.$inertia.form({
        cron_expression: null,
        is_enabled: true,
        name: '',
        chat_id: '',
        message_id: '',
        settings: default_settings
      }),
    };
  },

  methods: {
    openModal(scheduleEvent = null) {
      this.scheduleEvent = scheduleEvent;
      this.scheduleEventModalOpened = true;
      this.form.name = scheduleEvent.name;
      this.form.chat_id = scheduleEvent.chat_id;
      this.form.is_enabled = scheduleEvent.is_enabled;
      this.form.message_id = scheduleEvent.message_id;
      this.form.cron_expression = scheduleEvent.cron_expression;
      this.form.next_due_date = scheduleEvent.next_due_date;
      this.form.settings = scheduleEvent.settings || default_settings;
    },

    closeModal() {
      this.scheduleEvent = null;
      this.scheduleEventModalOpened = false;
    },

    scheduleEventForm() {
      if (this.scheduleEvent?.id) {
        this.form.put(route('bots.scheduleEvents.update', [this.$props.bot, this.scheduleEvent]), {
          errorBag: 'updateMessage',
          preserveScroll: true,
          onSuccess: () => this.closeModal(),
        });

      } else {
        this.form.post(route('bots.scheduleEvents.store', this.$props.bot), {
          errorBag: 'storeMessage',
          preserveScroll: true,
          onSuccess: () => this.closeModal(),
        });
      }
      this.isFormSaved = true;
    },

    deleteScheduleEvent() {
      this.form.delete(route('bots.scheduleEvents.destroy', [this.$props.bot, this.scheduleEvent]), {
        errorBag: 'deleteScheduleEvent',
        preserveScroll: true,
        onSuccess: () => this.closeModal(),
      });
    },

    callScheduleEvent() {
      this.form.post(route('bots.scheduleEvents.call', [this.$props.bot, this.scheduleEvent]), {
        errorBag: 'callScheduleEvent',
        preserveScroll: true,
        onSuccess: () => this.closeModal(),
      });
    },

    handleCheckMinutes(type = 'everyFiveMinutes') {
      if (type !== 'everyFiveMinutes') this.form.settings.everyFiveMinutes.checked = false;
      if (type !== 'everyTenMinutes') this.form.settings.everyTenMinutes.checked = false;
      if (type !== 'everyFifteenMinutes') this.form.settings.everyFifteenMinutes.checked = false;
      if (type !== 'everyThirtyMinutes') this.form.settings.everyThirtyMinutes.checked = false;
      if (type !== 'hourly') this.form.settings.hourly.checked = false;
      if (type !== 'hourlyAt') this.form.settings.hourlyAt.checked = false;
      this.emptyCronExpression();
    },

    handleCheckHours(type = 'everyTwoHours') {
      if (type !== 'everyTwoHours') this.form.settings.everyTwoHours.checked = false;
      if (type !== 'everyFourHours') this.form.settings.everyFourHours.checked = false;
      if (type !== 'everyThreeHours') this.form.settings.everyThreeHours.checked = false;
      if (type !== 'everySixHours') this.form.settings.everySixHours.checked = false;
      this.emptyCronExpression();
    },

    handleCheckDays(type = 'daily') {
      if (type !== 'daily') this.form.settings.daily.checked = false;
      if (type !== 'dailyAt') this.form.settings.dailyAt.checked = false;
      if (type !== 'twiceDaily') this.form.settings.twiceDaily.checked = false;
      this.emptyCronExpression();
    },

    handleCheckWeeks(type = 'weekly') {
      if (type !== 'weekly') this.form.settings.weekly.checked = false;
      if (type !== 'weeklyOn') this.form.settings.weeklyOn.checked = false;
      this.emptyCronExpression();
    },

    handleCheckMonths(type = 'monthly') {
      if (type !== 'monthly') this.form.settings.monthly.checked = false;
      if (type !== 'monthlyOn') this.form.settings.monthlyOn.checked = false;
      this.emptyCronExpression();
    },

    handleCheckLimits(type = 'weekdays') {
      if (type !== 'weekdays') this.form.settings.weekdays.checked = false;
      if (type !== 'weekends') this.form.settings.weekends.checked = false;
      if (type !== 'sundays') this.form.settings.sundays.checked = false;
      if (type !== 'mondays') this.form.settings.mondays.checked = false;
      if (type !== 'tuesdays') this.form.settings.tuesdays.checked = false;
      if (type !== 'wednesdays') this.form.settings.wednesdays.checked = false;
      if (type !== 'thursdays') this.form.settings.thursdays.checked = false;
      if (type !== 'thursdays') this.form.settings.thursdays.checked = false;
      if (type !== 'fridays') this.form.settings.fridays.checked = false;
      if (type !== 'saturdays') this.form.settings.saturdays.checked = false;
      this.emptyCronExpression();
    },

    getWeekDay(type = '0') {
      if (type === '0') return 'Sunday';
      if (type === '1') return 'Monday';
      if (type === '2') return 'Tuesday';
      if (type === '3') return 'Wednesday';
      if (type === '4') return 'Thursday';
      if (type === '5') return 'Friday';
      if (type === '6') return 'Saturday';
      return 'Sunday';
    },

    emptyCronExpression() {
      this.form.cron_expression = null;
      this.setIsFormSaved(false);
    },

    setIsFormSaved(type = true) {
      this.isFormSaved = type;
    },

    handleChangeExpression() {
      this.form.settings = default_settings;
      this.setIsFormSaved(false);
    }
  },
};
</script>
