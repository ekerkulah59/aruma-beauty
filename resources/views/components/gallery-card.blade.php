<div class="bg-white py-6 sm:py-8 text-white lg:py-12" x-data="{
    showLightbox: false,
    currentImage: null,
    images: [{
            src: 'images/IMG_5456.png',
            title: 'Knotless Braids - Waist Length'
        },
        {
            src: 'images/image.webp',
            title: 'Bohemian Knotless Braids'
        },
        {
            src: 'images/fd.jpg',
            title: 'Passion Twists - Mid Back'

        },
        {
            src: 'images/ds.jpg',
            title: 'Medium Twists - Waist Length'
        },
        {
            src: 'images/hfh.avif',
            title: 'Soft Locs - Mid Back'
        },
        {
            src: 'images/hhfg.avif',
            title: 'Jungle Braids - Waist Length'
        }, {
            src: 'images/images.jpg',
            title: 'Butterfly Locs - Mid Back'
        },
        {
            src: 'images/nmoultry.jpg',
            title: 'Boho Twist - Waist Length'

        },

    ]
}">
    <section class="py-12 bg-gray-100">
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-4xl font-semibold">Hair Style Gallery</h2>
            <p class="text-gray-600">Showcasing our beautiful hair transformations</p>
        </div>
        <div class="mb-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:mb-8 md:grid-cols-4 md:gap-6 xl:gap-8">
            <template x-for="(image, index) in images" :key="index">
                <a href="#" @click.prevent="showLightbox = true; currentImage = index"
                    class="group relative flex h-48 items-end overflow-hidden rounded-lg text-white bg-gray-100 shadow-lg md:h-80">
                    <img :src="image.src" :alt="image.title"
                        class="absolute inset-0 h-full w-full object-cover object-center transition duration-200 group-hover:scale-110" />

                    <div
                        class="pointer-events-none absolute inset-0 bg-gradient-to-t from-gray-800 via-transparent to-transparent opacity-50">
                    </div>

                    <span class="relative mb-3 ml-4 inline-block text-sm text-white md:ml-5 md:text-lg" style="text-shadow: 0 1px 4px rgba(0,0,0,0.8);" x-text="image.title"></span>
                </a>
            </template>
        </div>
    </section>

    <!-- Lightbox -->
    <div x-show="showLightbox" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4">

        <!-- Close button -->
        <button @click="showLightbox = false" class="absolute top-4 right-4 text-white hover:text-gray-300">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Navigation buttons -->
        <button @click="currentImage = (currentImage - 1 + images.length) % images.length"
            class="absolute left-4 text-white hover:text-gray-300">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <button @click="currentImage = (currentImage + 1) % images.length"
            class="absolute right-4 text-white hover:text-gray-300">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Image -->
        <div class="max-h-[90vh] max-w-[90vw]">
            <img :src="images[currentImage].src" :alt="images[currentImage].title"
                class="h-full w-full object-contain" />
            <div class="mt-2 text-center text-white" x-text="images[currentImage].title"></div>
        </div>
    </div>
</div>
