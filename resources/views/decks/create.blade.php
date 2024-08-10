@extends('layouts.app')

@section('content')

<div class="container mx-auto px-5 mt-4 mb-4">

  <h2 class="text-2xl font-bold mt-4 mb-4">Add a new deck</h2>

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
  
  @if(session('success'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
      
        document.getElementById('successModalOkButton').addEventListener('click', function() {
          window.location.href = "{{ route('decks.index') }}";
        });
      });
    </script>
  @endif

  <form class="w-full max-w-lg" method="POST" action="{{ route('decks.store') }} " enctype="multipart/form-data">
    @csrf
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-full px-3">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="card_name">
          Deck Name
        </label>
        <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" value="{{old('deck_name')}}" id="deck_name" name="deck_name" type="text" placeholder="deck_name">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-full px-3">
        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="card_description">
          Deck Description
        </label>
        <textarea class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="deck_description" name="deck_description" placeholder="Deck Description">{{old('deck_description')}}</textarea>
      </div>
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
    <div class="flex flex-wrap -mx-3 mb-2">
      <div class="w-full px-3">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          Add Deck
        </button>
      </div>
    </div>
  </form>
</div>

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