<?php

namespace App\Http\Middleware;

use App\Http\Requests\PostStoreRequest;
use App\Http\Resources\UserResource;
use App\Libs\Utilities;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // dd('REQUEST->USER', $request->user());
        return [
            ...parent::share($request),
            'appName' => env('APP_NAME'),
            'auth' => [
                // 'user' => $request->user(),
                // 'user' => new UserResource($request->user()),
                'user' => $request->user()
                    ? new UserResource($request->user())
                    : $request->user(),
                // o
                // : null,
            ],
            // 'upload' => [
            //     'allowedMimeTypes' => PostStoreRequest::$allowedMimeTypes,
            // ],
            // 'allowedMimeTypes' => PostStoreRequest::$allowedMimeTypes,
            // Pasado a ...
            'allowedMimeTypes' => Utilities::$allowedMimeTypes,
            'maxTimeOnSecondsForNotificationBox' => Utilities::$maxTimeOnSecondsForNotificationBox,

            'defaultAvatarImage' => Utilities::$defaultAvatarImage,
        ];
    }
}
