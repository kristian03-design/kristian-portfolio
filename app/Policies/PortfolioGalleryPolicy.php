<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PortfolioGallery;

class PortfolioGalleryPolicy
{
    /**
     * Determine whether the user can manage the portfolio gallery.
     */
    public function manage(User $user): bool
    {
        $allowedEmails = collect(explode(',', (string) env('ADMIN_EMAILS', 'hkristianlloyd2@gmail.com')))
            ->map(fn (string $email) => strtolower(trim($email)))
            ->filter();

        return $allowedEmails->contains(strtolower((string) $user->email));
    }

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, PortfolioGallery $item): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->manage($user);
    }

    public function update(User $user, PortfolioGallery $item): bool
    {
        return $this->manage($user);
    }

    public function delete(User $user, PortfolioGallery $item): bool
    {
        return $this->manage($user);
    }
}
