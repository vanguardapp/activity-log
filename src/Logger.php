<?php

namespace Vanguard\UserActivity;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use Vanguard\User;
use Vanguard\UserActivity\Repositories\Activity\ActivityRepository;

class Logger
{
    protected ?User $user = null;

    public function __construct(
        private readonly Request $request,
        private readonly Factory $auth,
        private readonly ActivityRepository $activities
    ) {
    }

    /**
     * Log user action.
     */
    public function log($description, $additional_data = null): Activity
    {
        return $this->activities->log([
            'description' => $description,
            'additional_data' => $additional_data,
            'user_id' => $this->getUserId(),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->getUserAgent(),
        ]);
    }

    /**
     * Get id if the user for who we want to log this action.
     * If user was manually set, then we will just return id of that user.
     * If not, we will return the id of currently logged user.
     */
    private function getUserId(): ?int
    {
        if ($this->user) {
            return $this->user->id;
        }

        return $this->auth->guard()->id();
    }

    /**
     * Get user agent from request headers.
     */
    private function getUserAgent(): string
    {
        return substr((string) $this->request->header('User-Agent'), 0, 500);
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
