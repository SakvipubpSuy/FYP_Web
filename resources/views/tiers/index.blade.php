@extends('layouts.app')

@section('content')
<div class="container mx-auto px-5 mt-4 mb-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold ml-2">Tiers</h2>
        <a href="{{ route('tiers.create') }}" class="btn btn-primary ml-2">
        Add New Tier
        </a>
    </div>
    <form method="GET" action="{{ route('tiers.search') }}" class="mb-4">
        <div class="flex">
            <input type="text" name="query" placeholder="Search tiers..." class="w-full px-4 py-2 border rounded" value="{{ request('query') }}">
            <button type="submit" class="btn btn-primary ml-2">
                Search
            </button>
        </div>
    </form>
    <div class="container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">id</th>
                    <th scope="col">Tier Name</th>
                    <th scope="col">Tier EXP</th>
                    <th scope="col">Energy Required</th>
                    <th scope="col">Total Cards</th>
                    <th scope="col">Color</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cardtiers as $cardtier)
                <tr style="border-bottom: 4px solid {{ $cardtier->color }}">
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ $cardtier->card_tier_id }}</td>
                    <td>{{ $cardtier->card_tier_name}}</td>
                    <td>{{ $cardtier->card_XP }}</td>
                    <td>{{ $cardtier->card_energy_required }}</td>
                    <td>{{ $cardtier->cards->count() }}</td>
                    <td>{{ $cardtier->color }}</td>
                    <td class="py-2">
                        <button class="text-blue-600 hover:text-blue-900" onclick="tierOpenEditModal({{$cardtier}})">Edit</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $cardtiers->links() }}
    </div>
</div>
@endsection 

<!-- Edit Modal -->
<div class="modal fade" id="editTierModal" tabindex="-1" aria-labelledby="editTierModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editTierModalLabel">Edit Card Tier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-tier-form" method="POST">
          @csrf
          @method('PATCH')
          <div class="mb-3">
            <label for="edit-card-tier-name" class="form-label">Tier Name</label>
            <input type="text" class="form-control" id="edit-card-tier-name" name="card_tier_name" required>
          </div>
          <div class="mb-3">
            <label for="edit-card-XP" class="form-label">Tier XP</label>
            <input type="number" class="form-control" id="edit-card-XP" name="card_XP" required>
          </div>
          <div class="mb-3">
            <label for="edit-card-energy-required" class="form-label">Energy Required</label>
            <input type="number" class="form-control" id="edit-card-energy-required" name="card_energy_required" required>
          </div>
          <div class="mb-3">
            <label for="edit-color" class="form-label">Color</label>
            <div id="edit-color-picker"></div>
            <input type="hidden" id="edit-color" name="color">
          </div>
          <button type="submit" class="btn btn-primary">Update Tier</button>
        </form>
      </div>
    </div>
  </div>
</div>