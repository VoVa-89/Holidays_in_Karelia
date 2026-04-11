<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Post;
use Illuminate\Http\Request;

final class GuestVoterKey
{
    public static function forRequest(Request $request, Post $post): string
    {
        $guestCookie = (string) $request->cookie('guest_id', '');

        return 'g:' . hash_hmac(
            'sha256',
            $post->id . '|' . $guestCookie . '|' . $request->ip(),
            (string) config('app.key')
        );
    }
}
