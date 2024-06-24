@extends('layouts.app')

@section('content')

<div class="container mx-auto px-5 mt-4 mb-4">

    <!-- This is success session for delete card -->
    @if(session('success'))
        <div class="w-full px-3 mb-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" style="background-color:lightgreen" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="w-full px-3 mb-6">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Cards</h2>
        <form method="GET" action="{{ route('cards.search') }}" class="mb-4">
            <div class="flex">
                <input type="text" name="query" placeholder="Search cards..." class="w-full px-4 py-2 border rounded" value="{{ request('query') }}">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                    Search
                </button>
            </div>
        </form>
        <a href="{{ route('cards.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
        Add Card
        </a>
    </div>

    @if ($cards->isEmpty())
        <p>No cards found.</p>
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
                        <div class="flex items-center justify-center">
                            <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="openDeleteModal({{ $card->card_id }},{{ request()->input('page', 1) }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M4 7l16 0" />
                                    <path d="M10 11l0 6" />
                                    <path d="M14 11l0 6" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2l8 0a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7l0 -3a1 1 0 0 1 1 -1l4 0a1 1 0 0 1 1 1l0 3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $cards->links() }}
    @endif
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="delete-form" method="POST">
          @csrf
          @method('DELETE')
          <div class="text-danger mt-3">
            Warning: This will delete the selected card. This action cannot be undone.
          </div>
          <input type="hidden" id="delete-card-id" name="card_id">
          <input type="hidden" name="page" value="{{ request()->input('page', 1) }}">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="submitDeleteForm()">Delete</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
    function openDeleteModal(cardId,page) {
        document.getElementById('delete-card-id').value = cardId;
        var deleteForm = document.getElementById('delete-form');
        deleteForm.action = '/cards/' + cardId;
        document.getElementById('delete-form').elements.namedItem('page').value = page;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    function submitDeleteForm() {
        document.getElementById('delete-form').submit();
    }
</script>
@endsection
