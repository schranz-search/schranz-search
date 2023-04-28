<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

final class HomeCest
{
    public function testIndexPage(AcceptanceTester $I): void
    {
        $I->wantTo('home page works.');
        $I->amOnPage('/');
        $I->expectTo('see page home.');
        $I->see('Hello!');
    }

    public function testIndexPageRu(AcceptanceTester $I): void
    {
        $I->wantTo('home page works.');
        $I->amOnPage('/ru/');
        $I->expectTo('see page home.');
        $I->see('Привет!');
    }
}
