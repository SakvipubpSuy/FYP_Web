@extends('layouts.app')
@section('content')
<div class="container mx-auto px-5 mt-4 mb-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Cards</h2>
    </div>

    @if ($cards->isEmpty())
        <p>No cards found in this deck</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4 mb-4">
            @foreach ($cards as $card)
                <div class="rounded overflow-hidden shadow-lg mx-auto bg-white" style="width: 18rem;">
                    <!-- Image Section -->
                    <img class="w-full h-48 object-cover" src="{{ $card->img_url ? asset($card->img_url) : asset('/images/Zhongli.jpg') }}" alt="Card Image">
                    
                    <!-- Name, Description, QR Code Section -->
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-bold text-xl truncate" title="{{ $card->card_name }}">{{ $card->card_name }}</div>
                            <button class="btn btn-primary p-2" onclick="showQRCode('{{ route('cards.qrcode', ['card_id' => $card->card_id]) }}')">
                                <i class="fas fa-qrcode"></i> <!-- QR Code Icon -->
                            </button>
                        </div>
                        <p class="text-gray-700 text-base truncate" title="{{ $card->card_description }}">{{ $card->card_description }}</p>
                    </div>
                    
                    <!-- Details Section -->
                    <div class="px-6 pt-4 pb-2">
                        <div class="flex flex-wrap mb-2">
                            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2">Deck Name: {{ $card->deck->deck_name }}</span>
                        </div>
                        <div class="flex flex-wrap mb-2">
                            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2">Card Tier: {{ $card->cardTier->card_tier_name }}</span>
                        </div>
                        <div class="flex flex-wrap mb-2">
                            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2">Card Version: {{ $card->card_version }}</span>
                        </div>
                        <div class="flex flex-wrap">
                            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2">Card EXP: {{ $card->cardTier->card_XP }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $cards->links() }}
    @endif
</div>
@endsection




<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
            </div>
            <div class="modal-body text-center">
                <img id="qrCodeImage" src="" alt="QR Code">
            </div>
        </div>
    </div>
</div>