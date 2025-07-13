<div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
    <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-48 object-cover">
    <div class="p-6 flex flex-col flex-1">
        <h3 class="text-2xl font-serif font-bold text-gray-800 mb-2">{{ $title }}</h3>
        <p class="text-gray-600 mb-4 flex-1">{{ $description }}</p>
        <div class="flex items-center justify-between mt-auto">
            <span class="text-lg font-semibold text-[#b7a98a]">${{ $price }}</span>
            <a href="/book" class="inline-block px-4 py-2 bg-[#d6c7b0] text-gray-800 text-sm font-semibold rounded shadow hover:bg-[#cbb89e] transition uppercase">Call to Book</a>
        </div>
    </div>
</div>
