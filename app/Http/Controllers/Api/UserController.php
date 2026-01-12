<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    /**
     * GET /users
     */
    public function index(Request $request)
    {
        $currentUser = $request->user();

        $query = User::query()
            ->where('status', User::STATUS_ACTIVE)
            ->where('id', '<>', $currentUser->id);

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $limit = (int) $request->query('limit', 25);
        $limit = max(1, min($limit, 100));

        $users = $query
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'phone', 'email']);

        return $this->success(
            message: 'Active users list.',
            code: 'USERS_LIST',
            data: $users,
        );
    }
}
