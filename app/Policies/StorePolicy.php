<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function view(User $user, Store $store): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        
        if ($user->id !== $store->user_id) {
            return false;
        }
        
        if ($store->workspace_id && $store->workspace) {
            return $store->workspace->user_id === $user->id;
        }
        
        return true;
    }

    public function update(User $user, Store $store): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        
        if ($user->id !== $store->user_id) {
            return false;
        }
        
        if ($store->workspace_id && $store->workspace) {
            return $store->workspace->user_id === $user->id;
        }
        
        return true;
    }

    public function delete(User $user, Store $store): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        
        if ($user->id !== $store->user_id) {
            return false;
        }
        
        if ($store->workspace_id && $store->workspace) {
            return $store->workspace->user_id === $user->id;
        }
        
        return true;
    }
}
