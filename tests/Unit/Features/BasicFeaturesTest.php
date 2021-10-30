<?php

use Illuminate\Http\Request;
use Nagi\LaravelWopi\Contracts\WopiInterface;
use Nagi\LaravelWopi\LaravelWopiFacade;
use Nagi\LaravelWopi\Tests\TestCase;

it('can check file info', function () {
    /** @var TestCase $this */

    /**
     * @var \Illuminate\Http\JsonResponse
     */
    $response = LaravelWopiFacade::checkFileInfo('1', 'access_token', (new Request));

    expect($response)
    ->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);

    expect($response->isOk())->toBeTrue();

    expect(json_decode($response->content(), true))
        ->toMatchArray([
            'BaseFileName' => 'test.docx',
            'OwnerId' => 'Ahmed Nagi',
            'Size' => 1000,
            'UserId' => 'Default User',
        ]);
});

it('can get file binary data or content', function () {
    /** @var TestCase $this */

    /**
     * @var \Symfony\Component\HttpFoundation\StreamedResponse
     */
    $response = LaravelWopiFacade::getFile('1', 'access_token', (new Request));

    expect($response)
    ->toBeInstanceOf(Symfony\Component\HttpFoundation\StreamedResponse::class);

    expect($response->isOk())->toBeTrue();

    ob_start();

    $response->sendContent();

    $content = ob_get_clean();

    expect($response->headers->get(WopiInterface::HEADER_ITEM_VERSION))->toBe('1');
    expect($response->headers->get('Content-Type'))->toBe('application/octet-stream');
    expect($response->headers->get('Content-Length'))->toBe('1000');
    expect($content)->toContain('Hello world');
});
