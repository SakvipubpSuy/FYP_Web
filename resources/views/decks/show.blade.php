@extends('layouts.app')
@section('content')
<div class="container mx-auto px-5 mt-4 mb-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold"> {{$decks->deck_name}}</h2>
        <!-- Button to trigger QR Code modal -->
        <button id="qrCodeModalButton" class="btn btn-primary p-2" onclick="openDownloadQRCodeModal()">
            <i class="fas fa-download text-xl"></i> Download QR Codes
        </button>
    </div>

    @if ($cards->isEmpty())
        <p>No cards found in this deck</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4 mb-4">
            @foreach ($cards as $card)
                <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- Section 1: Image -->
                    <div class="relative">
                        <img class="w-full h-48 object-cover" src="{{ $card->img_url ? asset($card->img_url) : asset('/images/no_img.jpg') }}" alt="Card Image">
                        <!-- QR Code Icon Button (floating over the image) -->
                        <button class="absolute top-2 right-2 bg-white text-gray-700 p-2 rounded-full shadow hover:bg-gray-100 transition" onclick="toggleQRCode(this, '{{ $card->qr_code_path }}', '{{ $card->card_name }}')">
                            <i class="fas fa-qrcode"></i>
                        </button>
                    </div>

                    <!-- Section 2: Card Name + Description -->
                    <div class="p-4">
                        <h3 class="font-bold text-lg truncate" title="{{ $card->card_name }}">{{ $card->card_name }}</h3>
                        <p class="text-gray-600 text-sm truncate mb-3" title="{{ $card->card_description }}">{{ $card->card_description }}</p>
                    </div>

                    <!-- Section 3: Card Details -->
                    <div class="px-4 py-2 bg-gray-100">
                        <div class="flex flex-col mb-2">
                            <span class="text-sm font-semibold text-gray-700">Deck Name: {{ $card->deck->deck_name }}</span>
                            <span class="text-sm font-semibold text-gray-700">Card Tier: {{ $card->cardTier->card_tier_name }}</span>
                            <span class="text-sm font-semibold text-gray-700">Card Version: {{ $card->card_version }}</span>
                            <span class="text-sm font-semibold text-gray-700">Card EXP: {{ $card->cardTier->card_XP }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $cards->links() }}
    @endif
</div>
@endsection

<!-- Download QR Code Modal -->
<div id="downloadQRCodeModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-h-screen max-w-3xl w-full flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-bold">Download QR Codes</h3>
            <button class="text-black" onclick="closeDownloadQRCodeModal()">&#x2715;</button>
        </div>
        <div class="overflow-y-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4" style="max-height: 400px;">
            @foreach ($allCards as $card) <!-- Use $allCards to include all cards in the deck -->
                <div class="text-center">
                    <p class="font-bold">{{ $card->card_name }}</p>
                    <img src="{{ asset($card->qr_code_path) }}" alt="QR Code" class="w-8 h-8 mx-auto mb-2">
                </div>
            @endforeach
        </div>
        <div class="text-right mt-4">
            <form method="POST" action="{{ route('decks.downloadPDF', $decks->deck_id) }}">
                @csrf
                <button type="submit" class="btn btn-primary p-2">
                    <i class="fas fa-download text-xl"></i> Download as PDF
                </button>
            </form>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qrCodeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="relative bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
        <!-- Close Button Inside the Modal -->
        <button class="text-gray-500 absolute top-4 right-4" onclick="closeQRCodeModal()">&#x2715;</button>
        
        <!-- Card Name -->
        <h3 id="qrCodeCardName" class="text-center text-lg font-semibold mb-4"></h3>
        
        <!-- QR Code Image -->
        <img id="qrCodeImage" src="" alt="QR Code" class="w-64 h-64 mx-auto">
    </div>
</div>


