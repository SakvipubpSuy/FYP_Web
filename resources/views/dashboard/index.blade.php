@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <a href="{{ route('dashboard.users') }}">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text">{{ $userCount }}</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Add more cards here -->
        <div class="col-md-4">
            <a href = "{{ route('decks.index') }}">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Decks</h5>
                        <p class="card-text">{{ $deckCount }}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href = "{{ route('cards.index') }}"
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total Cards</h5>
                        <p class="card-text">{{ $cardCount }}</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection