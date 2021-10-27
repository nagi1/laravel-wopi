<?php

namespace Nagi\LaravelWopi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Nagi\LaravelWopi\Contracts\DocumentManagerInterface;
use Nagi\LaravelWopi\Contracts\WopiInterface;

class LaravelWopi implements WopiInterface
{
    public function checkFileInfo(string $fileId, string $accessToken, Request $request): JsonResponse
    {
        $documentManager = app(DocumentManagerInterface::class);

        $document = $documentManager::find($fileId);

        $user = optional($request->user());

        // Todo implement some sort of caching mechanism

        return response()->json([
            'BaseFileName' => $document->basename(),
            'OwnerId' => $document->owner(),
            'Size' => $document->size(),
            'Version' => $document->version(),
            'UserId' => $user->id,
        ]);
    }
}
