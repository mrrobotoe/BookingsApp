<x-app-layout>
    <form
        x-on:submit.prevent="submit"
        x-data="{
            error: null,
            form: {
                employee_id: {{ $employee->id }},
                service_id: {{ $service->id }},
                date: null,
                time: null,
                name: null,
                email: null
            },

            submit() {
                axios.post('{{ route('appointments') }}', this.form).then((response) => {
                    window.location = response.data.redirect;
                }).catch((error) => {
                    this.error = error.response.data.error;
                });
            }
        }"

        class="space-y-12"
    >
        <div>
            <a href="{{ route('bookings.employee', $employee) }}" class="text-xs text-blue-500 hover:underline hover:underline-offset-2">&larr; Go back</a>
            <h2 class="text-xl font-medium mt-4">Here's what you're booking</h2>
            <div class="flex mt-6 space-x-3 bg-slate-100 rounded-lg p-4">
                <img src="{{ $employee->profile_photo_url }}" alt="" class="rounded-lg size-14 bg-slate-100">
                <div class="w-full">
                    <div class="flex justify-between">
                        <div class="font-semibold">
                            {{ $service->title }} ({{ $service->duration }} minutes)
                        </div>
                        <div class="text-sm">
                            {{ $service->price }}
                        </div>
                    </div>
                    <div class="text-sm">
                        {{ $employee->name }}
                    </div>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-medium mt-4">1. When for?</h2>

            <div
                x-data="{
                    picker: null,
                    availableDates: {{ json_encode($availableDates) }}
                }"
                x-init="
                    this.picker = new easepick.create({
                        element: $refs.date,
                        readonly: true,
                        zIndex: 1000,
                        date: '{{ $firstAvailableDate }}',
                        css: [
                            'https://cdn.jsdelivr.net/npm/@easepick/bundle@1.2.1/dist/index.css',
                            '/vendor/easepick/easepick.css'
                        ],
                        plugins: [
                            'LockPlugin'
                        ],
                        LockPlugin: {
                            minDate: new Date(),
                            filter (date, picked) {
                                return !Object.keys(availableDates).includes(date.format('YYYY-MM-DD'))
                            }
                        },
                        setup (picker) {
                            picker.on('view', (e) => {
                                const { view, date, target } = e.detail
                                const dateString = date ? date.format('YYYY-MM-DD') : null
                                if (view === 'CalendarDay' && availableDates[dateString]) {
                                    const span = target.querySelector('.day-slots') || document.createElement('span')
                                    span.className = 'day-slots'
                                    span.innerHTML = pluralize('slot', availableDates[dateString], true)
                                    target.append(span);
                                }
                            })
                        }
                    })
                    this.picker.on('select', (e) => {
                        form.date = new easepick.DateTime(e.detail.date).format('YYYY-MM-DD')
                        $dispatch('slots-requested')
                    })
                    $nextTick(() => {
                        this.picker.trigger('select', { date: '{{ $firstAvailableDate }}' });
                    })
                "
                class=""
            >
                <input x-ref="date" class="mt-6 text-sm bg-slate-100 border-0 rounded-lg px-6 py-4 w-full" placeholder="Select a date...">
            </div>
        </div>

        <div
            x-data="{
                slots: [],
                fetchSlots (event) {
                    console.log('Fetching slots for date:', form.date)
                    axios.get(`{{ route('slots', [$employee, $service]) }}?date=${form.date}`).then((response) => {
                        this.slots = response.data.times
                    })
                }
            }"
            x-on:slots-requested.window="fetchSlots(event)"
        >
            <h2 class="text-lg font-medium mt-4">2. Choose a time slot</h2>
            <div class="mt-6" x-show="slots.length" x-cloak>
                <div class="grid grid-cols-3 md:grid-cols-5 gap-8 mt-7">
                    <template x-for="slot in slots">
                        <div
                            x-text="slot"
                            x-on:click="form.time = slot"
                            x-bind:class="{ 'bg-slate-100 hover:bg-slate-100 border-slate-100': slot === form.time }"
                            class="py-3 px-4 text-sm border border-slate-200 rounded-lg flex flex-col items-center justify-between hover:bg-gray-50/75 cursor-pointer"
                        />
                    </template>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-medium mt-4">3. Your details and book</h2>
            <div x-text="error" x-show="error" x-cloak class="bg-slate-900 text-white py-4 px-6 rounded-lg mt-3"></div>
            <div class="mt-6" x-show="form.time" x-cloak>
                <div>
                    <label for="name" class="sr-only">Your name</label>
                    <input
                        type="text"
                        class="mt-6 text-sm bg-slate-100 border-0 rounded-lg px-6 py-4 w-full"
                        name="name"
                        id="name"
                        placeholder="Your name"
                        required
                        x-model="form.name"
                    >
                </div>

                <div class="mt-3">
                    <label for="email" class="sr-only">Your email</label>
                    <input
                        type="email"
                        class="mt-6 text-sm bg-slate-100 border-0 rounded-lg px-6 py-4 w-full"
                        name="email"
                        id="email"
                        placeholder="Your email address"
                        required
                        x-model="form.email"
                    >
                </div>

                <button
                    type="submit"
                    class="mt-6 py-3 px-6 text-sm border border-slate-200 rounded-lg flex flex-col item-center justify-center text-center hover:bg-slate-900 cursor-pointer bg-slate-800 text-white font-medium"
                >
                    Make Booking
                </button>
            </div>
        </div>
    </form>
</x-app-layout>
