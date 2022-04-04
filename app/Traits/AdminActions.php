<?php

namespace App\Traits;

trait AdminActions
{
    //allowing all actions for admin users
	public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}