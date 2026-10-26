<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryInfoRequest;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Voir le contenu du panier.
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Déterminer le type de panier (utile pour affichage badge)
        $cartType = $this->detectCartType($cart);

        return view('front.cart.index', compact('cart', 'total', 'cartType'));
    }

    /**
     * Ajouter un livre au panier.
     * Règles :
     *  - Le panier ne peut contenir que des livres du même type (physique OU numérique).
     *  - Un livre physique doit être en stock.
     */
    public function add(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $cart = session()->get('cart', []);

        // Déterminer le type d'article souhaité (physique ou numerique)
        $requestedType = $request->input('type');
        if (! $requestedType) {
            $requestedType = $book->isPhysique() ? 'physique' : 'numerique';
        }

        $isPhysiqueItem = ($requestedType === 'physique');

        // Vérification du stock pour les livres physiques
        if ($isPhysiqueItem && ! $book->isInStock()) {
            return redirect()->back()->with('error', 'Le livre "'.$book->titre.'" n\'est pas disponible en stock pour une commande physique.');
        }

        // Vérification de la cohérence du type de panier
        if (! empty($cart)) {
            $cartType = $this->detectCartType($cart);

            if ($cartType !== $requestedType) {
                $typeLabel = $cartType === 'physique' ? 'livres physiques (livraison à domicile)' : 'livres numériques (téléchargement instantané)';
                $oppositeLabel = $requestedType === 'physique' ? 'physique' : 'numérique';

                return redirect()->back()->with(
                    'error',
                    "Votre panier contient déjà des {$typeLabel}. Il est impossible de mélanger commandes physiques et numériques. Veuillez finaliser ou vider votre panier actuel avant d'ajouter un livre {$oppositeLabel}."
                );
            }
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id' => $book->id,
                'title' => $book->titre,
                'quantity' => 1,
                'price' => $book->prix,
                'cover_color' => $book->couleur_couverture,
                'cover_url' => $book->cover_url,
                'author' => $book->author->nom ?? 'Inconnu',
                'type' => $requestedType,
            ];
        }

        session()->put('cart', $cart);

        $label = $requestedType === 'physique' ? 'format physique (livraison)' : 'format numérique (PDF)';

        return redirect()->back()->with('success', 'Le livre "'.$book->titre.'" ('.$label.') a été ajouté au panier !');
    }

    /**
     * Mettre à jour la quantité d'un article.
     */
    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);

            return response()->json(['success' => true]);
        }
    }

    /**
     * Supprimer un article du panier.
     */
    public function remove(Request $request)
    {
        $id = $request->input('id');
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            return redirect()->back()->with('success', 'Article supprimé du panier.');
        }

        return redirect()->back()->with('error', 'Article introuvable dans le panier.');
    }

    /**
     * Afficher le formulaire d'adresse de livraison (uniquement pour les commandes physiques).
     */
    public function deliveryForm()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Le panier est vide.');
        }

        if ($this->detectCartType($cart) !== 'physique') {
            return redirect()->route('cart.index')->with('error', 'Ce formulaire est réservé aux commandes de livres physiques.');
        }

        return view('front.cart.delivery');
    }

    /**
     * Enregistrer les infos de livraison en session, puis rediriger vers le checkout.
     */
    public function storeDeliveryInfo(StoreDeliveryInfoRequest $request)
    {
        session()->put('delivery_info', $request->validated());

        return $this->checkout($request);
    }

    /**
     * Passer du panier à une commande réelle (Order + OrderItems).
     */
    public function checkout(Request $request)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('cart.index')->with('error', 'Veuillez utiliser le bouton "Passer à la caisse" depuis votre panier.');
        }

        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour passer commande.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Le panier est vide.');
        }

        $cartType = $this->detectCartType($cart);

        // Pour les commandes physiques : vérifier que les infos de livraison sont en session
        if ($cartType === 'physique' && ! session()->has('delivery_info')) {
            return redirect()->route('cart.delivery');
        }

        // Valider les produits et recalculer le total
        $validatedCart = [];
        $total = 0;

        foreach ($cart as $product) {
            $book = Book::find($product['id']);

            if (! $book) {
                session()->forget('cart');

                return redirect()->route('cart.index')->with('error', 'Un produit du panier n\'existe plus. Votre panier a été vidé.');
            }

            // Re-vérifier le stock pour les livres physiques
            if ($book->isPhysique() && ! $book->isInStock()) {
                return redirect()->route('cart.index')->with('error', 'Le livre "'.$book->titre.'" n\'est plus en stock. Veuillez le retirer de votre panier.');
            }

            $validatedCart[$product['id']] = [
                'id' => $book->id,
                'title' => $book->titre,
                'quantity' => $product['quantity'] ?? 1,
                'price' => $book->prix,
                'physique' => $book->isPhysique(),
            ];
            $total += $book->prix * ($product['quantity'] ?? 1);
        }

        $deliveryInfo = session()->get('delivery_info', []);

        DB::transaction(function () use ($user, $validatedCart, $total, $cartType, $deliveryInfo, &$order) {
            $order = Order::create([
                'user_id' => $user->id,
                'statut' => 'en_attente',
                'total' => $total,
                'type_commande' => $cartType,
                'nom_destinataire' => $deliveryInfo['nom_destinataire'] ?? null,
                'telephone_livraison' => $deliveryInfo['telephone_livraison'] ?? null,
                'adresse_livraison' => $deliveryInfo['adresse_livraison'] ?? null,
                'ville_livraison' => $deliveryInfo['ville_livraison'] ?? null,
                'pays_livraison' => $deliveryInfo['pays_livraison'] ?? null,
                'notes_livraison' => $deliveryInfo['notes_livraison'] ?? null,
            ]);

            foreach ($validatedCart as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $product['id'],
                    'quantite' => $product['quantity'],
                    'prix_unitaire' => $product['price'],
                ]);

                // Décrémenter le stock pour les livres physiques
                if ($product['physique']) {
                    Book::where('id', $product['id'])->decrement('stock', $product['quantity']);
                }
            }
        });

        session()->put('last_order_id', $order->id);
        session()->forget(['cart', 'delivery_info']);

        $subtotal = $total;

        return view('front.cart.confirm', compact('cart', 'order', 'subtotal', 'cartType'));
    }

    /**
     * Confirmer la commande et procéder au paiement.
     */
    public function confirmCheckout(Request $request, Order $order)
    {
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Accès refusé.');
        }

        $method = $request->input('payment_method', 'fedapay');

        if ($method === 'transfert_manuel') {
            return redirect()->route('payment.manual.instructions', ['order' => $order->id]);
        }

        return redirect()->route('payment.pay', ['order' => $order->id]);
    }

    /**
     * Détecter le type de panier à partir de son contenu.
     *
     * @param  array<int|string, array<string, mixed>>  $cart
     */
    private function detectCartType(array $cart): string
    {
        foreach ($cart as $item) {
            if (($item['type'] ?? 'numerique') === 'physique') {
                return 'physique';
            }
        }

        return 'numerique';
    }
}
