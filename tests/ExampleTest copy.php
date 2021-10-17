<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Pagination\Cursor;
use App\Models\CreditorInvoiceHeader;
use Illuminate\Foundation\Testing\WithoutMiddleware;

/**
 * @group documentation
 */
class CursorPaginationTest extends TestCase
{
    use WithoutMiddleware;

    public function testItCanCursorPaginateWithNullableColumns(): void
    {
        CreditorInvoiceHeader::factory()->create([
            'marking' => '1337',
            'buyer_ordernumber' => 'the whole description',
            'xml' => '<note></note>'
        ]);

        $firstWithNull = CreditorInvoiceHeader::factory()->create([
            'marking' => null
        ]);

        $secondWithNull = CreditorInvoiceHeader::factory()->create([
            'marking' => null
        ]);

        $thirdWithNull = CreditorInvoiceHeader::factory()->create([
            'marking' => null
        ]);
        CreditorInvoiceHeader::factory()->create([
            'marking' => '1'
        ]);
        CreditorInvoiceHeader::factory()->create([
            'marking' => '4'
        ]);

        $firstPage = CreditorInvoiceHeader::orderBy('marking')->orderBy('id')->cursorPaginateWithNullColumns(2, ['*'], null, null);

        $this->assertCount(2, $firstPage->getCollection()->filter(fn ($row) => $row->marking === null));

        $nextCursor = $firstPage->nextCursor();

        $this->assertEquals([
            'marking' => null,
            'id' => $secondWithNull->id,
            '_pointsToNextItems' => true
        ], $nextCursor->toArray());

        $secondPage = CreditorInvoiceHeader::orderBy('marking')->orderBy('id')->cursorPaginateWithNullColumns(2, ['*'], null, $nextCursor);

        $this->assertCount(1, $secondPage->getCollection()->filter(fn ($row) => $row->marking === null));
        $this->assertCount(1, $secondPage->getCollection()->filter(fn ($row) => $row->marking !== null));
    }

    public function testCursorAndPageIsNotAllowed()
    {
        $this->loginAdmin();

        $query = '
            query {
                creditorInvoices(pagination: {page: 1, cursor: null, limit: 10} ) {
                    items {
                        id
                    }
                }
            }
        ';

        $response = $this->graphQL()->query($query);

        $response->assertJsonValidationErrors([
            'pagination.page' => 'Not allowed if cursor is set'
        ]);
    }

    public function testCursorAndPageIsNotAllowedOppositeWay()
    {
        $this->loginAdmin();

        $query = '
            query {
                creditorInvoices(pagination: {cursor: "ff", page: 1, limit: 10} ) {
                    items {
                        id
                    }
                }
            }
        ';

        $response = $this->graphQL()->query($query);

        $response->assertJsonValidationErrors([
            'pagination.cursor' => 'Not allowed if page is set',
            'pagination.page' => 'Not allowed if cursor is set',
        ]);
    }

    public function testHavingOnlyLimitIsCaughtByValidation()
    {
        $this->loginAdmin();

        $query = '
            query {
                creditorInvoices(pagination: {limit: 10} ) {
                    items {
                        id
                    }
                }
            }
        ';

        $response = $this->graphQL()->query($query);

        $response->assertJsonValidationErrors([
            'pagination.limit' => 'Page or cursor is required when using limit',
        ]);
    }


    public function testCursorExampleOrderDesc()
    {
        $this->loginAdmin();

        $header = CreditorInvoiceHeader::factory()->create([
            'approval_comment' => 'comment1'
        ]);
        
        $null1 = CreditorInvoiceHeader::factory()->create([
            'approval_comment' => null
        ]);

        $null2 = CreditorInvoiceHeader::factory()->create([
            'approval_comment' => null
        ]);

        $query = '
            query ($cursor: String) {
                creditorInvoices(pagination: {cursor: $cursor, limit: 1}, orderBy: [{field: "approvalComment", direction: DESC}]) {
                    items {
                        id
                    }
                    nextCursor
                    previousCursor
                }
            }
        ';

        $response = $this->graphQL()->query($query, [
            'cursor' => null
        ]);
        $response->assertSuccessful();
        $response->assertJsonPath('data.creditorInvoices.items.0.id', (int)$header->id);


        $nextCursor = $response->json('data.creditorInvoices.nextCursor');

        $this->assertEquals([
            'creditor_invoice_header.approval_comment' => $header->approval_comment,
            'creditor_invoice_header.id' => $header->id,
            '_pointsToNextItems' => true
        ], Cursor::fromEncoded($nextCursor)->toArray());

        $response = $this->graphQL()->query($query, [
            'cursor' => $nextCursor
        ]);

        $response->assertJsonPath('data.creditorInvoices.items.0.id', (int)$null2->id);
        $nextCursor = $response->json('data.creditorInvoices.nextCursor');

        $response = $this->graphQL()->query($query, [
            'cursor' => $nextCursor
        ]);

        $response->assertJsonPath('data.creditorInvoices.items.0.id', (int)$null1->id);

        // now go backwards
        $previousCursor = $response->json('data.creditorInvoices.previousCursor');

        $response = $this->graphQL()->query($query, [
            'cursor' => $previousCursor
        ]);

        $response->assertJsonPath('data.creditorInvoices.items.0.id', (int)$null2->id);
        $previousCursor = $response->json('data.creditorInvoices.previousCursor');

        $response = $this->graphQL()->query($query, [
            'cursor' => $previousCursor
        ]);

        $response->assertJsonPath('data.creditorInvoices.items.0.id', (int)$header->id);
    }

    public function testCursorExampleOrderAsc()
    {
        $this->loginAdmin();

        $header = CreditorInvoiceHeader::factory()->create([
            'approval_comment' => '1'
        ]);
        
        $null1 = CreditorInvoiceHeader::factory()->create([
            'approval_comment' => null
        ]);

        $null2 = CreditorInvoiceHeader::factory()->create([
            'approval_comment' => null
        ]);

        $query = '
            query ($cursor: String) {
                creditorInvoices(pagination: {cursor: $cursor, limit: 1}, orderBy: [{field: "approvalComment", direction: ASC}]) {
                    items {
                        id
                        marking
                        approvalComment
                    }
                    nextCursor
                    previousCursor
                }
            }
        ';

        $response = $this->graphQL()->query($query, [
            'cursor' => null
        ]);
        $response->assertSuccessful();

        $response->assertJsonPath('data.creditorInvoices.items.0.id', (int)$null2->id);

        $nextCursor = $response->json('data.creditorInvoices.nextCursor');

        $response = $this->graphQL()->query($query, [
            'cursor' => $nextCursor
        ]);
        $response->assertJsonPath('data.creditorInvoices.items.0.id', (int)$null1->id);
        $nextCursor = $response->json('data.creditorInvoices.nextCursor');

        $response = $this->graphQL()->query($query, [
            'cursor' => $nextCursor
        ]);
        $response->assertJsonPath('data.creditorInvoices.items.0.id', (int)$header->id);
        $nextCursor = $response->json('data.creditorInvoices.nextCursor');
        $this->assertNull($nextCursor);

        // now go backwards
        $previousCursor = $response->json('data.creditorInvoices.previousCursor');

        $response = $this->graphQL()->query($query, [
            'cursor' => $previousCursor
        ]);

        $response->assertJsonPath('data.creditorInvoices.items.0.id', (int)$null1->id);
        $previousCursor = $response->json('data.creditorInvoices.previousCursor');

        $response = $this->graphQL()->query($query, [
            'cursor' => $previousCursor
        ]);

        $response->assertJsonPath('data.creditorInvoices.items.0.id', (int)$null2->id);

    }

}
