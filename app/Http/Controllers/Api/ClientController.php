<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Client::query();

        if ($user->hasRole('Commercial')) {
            $query->whereHas('prospect', function ($q) use ($user) {
                $q->where('commercial_id', $user->id);
            });
        }

        $clients = $query->with(['prospect.commercial'])->orderBy('created_at', 'desc')->get();

        return response()->json($clients);
    }

    public function show(Request $request, Client $client)
    {
        $user = $request->user();
        
        $client->load(['prospect.commercial', 'prospect.interactions']);

        if ($user->hasRole('Commercial') && $client->prospect->commercial_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json($client);
    }
}
