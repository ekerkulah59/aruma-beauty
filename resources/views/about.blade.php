<x-app-layout>
    <!-- Hero Section for About -->
    <div class="relative bg-gradient-to-r from-purple-900 via-pink-900 to-purple-900">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl">
                    About Us
                </h1>
                <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-300">
                    Discover our passion for beauty, our commitment to excellence, and the story behind your trusted salon
                </p>
            </div>
        </div>
    </div>

    <!-- Our Story Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl mb-6">
                        Our Story
                    </h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Founded in 2015, our salon began with a simple vision: to create a space where every client feels beautiful, confident, and valued. What started as a small family business has grown into one of the most trusted names in hair care and styling.
                    </p>
                    <p class="text-lg text-gray-600 mb-6">
                        Our journey has been driven by passion, creativity, and an unwavering commitment to excellence. We believe that great hair has the power to transform not just your appearance, but your entire outlook on life.
                    </p>
                    <p class="text-lg text-gray-600">
                        Today, we're proud to serve thousands of satisfied clients, each with their own unique style and story. Our team of expert stylists continues to push the boundaries of creativity while maintaining the warm, personal service that has always been our hallmark.
                    </p>
                </div>
                <div class="relative">
                    <img src="{{ asset('images/salon-interior.jpg') }}" alt="Salon Interior" class="rounded-lg shadow-xl">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-lg"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Our Mission & Values -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Our Mission & Values
                </h2>
                <p class="mt-4 text-lg text-gray-600">
                    The principles that guide everything we do
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Excellence -->
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Excellence</h3>
                    <p class="text-gray-600">
                        We strive for excellence in every service, every interaction, and every detail. Our commitment to quality is unwavering.
                    </p>
                </div>

                <!-- Creativity -->
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Creativity</h3>
                    <p class="text-gray-600">
                        We embrace creativity and innovation, constantly exploring new techniques and trends to deliver unique, personalized styles.
                    </p>
                </div>

                <!-- Community -->
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Community</h3>
                    <p class="text-gray-600">
                        We're more than a salon - we're a community. We build lasting relationships with our clients and support our local community.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Our Team Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Meet Our Team
                </h2>
                <p class="mt-4 text-lg text-gray-600">
                    Passionate professionals dedicated to making you look and feel your best
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Team Member 1 -->
                <div class="text-center">
                    <div class="relative mb-6">
                        <img src="{{ asset('images/stylist-1.jpg') }}" alt="Sarah Johnson" class="w-48 h-48 rounded-full mx-auto object-cover shadow-lg">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-t from-purple-600/20 to-transparent"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Sarah Johnson</h3>
                    <p class="text-purple-600 font-semibold mb-3">Master Stylist & Owner</p>
                    <p class="text-gray-600 text-sm">
                        With over 15 years of experience, Sarah specializes in color techniques and cutting-edge styling. Her passion for education keeps our team at the forefront of industry trends.
                    </p>
                </div>

                <!-- Team Member 2 -->
                <div class="text-center">
                    <div class="relative mb-6">
                        <img src="{{ asset('images/stylist-2.jpg') }}" alt="Michael Chen" class="w-48 h-48 rounded-full mx-auto object-cover shadow-lg">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-t from-pink-600/20 to-transparent"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Michael Chen</h3>
                    <p class="text-pink-600 font-semibold mb-3">Senior Stylist</p>
                    <p class="text-gray-600 text-sm">
                        Michael is our go-to expert for precision cuts and men's styling. His attention to detail and artistic vision create stunning transformations for every client.
                    </p>
                </div>

                <!-- Team Member 3 -->
                <div class="text-center">
                    <div class="relative mb-6">
                        <img src="{{ asset('images/stylist-3.jpg') }}" alt="Emma Rodriguez" class="w-48 h-48 rounded-full mx-auto object-cover shadow-lg">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-t from-blue-600/20 to-transparent"></div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Emma Rodriguez</h3>
                    <p class="text-blue-600 font-semibold mb-3">Color Specialist</p>
                    <p class="text-gray-600 text-sm">
                        Emma's expertise in color theory and advanced techniques makes her our premier color specialist. She creates stunning, personalized color transformations.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Achievements & Awards -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Awards & Recognition
                </h2>
                <p class="mt-4 text-lg text-gray-600">
                    Celebrating our achievements and the trust of our community
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Award 1 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Best Salon 2023</h3>
                    <p class="text-gray-600 text-sm">Local Business Awards</p>
                </div>

                <!-- Award 2 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Excellence Award</h3>
                    <p class="text-gray-600 text-sm">Hair Industry Association</p>
                </div>

                <!-- Award 3 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Customer Choice</h3>
                    <p class="text-gray-600 text-sm">5-Star Rating 2023</p>
                </div>

                <!-- Award 4 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Innovation Award</h3>
                    <p class="text-gray-600 text-sm">Beauty Industry Excellence</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Why Choose Us -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Why Choose Us
                </h2>
                <p class="mt-4 text-lg text-gray-600">
                    What sets us apart from the rest
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Expert Team</h3>
                            <p class="text-gray-600">Our stylists are certified professionals with years of experience and ongoing training.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Premium Products</h3>
                            <p class="text-gray-600">We use only the highest quality, professional-grade products for optimal results.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Personalized Service</h3>
                            <p class="text-gray-600">Every client receives personalized attention and customized styling recommendations.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Hygiene Standards</h3>
                            <p class="text-gray-600">We maintain the highest standards of cleanliness and sanitation for your safety.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Satisfaction Guarantee</h3>
                            <p class="text-gray-600">We're committed to your satisfaction and will work until you're completely happy.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Convenient Booking</h3>
                            <p class="text-gray-600">Easy online booking system with flexible scheduling to fit your busy lifestyle.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                Experience the Difference
            </h2>
            <p class="mt-4 text-xl text-purple-100">
                Join our community of satisfied clients and discover why we're the trusted choice for hair care
            </p>
            <div class="mt-8 flex justify-center space-x-4">
                <a href="{{ route('book') }}" class="inline-block bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Book Your Visit
                </a>
                <a href="{{ route('services') }}" class="inline-block bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-purple-600 transition duration-300">
                    View Services
                </a>
            </div>
        </div>
    </div>


</x-app-layout>
