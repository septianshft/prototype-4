<?php

// app/Observers/UserObserver.php

namespace App\Observers;

use App\Models\User;
use App\Models\Data_Mahasiswa;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Jika menggunakan Spatie Role:
        if (method_exists($user, 'hasRole') && $user->hasRole('mahasiswa')) {
            Data_Mahasiswa::create(['user_id' => $user->id]);
        }
        // Atau jika menggunakan kolom role di tabel users:
        elseif (isset($user->role) && $user->role === 'mahasiswa') {
            Data_Mahasiswa::create(['user_id' => $user->id]);
        }
    }
}

