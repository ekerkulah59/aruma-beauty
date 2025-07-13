<div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl mx-auto">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Booking Confirmed!</h2>
        <p class="text-gray-600">Thank you for booking with Aruma Beauty</p>
    </div>

    <div class="border-t border-gray-200 pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Details</h3>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Service</p>
                    <p class="font-medium text-gray-900">{{ $booking->service->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Date</p>
                    <p class="font-medium text-gray-900">{{ $booking->booking_date->format('F j, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Time</p>
                    <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($booking->booking_time)->format('g:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Duration</p>
                    <p class="font-medium text-gray-900">{{ $booking->service->duration }} minutes</p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Contact Information</h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-medium text-gray-900">{{ $booking->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium text-gray-900">{{ $booking->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-medium text-gray-900">{{ $booking->phone }}</p>
                    </div>
                </div>
            </div>

            @if($booking->notes)
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Additional Notes</h4>
                <p class="text-gray-600">{{ $booking->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-8 flex justify-center space-x-4">
        <button wire:click="$set('showRescheduleModal', true)" class="inline-flex items-center px-4 py-2 border border-[#d6c7b0] text-sm font-medium rounded-md text-[#d6c7b0] bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d6c7b0]">
            Reschedule
        </button>
        <button wire:click="cancel" class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Cancel
        </button>
        <a href="{{ route('book') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#d6c7b0] hover:bg-[#cbb89e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d6c7b0]">
            Book Another
        </a>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Print
        </button>
    </div>

    <!-- Reschedule Modal -->
    @if($showRescheduleModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reschedule Appointment</h3>

            <div class="space-y-4">
                <div>
                    <label for="newDate" class="block text-sm font-medium text-gray-700">New Date</label>
                    <input type="date" wire:model="newDate" id="newDate" min="{{ date('Y-m-d', strtotime('tomorrow')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d6c7b0] focus:ring focus:ring-[#d6c7b0] focus:ring-opacity-50">
                    @error('newDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="newTime" class="block text-sm font-medium text-gray-700">New Time</label>
                    <select wire:model="newTime" id="newTime" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#d6c7b0] focus:ring focus:ring-[#d6c7b0] focus:ring-opacity-50">
                        <option value="">Choose a time...</option>
                        @foreach($availableTimeSlots as $slot)
                            <option value="{{ $slot }}">{{ \Carbon\Carbon::parse($slot)->format('g:i A') }}</option>
                        @endforeach
                    </select>
                    @error('newTime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button wire:click="$set('showRescheduleModal', false)" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </button>
                <button wire:click="reschedule" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#d6c7b0] hover:bg-[#cbb89e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#d6c7b0]">
                    Confirm Reschedule
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
