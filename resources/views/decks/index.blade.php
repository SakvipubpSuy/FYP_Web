@extends('layouts.app')


@section('content')

<div class="container mx-auto px-5 mt-4 mb-4">
    <!-- This is success session for delete deck -->
    @if(session('deleteSuccess'))
        <div class="w-full px-3 mb-6">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong class="font-bold">Success!</strong>
                <div>{{ session('deleteSuccess') }}</div>
            </div>
        </div>
    @endif
    <!-- This is success and error session for edit deck -->
    @if(session('editSuccess'))
        <div class="w-full px-3 mb-6">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong class="font-bold">Success!</strong>
                <div>{{ session('editSuccess') }}</div>
            </div>
        </div>
    @endif

    @if(session('editError'))
        <div class="w-full px-3 mb-6">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong class="font-bold">Error!</strong>
                <div>{{ session('editError') }}</div>
            </div>
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold">Decks</h2>
      <div class="flex space-x-4">
          <a href="{{ route('reputation-titles.index') }}" class="btn btn-primary">
              Reputation System
          </a>
          <a href="{{ route('decks.create') }}" class="btn btn-primary ml-2">
              Add Deck
          </a>
      </div>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('decks.search') }}" class="mb-4">
            <div class="flex">
                <input type="text" name="query" placeholder="Search decks..." class="w-full px-4 py-2 border rounded" value="{{ request('query') }}">
                <button type="submit" class="btn btn-primary py-2 px-4 rounded ml-2">
                    Search
                </button>
            </div>
    </form>
    
    @if ($decks->isEmpty())
        <p>No decks found.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4 mb-4">
          @foreach ($decks as $deck)
              <div class="bg-white shadow-lg rounded-lg text-gray-900 p-4 flex flex-col justify-between">
                  <!-- Image Section -->
                <div class="h-32 overflow-hidden rounded-t-lg">
                    <img class="object-cover w-full h-full" src="{{ $deck->img_url ? asset($deck->img_url) : asset('/images/no_img.jpg') }}" alt="Card Image">
                </div>
                <!-- Deck Name & Description Section -->
                <div class="mt-2 flex-1">
                    <h2 class="font-semibold text-lg truncate">{{ $deck->deck_name }}</h2>
                    <p class="text-gray-500 text-sm truncate">{{ $deck->deck_description }}</p>
                </div>
                <!-- Number of Cards Section -->
                <div class="py-2 mt-2 text-gray-700 text-center">
                  <div class="flex items-center justify-center">
                      <!-- Playing Card Icon SVG -->
                      <x-tabler-cards class="w-4 fill-current text-blue-900 mr-2" />
                      <div>Number of Cards: {{ $deck->cards->count() }}</div>
                  </div>
                </div>
                <!-- Buttons Section -->
                <div class="px-6 pb-4 flex justify-between items-center">                        
                  <!-- Edit Button -->
                  <button type="button" class="text-black font-bold py-2 px-4 rounded" onclick="deckOpenEditModal({{$deck}})">
                      Edit
                  </button>
                  <!-- View Deck Button -->
                  <form id="view-form-{{ $deck->deck_id }}" action="{{ route('decks.show', $deck->deck_id) }}" method="GET" class="m-0 p-0">
                      <button type="submit" class="text-black font-bold py-2 px-4 rounded">
                        View
                      </button>
                  </form>
                  <!-- Row for Delete button -->
                  @if (Auth::check() && Auth::user()->role === 'superadmin')
                  <div>
                      <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deckOpenDeleteModal({{ $deck->deck_id }},{{ request()->input('page', 1) }})">
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
                    @endif
                </div>
            </div>
          @endforeach
        </div>
      {{ $decks->links() }}
    @endif
</div>

@endsection

<!-- Edit Deck Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Deck</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-form" method="POST" action="" enctype="multipart/form-data">
          @csrf
          @method('PATCH')
          <div class="mb-3">
            <label for="edit-deck-name" class="form-label">Deck Name</label>
            <input type="text" class="form-control" id="edit-deck-name" name="deck_name" required>
          </div>
          <div class="mb-3">
            <label for="edit-deck-description" class="form-label">Deck Description</label>
            <textarea class="form-control" id="edit-deck-description" name="deck_description" rows="3" required></textarea>
          </div>
          <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="image">
                    Upload Image
                </label>
                <input
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="image" name="deck_image" type="file" accept="image/*">
            </div>
          </div>
          <input type="hidden" id="edit-deck-id" name="deck_id">
          <button type="submit" class="btn btn-primary">Save changes</button>
        </form>
      </div>
    </div>
  </div>
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
          <div class="mb-3">
            <label for="admin-password" class="form-label">Enter your Password</label>
            <input type="password" class="form-control" id="admin-password" name="admin_password" required>
          </div>
          <div class="text-danger mt-3">
            Warning: Deleting this deck will also delete all the cards within it. This action cannot be undone.
          </div>
          <input type="hidden" id="delete-deck-id" name="deck_id">
          <input type="hidden" name="page" value="{{ request()->input('page', 1) }}">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="deckSubmitDeleteForm()">Delete</button>
      </div>
    </div>
  </div>
</div>

@section('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert[role="alert"]');
        alerts.forEach(alert => {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000); // this is in milliseconds, 1000ms = 1 second
</script>
@endsection