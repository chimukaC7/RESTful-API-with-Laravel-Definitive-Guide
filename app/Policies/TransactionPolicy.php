<?php

namespace App\Policies;

use App\User;
use App\Transaction;
use App\Traits\AdminActions;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization, AdminActions;

    /**
     * Determine whether the user can view the transaction.
     *
     * @param  \App\User  $user
     * @param  \App\Transaction  $transaction
     * @return mixed
     */
    public function view(User $user, Transaction $transaction)
    {
        //buyer can obtain the information and the seller can obtain the information
        return $user->id === $transaction->buyer->id || $user->id === $transaction->product->seller->id;
    }
}
