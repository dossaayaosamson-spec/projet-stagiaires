<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Contract;
use App\Models\Manuscript;
use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractAndPayoutModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_has_financial_attributes_and_relations(): void
    {
        $author = Author::factory()->create([
            'solde_disponible' => 25000.50,
            'total_gagne' => 45000.00,
            'total_retire' => 20000.00,
        ]);

        $this->assertEquals(25000.50, $author->solde_disponible);
        $this->assertEquals(45000.00, $author->total_gagne);
        $this->assertEquals(20000.00, $author->total_retire);

        $contract = Contract::factory()->create(['author_id' => $author->id]);
        $payout = PayoutRequest::factory()->create(['author_id' => $author->id]);

        $this->assertTrue($author->contracts->contains($contract));
        $this->assertTrue($author->payoutRequests->contains($payout));
    }

    public function test_contract_relations_and_active_scope(): void
    {
        $author = Author::factory()->create();
        $book = Book::create([
            'titre' => 'Roman Test',
            'auteur' => 'Nom Auteur',
            'author_id' => $author->id,
            'prix' => 5000,
            'disponibilite' => 'numérique',
        ]);
        $manuscript = Manuscript::create([
            'title' => 'Manuscrit Test',
            'file_path' => 'test.pdf',
            'status' => 'accepted',
        ]);

        $activeContract = Contract::factory()->active()->create([
            'author_id' => $author->id,
            'book_id' => $book->id,
            'manuscript_id' => $manuscript->id,
            'type_contrat' => 'compte_editeur',
            'taux_redevance' => 12.50,
        ]);

        $draftContract = Contract::factory()->create([
            'author_id' => $author->id,
            'statut' => 'brouillon',
        ]);

        $this->assertEquals($author->id, $activeContract->author->id);
        $this->assertEquals($book->id, $activeContract->book->id);
        $this->assertEquals($manuscript->id, $activeContract->manuscript->id);
        $this->assertTrue($activeContract->isActive());

        $this->assertEquals($activeContract->id, $book->activeContract->id);

        $activeContracts = Contract::active()->get();
        $this->assertTrue($activeContracts->contains($activeContract));
        $this->assertFalse($activeContracts->contains($draftContract));
    }

    public function test_payout_request_relations_and_scopes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = Author::factory()->create();

        $pendingPayout = PayoutRequest::factory()->create([
            'author_id' => $author->id,
            'montant' => 15000,
            'moyen_paiement' => 'tmoney',
            'coordonnees_paiement' => '+228 90 12 34 56',
            'statut' => 'en_attente',
        ]);

        $validatedPayout = PayoutRequest::factory()->validated()->create([
            'author_id' => $author->id,
            'validated_by' => $admin->id,
        ]);

        $this->assertEquals($author->id, $pendingPayout->author->id);
        $this->assertEquals($admin->id, $validatedPayout->validator->id);

        $pendingList = PayoutRequest::pending()->get();
        $this->assertTrue($pendingList->contains($pendingPayout));
        $this->assertFalse($pendingList->contains($validatedPayout));

        $validatedList = PayoutRequest::validated()->get();
        $this->assertTrue($validatedList->contains($validatedPayout));
        $this->assertFalse($validatedList->contains($pendingPayout));
    }

    public function test_manuscript_submission_source_scopes(): void
    {
        $candidature = Manuscript::create([
            'submission_source' => 'candidature_auteur',
            'title' => 'Candidature 1',
            'file_path' => 'file1.pdf',
        ]);

        $lecthor = Manuscript::create([
            'submission_source' => 'lecthor_public',
            'title' => 'Audit Lecthor',
            'file_path' => 'file2.pdf',
        ]);

        $contrat = Manuscript::create([
            'submission_source' => 'auteur_contrat',
            'title' => 'Nouveau Livre Contrat',
            'file_path' => 'file3.pdf',
        ]);

        $candidatures = Manuscript::candidatures()->get();
        $this->assertTrue($candidatures->contains($candidature));
        $this->assertFalse($candidatures->contains($lecthor));

        $publics = Manuscript::lecthorPublic()->get();
        $this->assertTrue($publics->contains($lecthor));
        $this->assertFalse($publics->contains($contrat));

        $contrats = Manuscript::auteurContrat()->get();
        $this->assertTrue($contrats->contains($contrat));
        $this->assertFalse($contrats->contains($candidature));
    }
}
