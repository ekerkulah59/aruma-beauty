<x-app-layout>
    <!-- Hero Section -->
    <x-hero-section />

    <!-- Brief Philosophy Section -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-800 mb-6">Welcome to ARUMA Hair Salon</h2>
            <p class="text-lg md:text-xl text-gray-600 mb-8 leading-relaxed">
                At our black-owned salon in Delaware, we believe hair is an art form that expresses your true style.
                From relaxers and sew-ins to African hair braiding, our experienced stylists provide professional techniques
                and personalized care to ensure your hair is not only beautiful, but healthy.
            </p>
            <div class="mx-auto w-24 h-1 bg-[#d6c7b0] rounded"></div>
        </div>
    </section>

    <!-- Featured Services -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-800 mb-4">Our Featured Services</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Discover our most popular services designed to enhance your natural beauty
                </p>
            </div>

            <div class="grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
                <x-service-card
                    image="{{ asset('images/IMG_5456.png') }}"
                    title="Wash and Curl"
                    description="Shampoo, condition, and style for moisture and definition."
                    price="50"
                />
                <x-service-card
                    image="{{ asset('images/nmoultry.jpg') }}"
                    title="Partial Sew-in Weave"
                    description="Leave a portion of your natural hair out for the most natural look."
                    price="90"
                />
                <x-service-card
                    image="{{ asset('images/images.jpg') }}"
                    title="Moisturizing Treatment"
                    description="Moisturize and strengthen hair damaged by heat and chemicals."
                    price="50"
                />
                <x-service-card
                    image="{{ asset('images/image.webp') }}"
                    title="Protein Reconstructor"
                    description="Rebuilds and repairs stressed or chemically treated hair."
                    price="50"
                />
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('services') }}" class="inline-block px-8 py-3 bg-[#d6c7b0] text-gray-800 text-lg font-semibold rounded shadow hover:bg-[#cbb89e] transition uppercase">
                    View All Services
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-800 mb-4">Why Choose ARUMA Hair Salon</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Experience the difference that professional expertise and personalized care can make
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-[#d6c7b0] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Expert Stylists</h3>
                    <p class="text-gray-600">Our certified professionals bring years of experience and ongoing training to every service.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-[#d6c7b0] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Personalized Care</h3>
                    <p class="text-gray-600">Every client receives individualized attention and customized styling recommendations.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-[#d6c7b0] rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Convenient Booking</h3>
                    <p class="text-gray-600">Easy online booking system with flexible scheduling to fit your busy lifestyle.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-gradient-to-r from-purple-900 via-pink-900 to-purple-900">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-white mb-6">Ready to Transform Your Look?</h2>
            <p class="text-xl text-purple-100 mb-8">
                Book your appointment today and experience the difference professional styling makes
            </p>
            <div class="flex justify-center">
                <a href="{{ route('book') }}" class="inline-block px-8 py-3 bg-white text-purple-900 text-lg font-semibold rounded shadow hover:bg-gray-100 transition">
                    Book Appointment
                </a>
            </div>
        </div>
    </section>

</x-app-layout>
