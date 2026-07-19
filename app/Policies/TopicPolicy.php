<?php

namespace App\Policies;

use App\Models\Topic;
use App\Models\User;

class TopicPolicy
{
    public function update(User $user, Topic $topic): bool
    {
        return $user->isAdmin() || $topic->user_id === $user->id;
    }

    public function delete(User $user, Topic $topic): bool
    {
        return $this->update($user, $topic);
    }

    public function toggleActive(User $user, Topic $topic): bool
    {
        return $this->update($user, $topic);
    }
}
