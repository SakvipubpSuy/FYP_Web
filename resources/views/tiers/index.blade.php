@extends('layouts.app')

@section('content')
<div class="container mx-auto px-5 mt-4 mb-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Tiers</h2>
        <form method="GET" action="{{ route('tiers.search') }}" class="mb-4">
            <div class="flex">
                <input type="text" name="query" placeholder="Search cards..." class="w-full px-4 py-2 border rounded" value="{{ request('query') }}">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                    Search
                </button>
            </div>
        </form>
        <a href="{{ route('tiers.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
        Add New Tier
        </a>
    </div>
    <div class="container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">id</th>
                    <th scope="col">Tier Name</th>
                    <th scope="col">Tier EXP</th>
                    <th scope="col">Energy Required</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cardtiers as $cardtier)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ $cardtier->card_tier_id }}</td>
                    <td>{{ $cardtier->card_tier_name}}</td>
                    <td>{{ $cardtier->card_XP }}</td>
                    <td>{{ $cardtier->card_energy_required }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $cardtiers->links() }}
    </div>
</div>
@endsection 