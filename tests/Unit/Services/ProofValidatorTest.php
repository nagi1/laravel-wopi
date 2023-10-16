<?php

use Carbon\CarbonImmutable;
use Mockery\MockInterface;
use Nagi\LaravelWopi\Facades\ProofValidator;
use Nagi\LaravelWopi\Services\Discovery;
use Nagi\LaravelWopi\Support\DotNetTimeConverter;
use Nagi\LaravelWopi\Support\ProofValidatorInput;
use Nagi\LaravelWopi\Support\RequestHelper;
use Nagi\LaravelWopi\Tests\TestCase;

// Base setup for all tests bellow
beforeEach(function () {
    /** @var TestCase $this */

    $wopiDiscoveryModulus = '0HOWUPFFgmSYHbLZZzdWO/HUOr8YNfx5NAl7GUytooHZ7B9QxQKTJpj0NIJ4XEskQW8e4dLzRrPbNOOJ+KpWHttXz8HoQXkkZV/gYNxaNHJ8/pRXGMZzfVM5vchhx/2C7ULPTrpBsSpmfWQ6ShaVoQzfThFUd0MsBvIN7HVtqzPx9jbSV04wAqyNjcro7F3iu9w7AEsMejHbFlWoN+J05dP5ixryF7+2U5RVmjMt7/dYUdCoiXvCMt2CaVr0XEG6udHU4iDKVKZjmUBc7cTWRzhqEL7lZ1yQfylp38Nd2xxVJ0sSU7OkC1bBDlePcYGaF3JjJgsmp/H5BNnlW9gSxQ==';

    $wopiDiscoveryExponent = 'AQAB';

    $wopiDiscoveryOldModulus = 'u/ppb/da4jeKQ+XzKr69VJTqR7wgQp2jzDIaEPQVzfwod+pc1zvO7cwjNgfzF/KQGkltoOi9KdtMzR0qmX8C5wZI6wGpS8S4pTFAZPhXg5w4EpyR8fAagrnlOgaVLs0oX5UuBqKndCQyM7Vj5nFd+r53giS0ch7zDW0uB1G+ZWqTZ1TwbtV6dmlpVuJYeIPonOJgo2iuh455KuS2gvxZKOKR27Uq7W949oM8sqRjvfaVf4xDmyor++98XX0zadnf4pMWfPr3XE+bCXtB9jIPAxxMrALf5ncNRhnx0Wyf8zfM7Rfq+omp/HxCgusF5MC2/Ffnn7me/628zzioAMy5pQ==';

    $wopiDiscoveryOldExponent = 'AQAB';

    $this->mock(Discovery::class, function (MockInterface $mock) use ($wopiDiscoveryExponent, $wopiDiscoveryModulus, $wopiDiscoveryOldExponent, $wopiDiscoveryOldModulus) {
        $mock
        ->shouldReceive('getProofModulus')
        ->andReturn($wopiDiscoveryModulus);

        $mock
        ->shouldReceive('getProofExponent')
        ->andReturn($wopiDiscoveryExponent);

        $mock
        ->shouldReceive('getOldProofExponent')
        ->andReturn($wopiDiscoveryOldExponent);

        $mock
        ->shouldReceive('getOldProofModulus')
        ->andReturn($wopiDiscoveryOldModulus);
    });
});

it('can verify a request after 20 minutes', function () {
    /** @var TestCase $this */

    // now() now :) is 2021-09-05 20:10:00.0 UTC (+00:00)
    CarbonImmutable::setTestNow(CarbonImmutable::createFromTimestamp(1630872600));

    $result = ProofValidator::isValid(new ProofValidatorInput(
        'test',
        '637706757744700017', // 2021-10-24 12:36:14.0 +00:00
        'http://localhost',
        'some-key',
        'some-old-key'
    ));

    expect($result)->toBeFalse();
});

it('can verify X-WOPI-Proof with current discovery proof key', function () {
    /** @var TestCase $this */

    $wopiHeaderTimeStamp = '635655897610773532';

    // now() now :) is  2015-04-25 20:16:01.0 +00:00
    CarbonImmutable::setTestNow(DotNetTimeConverter::toDatetime($wopiHeaderTimeStamp));

    $wopiHeaderProofKey = 'IflL8OWCOCmws5qnDD5kYMraMGI3o+T+hojoDREbjZSkxbbx7XIS1Av85lohPKjyksocpeVwqEYm9nVWfnq05uhDNGp2MsNyhPO9unZ6w25Rjs1hDFM0dmvYx8wlQBNZ/CFPaz3inCMaaP4PtU85YepaDccAjNc1gikdy3kSMeG1XZuaDixHvMKzF/60DMfLMBIu5xP4Nt8i8Gi2oZs4REuxi6yxOv2vQJQ5+8Wu2Olm8qZvT4FEIQT9oZAXebn/CxyvyQv+RVpoU2gb4BreXAdfKthWF67GpJyhr+ibEVDoIIolUvviycyEtjsaEBpOf6Ne/OLRNu98un7WNDzMTQ==';

    $wopiOldHeaderProofKey = 'lWBTpWW8q80WC1eJEH5HMnGka4/LUF7zjUPqBwRMO0JzVcnjICvMP2TZPB2lJfy/4ctIstCN6P1t38NCTTbLWlXuE"
    "+c4jqL9r2HPAdPPcPYiBAE1Evww93GpxVyOVcGADffshQvfaYFCfwL9vrBRstaQuWI0N5QlBCtWbnObF4dFsFWRRSZVU0X9YcNGhVX1NkVFVfCKG63Q/JkL+TnsJ7zqb7ZQpbS19tYyy4abtlGKWm3Zc1Jq9hPI3XVpoARXEO8cW6lT932QGdZiNr9aW2c15zTC6WiTxVeu7RW2Y0meX+Sfyrfu7GFb5JXDJAq8ZrUEUWABv1BOhHz5vLYHIA==';

    $wopiUrl = 'https://contoso.com/wopi/files/vHxYyRGM8VfmSGwGYDBMIQPzuE+sSC6kw+zWZw2Nyg?access_token=yZhdN1qgywcOQWhyEMVpB6NE3pvBksvcLXsrFKXNtBeDTPW%2fu62g2t%2fOCWSlb3jUGaz1zc%2fzOzbNgAredLdhQI1Q7sPPqUv2owO78olmN74DV%2fv52OZIkBG%2b8jqjwmUobcjXVIC1BG9g%2fynMN0itZklL2x27Z2imCF6xELcQUuGdkoXBj%2bI%2bTlKM';

    $accessToken = RequestHelper::getAccessTokenFromUrl($wopiUrl);

    $result = ProofValidator::isValid(new ProofValidatorInput(
        $accessToken,
        $wopiHeaderTimeStamp,
        $wopiUrl,
        $wopiHeaderProofKey,
        $wopiOldHeaderProofKey
    ));

    expect($result)->toBeTrue();
});

it('can verify X-WOPI-ProofOld with current discovery proof key', function () {
    /** @var TestCase $this */

    $wopiHeaderTimeStamp = '635655898374047766';

    // now() now :) is  2015-04-25 20:16:01.0 +00:00
    CarbonImmutable::setTestNow(DotNetTimeConverter::toDatetime($wopiHeaderTimeStamp));

    $wopiHeaderProofKey = 'qQhMjQf9Zohj+S/wvhe+RD6W5TIEqJwDWO3zX9DB85yRe3Ide7EPQDCY9dAZtJpWkIDDzU+8FEwnexF0EhPimfCkmAyoPpkl2YYvQvvwUK2gdlk3WboWOVszm17p4dSDA0TDMPYsjaAGHKM/nPnTyIMzRyArEzoy2vNkLEP6qdBIuMP2aCtGsciwMjYifHYRIRenB7H7I+FkwH0UaoTUCoo2PJkyZjy1nK6OwGVWaWG0G8g7Zy+K3bRYV+7cNaM5SB720ezhmYYJJvsIdRvO7pLsjAuTo4KJhvmVFCipwyCdllVHY83GjuGOsAbHIIohl0Ttq59o0jp4w2wUs8U+mQ==';

    $wopiOldHeaderProofKey = 'PjKR1BTNNnfOrUzfo27cLIhlrbSiOVZaANadDyHxKij/77ZYId+liyXoawvvQQPgnBH1dW6jqpr6fh5ZxZ9IOtaV+cTSUGnGdRSn7FyKs1ClpApKsZBO/iRBLXw3HDWOLc0jnA2bnxY8yqbEPmH5IBC9taYzxnf7aGjc6AWFHfs6AEQ8lMio6UoASNzjy3VVNzUX+CK+e5Z45coT0X60mjaJmidGfPdWIfyUw8sSuUwxQa1uNXAd8IceRUL7j5s9/kk7EwsihCw1Y3L+XJGG5zMsGhM9bTK5mvxj30UdmZORouNHdywOfdHaB1iOeKOk+yvWFMW3JsYShWbUhZUOEQ==';

    $wopiUrl = 'https://contoso.com/wopi/files/RVQ29k8tf3h8cJ/Endy+aAMPy0iGhLatGNrhvKofPY9p2w?access_token=zo7pjAbo%2fyof%2bvtUJn5gXpYcSl7TSSx0TbQGJbWJSll9PTjRRsAbG%2fSNL7FM2n5Ei3jQ8UJsW4RidT5R4tl1lrCi1%2bhGjxfWAC9pRRl6J3M1wZk9uFkWEeGzbtGByTkaGJkBqgKV%2ffxg%2bvATAhVr6E3LHCBAN91Wi8UG';

    $accessToken = RequestHelper::getAccessTokenFromUrl($wopiUrl);

    $result = ProofValidator::isValid(new ProofValidatorInput(
        $accessToken,
        $wopiHeaderTimeStamp,
        $wopiUrl,
        $wopiHeaderProofKey,
        $wopiOldHeaderProofKey
    ));

    expect($result)->toBeTrue();
});

it('can verify X-WOPI-Proof with old discovery proof key', function () {
    /** @var TestCase $this */

    $wopiHeaderTimeStamp = '635655898062751632';

    // now() now :) is  2015-04-25 20:16:01.0 +00:00
    CarbonImmutable::setTestNow(DotNetTimeConverter::toDatetime($wopiHeaderTimeStamp));

    $wopiHeaderProofKey = 'qF15pAAnOATqpUTLHIS/Z5K7OYFVjWcgKGbHPa0eHRayXsb6JKTelGQhvs74gEFgg1mIgcCORwAtMzLmEFmOHgrdvkGvRzT3jtVVtwkxEhQt8aQL20N0Nwn4wNah0HeBHskdvmA1G/qcaFp8uTgHpRYFoBaSHEP3AZVNFg5y2jyYR34nNj359gktc2ZyLel3J3j7XtyjpRPHvvYVQfh7RsArLQ0VGp8sL4/BDHdSsUyJ8FXe67TSrz6TMZPwhEUR8dYHYek9qbQjC+wxPpo3G/yusucm1gHo0BjW/l36cI8FRmNs1Fqaeppxqu31FhR8dEl7w5dwefa9wOUKcChF6A==';

    $wopiOldHeaderProofKey = 'KmYzHJ9tu4SXfoiWzOkUIc0Bh8H3eJrA3OnDSbu2hT68EuLTp2vmvvFcHyHIiO8DuKj7u13MxkpuUER6VSIJp3nYfm91uEE/3g61V3SzaeRXdnkcKUa5x+ulKViECL2n4mpHzNnymxojFW5Y4lKUU4qEGzjE71K1DSFTU/CBkdqycsuy/Oct8G4GhA3O4MynlCf64B9LIhlWe4G+hxZgxIO0pq7w/1SH27nvScWiljVqgOAKr0Oidk/7sEfyBcOlerLgS/A00nJYYJk23DjrKGTKz1YY0CMEsROJCMiW11caxr0aKseOYlfmb6K1RXxtmiDpJ2T4y8jintjEdzEWDA==';

    $wopiUrl = 'https://contoso.com/wopi/files/DJNj59eQlM6BvwzAHkykiB1vNOWRuxT487+guv3v7HexfA?access_token=pbocsujrb9BafFujWh%2fuh7Y6S5nBnonddEzDzV0zEFrBwhiu5lzjXRezXDC9N4acvJeGVB5CWAcxPz6cJ6FzJmwA4ZgGP6FaV%2b6CDkJYID3FJhHFrbw8f2kRfaceRjV1PzXEvFXulnz2K%2fwwv0rF2B4A1wGQrnmwxGIv9cL5PBC4';

    $accessToken = RequestHelper::getAccessTokenFromUrl($wopiUrl);

    $result = ProofValidator::isValid(new ProofValidatorInput(
        $accessToken,
        $wopiHeaderTimeStamp,
        $wopiUrl,
        $wopiHeaderProofKey,
        $wopiOldHeaderProofKey
    ));

    expect($result)->toBeTrue();
});

it('can invalidate proof key', function () {
    /** @var TestCase $this */

    $wopiHeaderTimeStamp = '635655899260461032';

    // now() now :) is  2015-04-25 20:16:01.0 +00:00
    CarbonImmutable::setTestNow(DotNetTimeConverter::toDatetime($wopiHeaderTimeStamp));

    $wopiHeaderProofKey = 'Y/wVmPuvWJ5Q/Gl/a5mCkTrCKbfHWYiMG6cxmJgD+M/yYFzTsfcgbK2IRAlCR2eqGx3a5wh5bQzlC6Y/sKo2IE9Irz/NHFpV55zYfdwDi5ccwXSd34jWVUgkM3uL1r6KVmHcQH/ew10p54FVatXatuGp2Y+cq9BScYV1a45U8fs9zYoZcTAYvdWeYXmJbRGMOLLxab3fOiulPmbw+gOqpYPbuInc0yut6eGxAUmY1ENxpN7nbUqI3LbJvmE3PuX8Ifgg3RCsaFJEqC6JR36MjG+VqoKaFI6hh/ZWLR4y7FBarSmQBr2VlAHrcWCJIMXoOOKaHdsDfNCb3A24LFvdAQ==';

    $wopiOldHeaderProofKey = 'Pdbk1FoAB4zhxaDptfVOwTCmrNgWO6zdokoI3VYO8eshE9nJR1Rzr9K2666za29IfT050jJX0EBanIXAawL4rFA6swPHYQAzf3pWJqwvqIbaLYvi4104IBWhm9XdZ7C1jDUmG8DgwbKrXZfg7xxZ/hzPlwEp5Y9ZijD/ReAgRs0Va8/ytWc3AJ+121Q1Ss9U8vD08K5+wg1PVYyNa2YGBpVJbt2ZSt8dvuciWZujFDTzgLvRr6w17kg6+jkiwJyz2ZIL6ytyiUE1oJzsbslIZN3yGHEcmXZZ8Xz5q8fzrLUVmRx1kX6FE2QzRe4+6Q+qNeI8Ct7dj7JBBdbK2Jq+6A==';

    $wopiUrl = 'https://contoso.com/wopi/files/Dy07US/uXVeOMwgyEqGqeVNnyOoaRxR+es2atR08tZPMatf0gf0?access_token=7DHGrfxYtgXxfaXF%2benHF3PTGMrgNlYktkbQB8q%2fn2aQwzYQ6qTmqNJRnJm5QIMXS7WbIxMy0LXaova2h687Md4%2bNTazty3P7HD3j5q9anbCuLsUJHtSXfKANUetLyAjFWq6egtMZJSHzDajO0EaHTeA9M7zJg1j69dEMoLmIbxP03kwAvBrdVQmjFdFryKw';

    $accessToken = RequestHelper::getAccessTokenFromUrl($wopiUrl);

    $result = ProofValidator::isValid(new ProofValidatorInput(
        $accessToken,
        $wopiHeaderTimeStamp,
        $wopiUrl,
        $wopiHeaderProofKey,
        $wopiOldHeaderProofKey
    ));

    expect($result)->toBeFalse();
});
