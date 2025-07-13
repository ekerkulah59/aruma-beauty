<div>
    <!-- Admin Booking Details Modal -->
    @if($showModal)
    <div x-data="{ showCancelConfirm: false }"
         x-show="true"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75"
         @keydown.escape.window="$wire.closeModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto admin-booking-modal"
                 @click.away="$wire.closeModal()"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <!-- Modal Header -->
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900" style="color: #000000 !important;">
                            @if($editMode)
                                Edit Appointment
                            @else
                                Appointment Details
                            @endif
                        </h3>
                        <button wire:click="closeModal"
                                type="button"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                @if($booking)
                <!-- Modal Content -->
                <div class="px-6 py-4" style="color: #000000 !important;">
                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                            <div class="text-sm text-red-600">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Success Messages -->
                    @if(session()->has('message'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-sm text-green-600">{{ session('message') }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column: Client & Service Details -->
                        <div class="space-y-6">
                            <!-- Client Information -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-800 mb-3" style="color: #000000 !important;">Client Information</h4>

                                @if($editMode)
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1" style="color: #000000 !important;">Name</label>
                                            <input type="text" wire:model="name"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1" style="color: #000000 !important;">Email</label>
                                            <input type="email" wire:model="email"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1" style="color: #000000 !important;">Phone</label>
                                            <input type="tel" wire:model="phone"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <p class="text-gray-900" style="color: #000000 !important;"><span class="font-medium text-gray-900" style="color: #000000 !important;">Name:</span> {{ $booking->name }}</p>
                                        <p class="text-gray-900" style="color: #000000 !important;"><span class="font-medium text-gray-900" style="color: #000000 !important;">Email:</span> {{ $booking->email }}</p>
                                        <p class="text-gray-900" style="color: #000000 !important;"><span class="font-medium text-gray-900" style="color: #000000 !important;">Phone:</span> {{ $booking->phone }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Service Information -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-800 mb-3" style="color: #000000 !important;">Service Details</h4>

                                @if($editMode)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                                        <select wire:model.live="service_id"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select a service</option>
                                            @foreach($availableServices as $service)
                                                <option value="{{ $service->id }}">
                                                    {{ $service->name }} - ${{ $service->price }} ({{ $service->duration }} min)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <p class="text-gray-900"><span class="font-medium text-gray-900">Service:</span> {{ $booking->service->name ?? 'N/A' }}</p>
                                        <p class="text-gray-900"><span class="font-medium text-gray-900">Duration:</span> {{ $booking->service->duration ?? 'N/A' }} minutes</p>
                                        <p class="text-gray-900"><span class="font-medium text-gray-900">Price:</span> ${{ $booking->service->price ?? 'N/A' }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Notes -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-800 mb-3">Notes</h4>

                                @if($editMode)
                                    <textarea wire:model="notes" rows="4"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Add any notes about this appointment..."></textarea>
                                @else
                                    <p class="text-gray-600">{{ $booking->notes ?: 'No notes added.' }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Right Column: Appointment & Status -->
                        <div class="space-y-6">
                            <!-- Appointment Information -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-800 mb-3">Appointment Time</h4>

                                @if($editMode)
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                            <input type="date" wire:model.live="booking_date"
                                                   min="{{ date('Y-m-d') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                            <select wire:model="booking_time"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Select a time</option>
                                                @foreach($availableTimeSlots as $slot)
                                                    <option value="{{ $slot->format('H:i:s') }}">
                                                        {{ $slot->format('g:i A') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <p><span class="font-medium">Date:</span> {{ $this->getBookingDateTimeAttribute()?->format('l, F j, Y') }}</p>
                                        <p><span class="font-medium">Time:</span> {{ $this->getBookingDateTimeAttribute()?->format('g:i A') }}</p>
                                        @if($this->getBookingEndTimeAttribute())
                                            <p><span class="font-medium">End Time:</span> {{ $this->getBookingEndTimeAttribute()->format('g:i A') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Status Management -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-800 mb-3">Status</h4>
                                @if($this->isCompleted)
                                    <div class="flex items-center mb-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            Status: Completed
                                        </span>
                                    </div>
                                @elseif($editMode)
                                    <select wire:model="status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @switch($booking->status)
                                                @case('pending')
                                                    bg-yellow-100 text-yellow-800
                                                    @break
                                                @case('confirmed')
                                                    bg-blue-100 text-blue-800
                                                    @break
                                                @case('completed')
                                                    bg-green-100 text-green-800
                                                    @break
                                                @case('cancelled')
                                                    bg-red-100 text-red-800
                                                    @break
                                                @case('rescheduled')
                                                    bg-purple-100 text-purple-800
                                                    @break
                                                @case('no_show')
                                                    bg-gray-100 text-gray-800
                                                    @break
                                                @default
                                                    bg-gray-100 text-gray-800
                                            @endswitch
                                        ">
                                            {{ $statusOptions[$booking->status] ?? ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                    <!-- Quick Status Update Buttons -->
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($statusOptions as $value => $label)
                                            @if($value !== $booking->status)
                                                <button wire:click="updateStatus('{{ $value }}')"
                                                        class="px-3 py-1 text-xs rounded border border-gray-300 hover:bg-gray-50 transition-colors">
                                                    {{ $label }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Booking ID and Created -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-md font-semibold text-gray-800 mb-3">Booking Info</h4>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <p><span class="font-medium">ID:</span> #{{ $booking->id }}</p>
                                    <p><span class="font-medium">Created:</span> {{ $booking->created_at->format('M j, Y g:i A') }}</p>
                                    @if($booking->updated_at != $booking->created_at)
                                        <p><span class="font-medium">Updated:</span> {{ $booking->updated_at->format('M j, Y g:i A') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 rounded-b-lg">
                    @if($editMode)
                        <!-- Edit Mode Actions -->
                        <div class="flex justify-between">
                            <div class="flex space-x-3">
                                <button wire:click="cancelEdit"
                                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                    Cancel
                                </button>
                            </div>
                            <div class="flex space-x-3">
                                <button wire:click="updateBooking"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    @elseif($this->isCompleted)
                        <div class="flex justify-end">
                            <button wire:click="closeModal"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                Close
                            </button>
                        </div>
                    @else
                        <!-- View Mode Actions -->
                        <div class="flex justify-between">
                            <div class="flex space-x-3">
                                <button wire:click="enableEditMode"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Edit Appointment
                                </button>
                            </div>
                            <div class="flex space-x-3">
                                <button @click="showCancelConfirm = true"
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                    Cancel Appointment
                                </button>
                                <button wire:click="closeModal"
                                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                    Close
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                @endif

                <!-- Cancellation Confirmation Modal -->
                <div x-show="showCancelConfirm"
                     x-transition
                     class="absolute inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center"
                     style="display: none;">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.away="showCancelConfirm = false">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Cancel Appointment</h4>
                        <p class="text-gray-600 mb-4">Are you sure you want to cancel this appointment? This action cannot be undone.</p>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason (Optional)</label>
                            <textarea wire:model="cancellation_reason" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                                      placeholder="Enter reason for cancellation..."></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button @click="showCancelConfirm = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                Keep Appointment
                            </button>
                            <button wire:click="cancelBooking"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                Cancel Appointment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
