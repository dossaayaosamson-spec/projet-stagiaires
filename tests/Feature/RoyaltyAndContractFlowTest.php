<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Contract;
use App\Models\Manuscript;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PayoutRequest;
use App\Models\User;
use App\Services\RoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoyaltyAndContractFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_accept_candidate_and_create_author_and_contract(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $candidate = User::factory()->create([
            'role' => 'client',
            'prenom' => 'Kofi',
            'nom_famille' => 'Mensah',
        ]);

        $manuscript = Manuscript::create([
            'user_id' => $candidate->id,
            'submission_source' => 'candidature_auteur',
            'title' => 'Les Lueurs du Sud',
            'author_name' => 'Kofi Mensah',
            'pen_name' => 'K. Mensah Alias',
            'category' => 'Roman',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.manuscripts.update', $manuscript), [
                'status' => 'accepted',
                'admin_notes' => 'Candidature retenue par le comité.',
                'type_contrat' => 'compte_editeur',
                'taux_redevance' => 12.5,
            ]);

        $response->assertRedirect(route('admin.manuscripts.show', $manuscript));

        // Vérifier la promotion de l'utilisateur
        $this->assertEquals('auteur', $candidate->fresh()->role);

        // Vérifier la création du profil auteur
        $author = Author::where('user_id', $candidate->id)->first();
        $this->assertNotNull($author);
        $this->assertEquals('K. Mensah Alias', $author->nom);

        // Vérifier la création du contrat
        $contract = Contract::where('manuscript_id', $manuscript->id)->first();
        $this->assertNotNull($contract);
        $this->assertEquals('compte_editeur', $contract->type_contrat);
        $this->assertEquals(12.5, $contract->taux_redevance);
        $this->assertEquals('actif', $contract->statut);
    }

    public function test_royalty_service_calculates_and_credits_royalties_on_paid_order(): void
    {
        $authorUser = User::factory()->create(['role' => 'auteur']);
        $author = Author::create([
            'user_id' => $authorUser->id,
            'nom' => 'Auteur Vedette',
            'initiales' => 'AV',
            'solde_disponible' => 0,
            'total_gagne' => 0,
            'total_retire' => 0,
        ]);

        $book = Book::create([
            'titre' => 'Le Chant des Collines',
            'author_id' => $author->id,
            'prix' => 5000,
            'disponibilite' => 'disponible',
        ]);

        // Contrat avec 10% de redevance
        Contract::create([
            'author_id' => $author->id,
            'book_id' => $book->id,
            'type_contrat' => 'compte_editeur',
            'taux_redevance' => 10.0,
            'date_debut' => now()->toDateString(),
            'statut' => 'actif',
        ]);

        $buyer = User::factory()->create();
        $order = Order::create([
            'user_id' => $buyer->id,
            'total' => 10000,
            'statut' => 'en_attente',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'book_id' => $book->id,
            'quantite' => 2,
            'prix_unitaire' => 5000,
        ]);

        // Exécuter le service de calcul des royalties
        $service = new RoyaltyService();
        $service->processOrderRoyalties($order);

        // 2 * 5000 * (10 / 100) = 1000 FCFA
        $author->refresh();
        $this->assertEquals(1000, $author->solde_disponible);
        $this->assertEquals(1000, $author->total_gagne);
    }

    public function test_author_can_request_payout_and_admin_can_approve_it(): void
    {
        $authorUser = User::factory()->create(['role' => 'auteur']);
        $author = Author::create([
            'user_id' => $authorUser->id,
            'nom' => 'Auteur Bénéficiaire',
            'initiales' => 'AB',
            'solde_disponible' => 25000,
            'total_gagne' => 25000,
            'total_retire' => 0,
        ]);

        // 1. Demande de versement par l'auteur
        $response = $this->actingAs($authorUser)
            ->post(route('author.payout.request'), [
                'montant' => 15000,
                'moyen_paiement' => 'tmoney',
                'coordonnees_paiement' => '+228 90 00 00 00',
            ]);

        $response->assertRedirect(route('author.dashboard'));

        $payout = PayoutRequest::where('author_id', $author->id)->first();
        $this->assertNotNull($payout);
        $this->assertEquals(15000, $payout->montant);
        $this->assertEquals('en_attente', $payout->statut);

        // 2. Validation par l'administrateur
        $admin = User::factory()->create(['role' => 'admin']);

        $approveResponse = $this->actingAs($admin)
            ->post(route('admin.payouts.approve', $payout), [
                'reference_transaction' => 'TM_CONFIRM_998877',
                'admin_notes' => 'Transfert T-Money exécuté.',
            ]);

        $approveResponse->assertRedirect(route('admin.payouts.index'));

        $payout->refresh();
        $author->refresh();

        $this->assertEquals('valide', $payout->statut);
        $this->assertEquals('TM_CONFIRM_998877', $payout->reference_transaction);
        $this->assertEquals(10000, $author->solde_disponible); // 25000 - 15000
        $this->assertEquals(15000, $author->total_retire);
    }
}
