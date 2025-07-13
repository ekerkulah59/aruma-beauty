<x-app-layout>
    <x-banner :style="'success'" :message="' Contact us at 1-800-555-5555, or email us at 4g9n6@example.com to book or fill out our contact form.'" />

    <section class="bg-gray-100">
        <div class="relative">
            <img src="{{ asset('images/gg.avif') }}" alt="Hair Style Gallery" class="w-full h-96 object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <h1 class="text-white text-4xl md:text-6xl font-bold">HAIR STYLE GALLERY</h1>
            </div>
        </div>
    </section>
    <x-gallery-card />


</x-app-layout>
