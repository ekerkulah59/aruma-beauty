<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
            <div class="flex items-center">
                <h2 class="text-2xl font-bold text-gray-900">Booking Calendar</h2>
            </div>
            <div class="flex gap-3">
                <button wire:click="$dispatch('booking-created')"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200 text-sm font-medium">
                    Refresh
                </button>
                <button x-data @click="$dispatch('open-modal', 'booking-form-modal')"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200 text-sm font-medium">
                    New Booking
                </button>
            </div>
        </div>

        <div wire:ignore id="calendar" class="min-h-[600px] text-gray-900 calendar-text-fix" style="color: #000000 !important;"></div>
    </div>

    <!-- Booking Form Modal -->
    <div x-data="{ show: false }"
         @open-modal.window="if ($event.detail === 'booking-form-modal') show = true"
         @close-booking-form.window="show = false"
         x-show="show"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl p-6 relative" @click.away="show = false">
            <button @click="show = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                &times;
            </button>
            <livewire:booking-form />
        </div>
    </div>

    <!-- Admin Booking Details Modal -->
    @auth
        @if(auth()->user()->is_admin)
            <livewire:admin-booking-details-modal />
        @endif
    @endauth

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('livewire:initialized', function () {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                timeZone: 'America/New_York',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                slotMinTime: '09:00:00',
                slotMaxTime: '20:00:00',
                allDaySlot: false,
                events: function(fetchInfo, successCallback, failureCallback) {
                    @this.call('getEvents').then(events => {
                        console.log('Fetched events:', events);
                        successCallback(events);
                    }).catch(error => {
                        console.error('Error fetching events:', error);
                        failureCallback(error);
                    });
                },
                eventClick: function(info) {
                    @this.call('eventClick', info.event.toPlainObject());
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: true
                },
                height: 'auto',
                expandRows: true,
                nowIndicator: true,
                dayMaxEvents: true,
            });
            calendar.render();

            // Force text visibility after render
            setTimeout(() => {
                const calendarEl = document.getElementById('calendar');
                if (calendarEl) {
                    // Apply black text to all elements
                    const allElements = calendarEl.querySelectorAll('*');
                    allElements.forEach(el => {
                        el.style.color = '#000000';
                        el.style.setProperty('color', '#000000', 'important');
                    });
                }
            }, 100);

            // Force modal text visibility
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1) { // Element node
                                if (node.classList && (node.classList.contains('fixed') || node.classList.contains('modal'))) {
                                    // Force all text in modals to be black
                                    const modalElements = node.querySelectorAll('*');
                                    modalElements.forEach(el => {
                                        el.style.color = '#000000';
                                        el.style.setProperty('color', '#000000', 'important');
                                    });
                                }
                            }
                        });
                    }
                });
            });
            observer.observe(document.body, { childList: true, subtree: true });

            @this.on('refreshCalendar', () => {
                console.log('Refreshing calendar events...');
                calendar.refetchEvents();
            });

            // Handle unauthorized access
            @this.on('unauthorized-access', () => {
                alert('You are not authorized to perform this action.');
            });

            // Handle booking not found
            @this.on('booking-not-found', () => {
                alert('Booking not found.');
            });
        });
    </script>
    @endpush
</div>
