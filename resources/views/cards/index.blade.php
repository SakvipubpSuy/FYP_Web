<!-- Show all the cards here -->

@extends('layouts.app')

@section('content')

<div class="container mx-auto px-5 mt-4 mb-4">

    <!-- Delete success or error -->
    @if(session('deleteSuccess'))
      <div class="w-full px-3 mb-6">
          <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" style="background-color:lightgreen" role="alert">
              <strong class="font-bold">Success!</strong>
              <div>{{ session('deleteSuccess') }}</div>
          </div>
      </div>
    @endif

    @if(session('deleteError'))
      <div class="w-full px-3 mb-6">
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
              <strong class="font-bold">Error!</strong>
              <div>{{ session('deleteError') }}</div>
          </div>
      </div>
    @endif

    <!-- Edit Sucess or Error -->
    @if(session('editSuccess'))
      <div class="w-full px-3 mb-6">
          <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" style="background-color:lightgreen" role="alert">
              <strong class="font-bold">Success!</strong>
              <div>{{ session('editSuccess') }}</div>
          </div>
      </div>
    @endif

    @if(session('editError'))
      <div class="w-full px-3 mb-6">
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
              <strong class="font-bold">Error!</strong>
              <div>{{ session('editError') }}</div>
          </div>
      </div>
    @endif

    <!-- Update Sucess or Error -->
    @if(session('updateSuccess'))
      <div class="w-full px-3 mb-6">
          <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" style="background-color:lightgreen" role="alert">
              <strong class="font-bold">Success!</strong>
              <div>{{ session('updateSuccess') }}</div>
          </div>
      </div>
    @endif

    @if(session('updateError'))
      <div class="w-full px-3 mb-6">
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
              <strong class="font-bold">Error!</strong>
              <div>{{ session('updateError') }}</div>
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
                            <button class="btn btn-primary" onclick="showQRCode('{{ route('cards.qrcode', ['card_id' => $card->card_id]) }}' , '{{ $card->card_name}}')">Show QR Code</button>
                        </div>
                        <p class="text-gray-700 text-base">{{ $card->card_description }}</p>
                    </div>
                    <div class="px-6 pt-4 pb-2">
                        <div class="flex flex-wrap">
                            <div class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Deck Name: {{ $card->deck->deck_name }}</div>
                        </div>
                        <div class="flex flex-wrap">
                            <div class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Card Tier: {{ $card->cardTier->card_tier_name }}</div>
                        </div>
                        <div class="flex flex-wrap">
                            <div class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Card Version: {{ $card->card_version }}</div>
                        </div>
                        <div class="flex flex-wrap">
                            <div class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Card EXP: {{ $card->cardTier->card_XP }}</div>
                        </div>
                        <div class="flex items-center justify-center ">
                            <!-- Edit Button -->
                            <button type="button" class="text-black font-bold py-2 px-4 rounded" onclick="cardOpenEditModal({{$card}})">
                                <text> Edit </text>
                            </button>
                            <!-- Delete Button -->
                            <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="cardOpenDeleteModal({{ $card->card_id }},{{ request()->input('page', 1) }})">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M4 7l16 0" />
                                    <path d="M10 11l0 6" />
                                    <path d="M14 11l0 6" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2l8 0a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7l0 -3a1 1 0 0 1 1 -1l4 0a1 1 0 0 1 1 1l0 3" />
                                </svg>
                            </button>
                            <!-- Update Button -->
                            <button type="button" class="text-black font-bold py-2 px-4 rounded" onclick="cardOpenUpdateModal({{$card}})">
                                <text> Update </text>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $cards->links() }}
    @endif
</div>
@endsection

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
            Warning: This will delete the selected card. If you delete the latest version, all others versions will be deleted as well. 
            This action cannot be undone.
          </div>
          <input type="hidden" id="delete-card-id" name="card_id">
          <input type="hidden" name="page" value="{{ request()->input('page', 1) }}">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="cardSubmitDeleteForm()">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Card</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning mt-3">
            IMPORTANT: Edit means you are just changing some details and not update the card. Update the card will change the card version.
            To update the card, please click the Update button.
        </div>
        <form id="edit-form" method="POST" action="">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label for="edit-card-name" class="form-label">Card Name</label>
            <input type="text" class="form-control" id="edit-card-name" name="card_name" required>
          </div>
          <div class="mb-3">
            <label for="edit-card-description" class="form-label">Card Description</label>
            <textarea class="form-control" id="edit-card-description" name="card_description" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="edit-card-tier" class="form-label">Card Tier</label>
            <select class="form-control" id="edit-card-tier" name="card_tier_id" required>
              @foreach ($cardTiers as $cardTier)
                <option value="{{ $cardTier->card_tier_id }}">{{ $cardTier->card_tier_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="edit-card-deck" class="form-label">Card Deck</label>
            <select class="form-control" id="edit-card-deck" name="deck_id" required>
              @foreach ($decks as $deck)
                <option value="{{ $deck->deck_id }}">{{ $deck->deck_name }}</option>
              @endforeach
            </select>
          </div>
          <input type="hidden" id="edit-card-id" name="card_id">
          <button type="submit" class="btn btn-primary">Save changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateModalLabel">Update Card</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning mt-3">
            IMPORTANT: Updating the card, will increase card version. Older version won't be able to scan anymore. User that scan the QR code
            will get the latest version. Older version(s) will be available for trading.
        </div>
        <form id="update-form" method="POST" action="">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label for="update-card-name" class="form-label">Card Name</label>
            <input type="text" class="form-control" id="update-card-name" name="card_name" required>
          </div>
          <div class="mb-3">
            <label for="update-card-description" class="form-label">Card Description</label>
            <textarea class="form-control" id="update-card-description" name="card_description" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="update-card-tier" class="form-label">Card Tier</label>
            <select class="form-control" id="update-card-tier" name="card_tier_id" required>
              @foreach ($cardTiers as $cardTier)
                <option value="{{ $cardTier->card_tier_id }}">{{ $cardTier->card_tier_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="update-card-deck" class="form-label">Card Deck</label>
            <select class="form-control" id="update-card-deck" name="deck_id" required>
              @foreach ($decks as $deck)
                <option value="{{ $deck->deck_id }}">{{ $deck->deck_name }}</option>
              @endforeach
            </select>
          </div>
          <input type="hidden" id="update-card-id" name="card_id">
          <button type="submit" class="btn btn-primary">Proceed with update</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <p id="cardName"></p>
              <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
            </div>
            <div class="modal-body text-center">
                <img id="qrCodeImage" src="" alt="QR Code">
            </div>
        </div>
    </div>
</div>

