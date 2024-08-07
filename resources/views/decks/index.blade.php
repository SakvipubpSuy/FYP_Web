@extends('layouts.app')

@section('content')

<div class="container mx-auto px-5 mt-4 mb-4">

    <!-- This is success session for delete deck -->
    @if(session('editSuccess'))
        <div class="w-full px-3 mb-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" style="background-color:lightgreen" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('editSuccess') }}</span>
            </div>
        </div>
    @endif

    @if(session('editError'))
        <div class="w-full px-3 mb-6">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('editError') }}</span>
            </div>
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold">Decks</h2>
        
        <!-- Search Form -->
        <form method="GET" action="{{ route('decks.search') }}" class="mb-4">
            <div class="flex">
                <input type="text" name="query" placeholder="Search decks..." class="w-full px-4 py-2 border rounded" value="{{ request('query') }}">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                    Search
                </button>
            </div>
        </form>
        <a href="{{ route('decks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
            Add Deck
        </a>
    </div>

    @if ($decks->isEmpty())
        <p>No decks found.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4 mb-4">
            @foreach ($decks as $deck)
                <div class="max-w-2xl mx-4 sm:max-w-sm md:max-w-sm lg:max-w-sm xl:max-w-sm sm:mx-auto md:mx-auto lg:mx-auto xl:mx-auto mt-16 bg-white shadow-xl rounded-lg text-gray-900 ">
                    <div class="rounded-t-lg h-32 overflow-hidden">
                        <img class="object-cover object-top w-full" src='https://images.unsplash.com/photo-1549880338-65ddcdfd017b?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=400&fit=max&ixid=eyJhcHBfaWQiOjE0NTg5fQ' alt='Mountain'>
                    </div>
                    <div class="text-center mt-2">
                        <h2 class="font-semibold">{{ $deck->deck_name }}</h2>
                        <p class="text-gray-500">{{ $deck->deck_description }}</p>
                    </div>
                    <div class="py-4 mt-2 text-gray-700 text-center">
                        <div class="flex items-center justify-center mb-4">
                            <!-- Playing Card Icon SVG -->
                            <x-tabler-cards class="w-4 fill-current text-blue-900" />
                            <div>Number of Cards: {{ $deck->cards->count() }}</div>
                        </div>
                        <div class="flex items-center justify-center">
                            <button type="button" class="text-black font-bold py-2 px-4 rounded" onclick="deckOpenEditModal({{$deck}})">
                                <text> Edit </text>
                            </button>
                            <form id="view-form-{{ $deck->deck_id }}" action="{{ route('decks.show', $deck->deck_id) }}" method="GET">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">View Deck</button>
                            </form>
                            <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deckOpenDeleteModal({{ $deck->deck_id }},{{ request()->input('page', 1) }})">Delete</button>
                        </div>
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
        <h5 class="modal-title" id="editModalLabel">Edit Card</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-form" method="POST" action="">
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
