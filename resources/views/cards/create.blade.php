@extends('layouts.app')


@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            var q_index = 0;
            $('#add-answer').click(function() {
                ++q_index;
                $('#answers').append(
                    `
                <div class="w-full px-3 answer_body mt-1">
                    <div class="answer">
                        <input type="text" name="answers[${q_index}]" required>
                        <input type="radio" class="mr-1" name="is_correct" value="${q_index}"> Correct
                        <button type="button" class="btn btn-danger ml-2 remove-answer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                `
                );
            });

            $(document).on('click', '.remove-answer', function() {
                $(this).parents('div.answer_body').remove();
            });
        });
    </script>
@endsection

@section('content')
    <div class="container mx-auto px-5 mt-4 mb-4">
        <h2 class="text-2xl font-bold mt-4 mb-4">Add a new card</h2>
        @if ($errors->any())
            <div class="w-full px-3 mb-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Whoops!</strong>
                    <span class="block sm:inline">Please correct the following errors:</span>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    document.getElementById('successModalOkButton').addEventListener('click', function() {
                        window.location.href = "{{ route('cards.index') }}";
                    });
                });
            </script>
        @endif

    <form class="w-full" method="POST" action="{{ route('cards.store') }}" enctype="multipart/form-data">
        <div class="flex justify-between">
            <div class="w-1/2">
                    @csrf
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="card_name">
                                Card Name
                            </label>
                            <input
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                value="{{ old('card_name') }}" id="card_name" name="card_name" type="text"
                                placeholder="Card Name">
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="card_description">
                                Card Description
                            </label>
                            <textarea
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="card_description" name="card_description" placeholder="Card Description">{{ old('card_description') }}</textarea>
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="card_tier_id">Card Tier</label>
                            <select name="card_tier_id" id="card_tier_id"
                                class="mt-1 p-2 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md"
                                required>
                                <option value="">Select Card Tier</option>
                                @foreach ($cardTiers as $cardTier)
                                    <option value="{{ $cardTier->card_tier_id }}"
                                        {{ $cardTier->card_tier_id == old('card_tier_id') ? 'selected' : '' }}>
                                        {{ $cardTier->card_tier_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3">
                            <label for="deck_id" class="block text-sm font-medium text-gray-700">Deck Name</label>
                            <select name="deck_id" id="deck_id"
                                class="mt-1 p-2 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md"
                                required>
                                <option value="">Select a Deck</option>
                                @foreach ($decks as $deck)
                                    <option value="{{ $deck->deck_id }}"
                                        {{ $deck->deck_id == old('deck_id') ? 'selected' : '' }}>
                                        {{ $deck->deck_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
            </div>
            <div class="w-1/2 pl-10">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="question">
                            Question
                        </label>
                        <textarea
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="question" name="question" placeholder="Question" required>{{ old('question') }}</textarea>
                    </div>
                </div>

                <div>
                    <div class="flex flex-wrap -mx-3 mb-6" id="answers">
                        <div class="w-full px-3">
                            <label>Answers:</label>
                        </div>
                    </div>
                    <button type="button" id="add-answer" class="btn btn-secondary me-2">Add Option</button>
                </div>
                <br>
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="image">
                            Upload Image
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="image" name="image" type="file" accept="image/*">
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-2">
                    <div class="w-full px-3">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Card
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ session('success') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="successModalOkButton">OK</button>
                </div>
            </div>
        </div>
    </div>

@endsection
