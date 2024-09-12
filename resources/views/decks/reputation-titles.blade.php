@extends('layouts.app')

@section('content')
<div class="container mx-auto px-5 mt-4 mb-4">

@if(session('editRepuationSuccess'))
        <div class="w-full px-3 mb-6">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong class="font-bold">Success!</strong>
                <div>{{ session('editRepuationSuccess') }}</div>
            </div>
        </div>
  @endif

  @if($errors->has('error_min_percentage'))
    <div class="w-full px-3 mb-6">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong class="font-bold">Error!</strong>
            <div>{{ $errors->first('error_min_percentage') }}</div>
        </div>
    </div>
  @endif

  @if($errors->has('error_max_percentage'))
    <div class="w-full px-3 mb-6">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong class="font-bold">Error!</strong>
            <div>{{ $errors->first('error_max_percentage') }}</div>
        </div>
    </div>
  @endif


    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Reputation Titles</h2>
        <a href="{{ route('tiers.create') }}" class="btn btn-primary ml-2">
        Add Reputation Title
        </a>
    </div>
    <div class="container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Minimal Percentage</th>
                    <th scope="col">Maximal Percentage</th>
                    <th scope="col">Title</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deckTitles as $deckTitle)
                <tr style="border-bottom: 2px solid">
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ $deckTitle->min_percentage}}</td>
                    <td>{{ $deckTitle->max_percentage }}</td>
                    <td>{{ $deckTitle->title }}</td>
                    <td class="py-2">
                        <button class="text-blue-600 hover:text-blue-900" onclick="reputationOpenEditModal({{$deckTitle}})">Edit</button>
                    </td> 
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $deckTitles->links() }}
    </div>
</div>
@endsection 

<!-- Edit Modal -->
<div class="modal fade" id="editReputationModal" tabindex="-1" aria-labelledby="editReputationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editReputationModalLabel">Edit Reputation Title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-reputation-form" method="POST" action="{{ route('reputation-titles.edit') }}">
          @csrf
          @method('PATCH')
          <!-- Hidden field for deck_titles_id -->
          <input type="hidden" id="edit-deck-titles-id" name="deck_titles_id">
          
          <div class="mb-3">
            <label for="edit-min-percentage" class="form-label">Minimum Percentage</label>
            <input type="number" class="form-control" id="edit-min-percentage" name="min_percentage" min="0" max="100" required>
          </div>
          
          <div class="mb-3">
            <label for="edit-max-percentage" class="form-label">Maximum Percentage</label>
            <input type="number" class="form-control" id="edit-max-percentage" name="max_percentage" min="0" max="100" required>
          </div>
          
          <div class="mb-3">
            <label for="edit-title" class="form-label">Title</label>
            <input type="text" class="form-control" id="edit-title" name="title" required>
          </div>
          
          <button type="submit" class="btn btn-primary">Update Title</button>
        </form>
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