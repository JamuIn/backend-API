<?php

namespace App\Http\Controllers\RekomendasiJamu;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RekomendasiJamu\Jamu;
use App\Models\RekomendasiJamu\JamuUser;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JamuUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:customer']);
    }

    public function showFavorites()
    {
        $favoritesJamu = JamuUser::where('user_id', auth()->user()->id)->get();
        $favorite = [];
        foreach ($favoritesJamu as $favoriteJamu) {
            $jamu = Jamu::find($favoriteJamu->jamu_id);
            if ($jamu) {
                $favorite[] = $jamu;
            }
        }
        return response()->json([
            'favorites' => $favorite
        ], Response::HTTP_OK);
    }

    public function addJamuToUserFavorite($jamuId)
    {
        $jamu = Jamu::query()->findOrFail($jamuId);
        $user = Auth::user();

        $jamu->users()->attach($user);

        return response()->json(['message' => 'Jamu added to favorite successfully.'], Response::HTTP_CREATED);
    }

    public function detachJamuFromUserFavorite($jamuId)
    {
        $jamu = Jamu::query()->findOrFail($jamuId);
        $user = Auth::user();
        $favorite = JamuUser::where('jamu_id', $jamuId)->where('user_id', $user->id)->first();

        if ($favorite == null) {
            return response()->json([
                'error' => 'No Favorite Jamu found in this account for the jamu ID'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($favorite->user_id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to delete this favorite'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $jamu->users()->detach($user);

        return response()->json(['message' => 'Jamu removed from favorites successfully.'], Response::HTTP_OK);
    }
}
