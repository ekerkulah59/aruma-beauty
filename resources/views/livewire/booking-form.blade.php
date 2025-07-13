<div class="max-w-2xl mx-auto p-6">
    <form wire:submit="submit" class="space-y-6">
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Service Selection -->
        <div>
            <label for="service" class="block text-sm font-medium text-gray-700">Select Service</label>
            <select wire:model.live="selectedService" id="service" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select a service</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }} - ${{ number_format($service->price, 2) }} ({{ $service->duration }} minutes)</option>
                @endforeach
            </select>
            @error('selectedService') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Date Selection -->
        <div>
            <label for="date" class="block text-sm font-medium text-gray-700">Select Date</label>
            <input type="date" wire:model.live="selectedDate" id="date" min="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('selectedDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Time Selection -->
        <div>
            <label for="time" class="block text-sm font-medium text-gray-700">Select Time</label>
            <select wire:model="selectedTime" id="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @if(empty($availableTimeSlots)) disabled @endif>
                <option value="">Select a time</option>
                @forelse($availableTimeSlots as $time)
                    <option value="{{ $time }}">{{ \Carbon\Carbon::parse($time)->format('g:i A') }}</option>
                @empty
                    <option value="" disabled>No available slots for this day</option>
                @endforelse
            </select>
            @error('selectedTime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Contact Information -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="tel" wire:model="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Notes -->
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes (Optional)</label>
            <textarea wire:model="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Book Appointment
            </button>
        </div>
    </form>
</div>
