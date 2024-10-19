
@extends('layouts.app')

@section('content')
    
<div class="container mx-auto px-5 mt-4 mb-4">

  <!-- Delete success or error -->
  @if(session('deleteSuccess'))
        <div class="w-full px-3 mb-6">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong class="font-bold">Success!</strong>
                <div>{{ session('deleteSuccess') }}</div>
            </div>
        </div>
  @endif

  @if(session('deleteError'))
    <div class="w-full px-3 mb-6">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong class="font-bold">Error!</strong>
            <div>{{ session('deleteError') }}</div>
        </div>
    </div>
  @endif

    <!-- Update Sucess or Error -->
    @if(session('updateSuccess'))
    <div class="w-full px-3 mb-6">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong class="font-bold">Success!</strong>
            <div>{{ session('updateSuccess') }}</div>
        </div>
    </div>
  @endif

  @if(session('updateError'))
    <div class="w-full px-3 mb-6">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong class="font-bold">Error!</strong>
            <div>{{ session('updateError') }}</div>
        </div>
    </div>
  @endif

  <!-- Edit Sucess or Error -->
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
        <h2 class="text-2xl font-bold">Cards</h2>
        <a href="{{ route('cards.create') }}" class="btn btn-primary py-2 px-4 rounded ml-2">
        Add Card
        </a>
    </div>
    <form method="GET" action="{{ route('cards.search') }}" class="mb-4">
            <div class="flex">
                <input type="text" name="query" placeholder="Search cards..." class="w-full px-4 py-2 border rounded" value="{{ request('query') }}">
                <button type="submit" class="btn btn-primary py-2 px-4 rounded ml-2">
                    Search
                </button>
            </div>
    </form>

    @if ($cards->isEmpty())
        <p>No cards found.</p>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4 mb-4">
        @foreach ($cards as $card)
            <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Section 1: Image -->
                <div class="relative">
                    <img class="w-full h-48 object-cover" src="{{ $card->img_url ? asset($card->img_url) : asset('/images/no_img.jpg') }}" alt="Card Image">
                    <!-- Preview Button (floating over the image on the left) -->
                    <button class="absolute top-2 ml-2 bg-white text-gray-700 p-2 rounded-full shadow hover:bg-gray-100 transition" 
                        onclick="openCardPreview(
                            '{{ $card->img_url }}',
                            '{{ $card->card_name }}',
                            '{{ $card->cardTier->color }}',
                            '{{ $card->deck->deck_name }}',
                            '{{ $card->energy }}',
                            '{{ $card->cardTier->card_tier_name }}',
                            '{{ $card->cardTier->card_XP }}',
                            '{{ $card->card_version }}'
                        )">
                      <i class="fas fa-eye"></i>
                    </button>
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

              <!-- Section 4: Action Buttons -->
              <div class="px-2 py-2 flex flex-wrap justify-center items-center bg-gray-100 gap-2 ">
                  <!-- Edit Button -->
                  <button class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition min-w-[100px]" onclick="cardOpenEditModal({{ $card }},{{ $card->question }})">
                      Edit
                  </button>
                  <!-- Update Button -->
                  <button class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition min-w-[100px]" onclick="cardOpenUpdateModal({{ $card }},{{ $card->question }})">
                      Update
                  </button>
                  <!-- Delete Button (Superadmin only) -->
                  @if (Auth::check() && Auth::user()->role === 'superadmin')
                  <button class="bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 transition min-w-[100px]" onclick="cardOpenDeleteModal({{ $card->card_id }}, {{ request()->input('page', 1) }})">
                      Delete
                  </button>
                  @endif
              </div>
            </div>
        @endforeach
      </div>
      {{ $cards->links() }}
    @endif
</div>
@endsection

<!-- QR Code Modal -->
<div id="qrCodeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="relative bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
        <!-- Close Button Inside the Modal -->
        <button class="text-gray-500 absolute top-4 right-4" onclick="closeQRCodeModal()">&#x2715;</button>
        
        <!-- Card Name -->
        <h3 id="qrCodeCardName" class="text-center text-lg font-semibold mb-4"></h3>
        
        <!-- QR Code Image -->
        <img id="qrCodeImage" src="" alt="QR Code" class="w-64 h-64 mx-auto">

        <!-- Download button -->
        <div class="flex justify-center">
          <a id="downloadQRCode" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700 transition" download>
            Download QR Code
          </a>
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
        <form id="edit-form" method="POST" action="" enctype="multipart/form-data">
          @csrf
          @method('PATCH')
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

          <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="edit_question">
                    Question
                </label>
                <textarea
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="edit_question" name="edit_question" placeholder="Question" required>{{ old('edit_question') }}
                </textarea>
            </div>
          </div>

          <div class="ml-2 mb-2">
            <div class="flex flex-wrap -mx-3 mb-6" id="edit_answers">
                <div class="w-full px-3">
                    <label>Answers:</label>
                </div>
            </div>
            <button type="button" id="edit_add_answer" class="btn btn-secondary me-2">Add Option</button>
          </div>

          <div class="mb-3">      
            <label for="edit-card-image" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
              Card Image</label>
            <input type="file" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
            id="edit-card-image" name="card_image" accept="image/*">
            <small class="form-text text-muted">Leave empty to keep the current image.</small>
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
        <form id="update-form" method="POST" action="" enctype="multipart/form-data">
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

          <div class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="update_question">
                    Question
                </label>
                <textarea
                  class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                  id="update_question" name="update_question" placeholder="Question" required>{{ old('question') }}
                </textarea>
            </div>
          </div>

          <div class="ml-2 mb-2">
            <div class="flex flex-wrap -mx-3 mb-6" id="update_answers">
                <div class="w-full px-3">
                    <label>Answers:</label>
                </div>
            </div>
            <button type="button" id="update_add_answer" class="btn btn-secondary me-2">Add Option</button>
          </div>

          <div class="mb-3">      
            <label for="update-card-image" class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
              Card Image</label>
            <input type="file" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
            id="update-card-image" name="card_image" accept="image/*">
            <small class="form-text text-muted">Leave empty to keep the current image.</small>
          </div>

          <input type="hidden" id="update-card-id" name="card_id">
          <button type="submit" class="btn btn-primary">Proceed with update</button>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#edit_add_answer').click(function() {
        // Get the current number of answers (count the existing answer fields)
        var edit_currentAnswerCount = $('#edit_answers .answer_body').length;

        // Increment the index from the current number of answers
        var edit_newAnswerIndex = edit_currentAnswerCount;
            $('#edit_answers').append(
                `
            <div class="w-full px-3 answer_body mt-1">
                <div class="answer">
                    <input type="text" name="edit_answers[${edit_newAnswerIndex}]" required>
                    <input type="radio" class="mr-1" name="is_correct" value="${edit_newAnswerIndex}"> Correct
                    <button type="button" class="btn btn-danger ml-2 remove-answer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            `
            );
        })
        $(document).on('click', '.remove-answer', function() {
            $(this).parents('div.answer_body').remove();
        });
    });


    $(document).ready(function() {
    // Function to handle the dynamic addition of answers
      $('#update_add_answer').click(function() {
          // Get the current number of answers (count the existing answer fields)
          var update_currentAnswerCount = $('#update_answers .answer_body').length;

          // Increment the index from the current number of answers
          var update_newAnswerIndex = update_currentAnswerCount;

          $('#update_answers').append(
              `
              <div class="w-full px-3 answer_body mt-1">
                  <div class="answer">
                      <input type="text" name="update_answers[${update_newAnswerIndex}]" required>
                      <input type="radio" class="mr-1" name="is_correct" value="${update_newAnswerIndex}"> Correct
                      <button type="button" class="btn btn-danger ml-2 remove-answer">
                          <i class="fa-solid fa-xmark"></i>
                      </button>
                  </div>
              </div>
              `
          );
        });

        // Function to handle the removal of answers
        $(document).on('click', '.remove-answer', function() {
            $(this).parents('div.answer_body').remove();
        });
    });
</script>
@endsection
