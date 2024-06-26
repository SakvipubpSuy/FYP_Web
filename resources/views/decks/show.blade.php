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
                <div class="rounded overflow-hidden shadow-lg max-w-xs mx-auto">
                    <img class="w-full h-48 object-cover" src="{{ asset('/images/Zhongli.jpg') }}" alt="Card Image">
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="font-bold text-xl">{{ $card->card_name }}</div>
                            <img src="{{ route('cards.qrcode', ['card_id' => $card->card_id]) }}" alt="QR Code">
                        </div>
                        <p class="text-gray-700 text-base">{{ $card->card_description }}</p>
                    </div>
                    <div class="px-6 pt-4 pb-2">
                        <div class="flex flex-wrap">
                            <div class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Deck Name: {{ $card->deck->deck_name }}</div>
                        </div>
                        <div class="flex flex-wrap">
                            <div class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Card Tier: {{ $card->card_tier }}</div>
                        </div>
                        <div class="flex flex-wrap">
                            <div class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Card Version: {{ $card->card_version }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $cards->links() }}
    @endif
</div>

@endsection