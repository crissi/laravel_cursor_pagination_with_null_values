<?php

use Crissi\LaravelCursorPaginationWithNullValues\Database\Models\Customer;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\Cursor;

uses(RefreshDatabase::class);

function paginate($query, ?Cursor $cursor)
{
    return $query->cursorPaginateWithNullValues(1, ['*'], null, $cursor);
}

it('can cursor paginate by ordering desc', function () {
    $customer = Customer::factory()->create([
        'email' => 'mail@mail.dk',
        'name' => 'Hanson',
    ]);

    $null1 = Customer::factory()->create([
        'email' => null,
        'name' => 'Peter',
    ]);

    $null2 = Customer::factory()->create([
        'email' => null,
        'name' => 'Jens',
    ]);

    $page1 = paginate(Customer::orderBy('email', 'DESC')->orderBy('id', 'DESC'), null);
    $this->assertEquals($customer->id, $page1[0]->id);

    $nextCursor = $page1->nextCursor();

    $page2 = paginate(Customer::orderBy('email', 'DESC')->orderBy('id', 'DESC'), $nextCursor);
    $this->assertEquals($null2->id, $page2[0]->id);

    $nextCursor = $page2->nextCursor();

    $page3 = paginate(Customer::orderBy('email', 'DESC')->orderBy('id', 'DESC'), $nextCursor);
    $this->assertEquals($null1->id, $page3[0]->id);
    $this->assertNull($page3->nextCursor());

    // now go backwards
    $previousCursor = $page3->previousCursor();

    $page2Back = paginate(Customer::orderBy('email', 'DESC')->orderBy('id', 'DESC'), $previousCursor);
    $this->assertEquals($null2->id, $page2Back[0]->id);

    $previousCursor = $page2Back->previousCursor();

    $page1Back = paginate(Customer::orderBy('email', 'DESC')->orderBy('id', 'DESC'), $previousCursor);
    $this->assertEquals($customer->id, $page1Back[0]->id);

    $this->assertNull($page1Back->previousCursor());
});

it('can cursor paginate by ordering asc', function () {
    $customer = Customer::factory()->create([
        'email' => 'mail@mail.dk',
        'name' => 'Hanson',
    ]);

    $null1 = Customer::factory()->create([
        'email' => null,
        'name' => 'Peter',
    ]);

    $null2 = Customer::factory()->create([
        'email' => null,
        'name' => 'Jens',
    ]);

    $page1 = paginate(Customer::orderBy('email', 'ASC')->orderBy('id', 'ASC'), null);

    $this->assertEquals($null1->id, $page1[0]->id);

    $nextCursor = $page1->nextCursor();

    $page2 = paginate(Customer::orderBy('email', 'ASC')->orderBy('id', 'ASC'), $nextCursor);
    $this->assertEquals($null2->id, $page2[0]->id);

    $nextCursor = $page2->nextCursor();

    $page3 = paginate(Customer::orderBy('email', 'ASC')->orderBy('id', 'ASC'), $nextCursor);
    $this->assertEquals($customer->id, $page3[0]->id);
    $this->assertNull($page3->nextCursor());

    // now go backwards
    $previousCursor = $page3->previousCursor();

    $page2Back = paginate(Customer::orderBy('email', 'ASC')->orderBy('id', 'ASC'), $previousCursor);
    $this->assertEquals($null2->id, $page2Back[0]->id);

    $previousCursor = $page2Back->previousCursor();

    $page1Back = paginate(Customer::orderBy('email', 'ASC')->orderBy('id', 'ASC'), $previousCursor);
    $this->assertEquals($null1->id, $page1Back[0]->id);

    $this->assertNull($page1Back->previousCursor());
});
