<?php

namespace Nagi\LaravelWopi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\WopiInterface;

class LaravelWopi implements WopiInterface
{
    public function checkFileInfo(string $fileId, string $accessToken, Request $request): JsonResponse
    {
        /**
         * @var AbstractDocumentManager
         */
        $documentManager = app(AbstractDocumentManager::class);

        $document = $documentManager::find($fileId);

        // Todo implement some sort of caching mechanism

        return response()->json($document->getResponseProprties());
    }

    public function getFile(string $fileId, string $accessToken, Request $request)
    {
        $documentManager = app(AbstractDocumentManager::class);

        $document = $documentManager::find($fileId);

        return response()->file($document->content(), [
            WopiInterface::HEADER_ITEM_VERSION => sprintf('v%s', $document->version()),
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => $document->size(),
            'Content-Disposition' => sprintf('attachment; filename=%s', $document->basename()),
        ]);
    }

    public function putFile(string $fileId, string $accessToken, Request $request)
    {
        dump('called');
    }
}
