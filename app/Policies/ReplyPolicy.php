<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;

class ReplyPolicy
{
    public function toggleActive(User $user, Reply $reply): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Reply $reply): bool
    {
        return $user->isAdmin();
    }
}
