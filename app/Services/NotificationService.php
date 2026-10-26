<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Manuscript;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Récupère la liste des notifications dynamiques non lues pour l'utilisateur connecté.
     */
    public function getNotificationsForUser(?User $user = null): Collection
    {
        $user = $user ?? Auth::user();

        if (! $user) {
            return collect();
        }

        $notifications = collect();
        $readIds = session()->get('read_notifications', []);

        // 1. Notifications Administrateur
        if ($user->role === 'admin') {
            // Commandes en attente de vérification
            $pendingOrders = Order::whereIn('statut', ['en_attente_verification', 'en_attente'])
                ->latest()
                ->take(5)
                ->get();

            foreach ($pendingOrders as $order) {
                $notifications->push([
                    'id' => 'order_'.$order->id,
                    'title' => 'Commande à valider #'.$order->id,
                    'message' => 'Paiement manuel de '.number_format($order->total, 0, ',', ' ').' FCFA en attente de vérification.',
                    'time' => $order->created_at ? $order->created_at->diffForHumans() : 'Récemment',
                    'icon' => 'bi-credit-card-2-front',
                    'color' => 'amber',
                    'url' => route('admin.orders.show', $order),
                ]);
            }

            // Manuscrits reçus en attente
            $pendingManuscripts = Manuscript::where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();

            foreach ($pendingManuscripts as $manuscript) {
                $notifications->push([
                    'id' => 'manuscript_'.$manuscript->id,
                    'title' => 'Nouveau Manuscrit Reçu',
                    'message' => '"'.$manuscript->titre.'" soumis par '.$manuscript->auteur_nom,
                    'time' => $manuscript->created_at ? $manuscript->created_at->diffForHumans() : 'Récemment',
                    'icon' => 'bi-journal-arrow-up',
                    'color' => 'blue',
                    'url' => route('admin.manuscripts.show', $manuscript->id),
                ]);
            }

            // Commentaires signalés
            $flaggedComments = Comment::where('is_flagged', true)
                ->latest()
                ->take(3)
                ->get();

            foreach ($flaggedComments as $comment) {
                $notifications->push([
                    'id' => 'comment_'.$comment->id,
                    'title' => 'Commentaire Signalé',
                    'message' => 'Un commentaire a été signalé pour modération : "'.Str::limit($comment->contenu, 50).'"',
                    'time' => $comment->updated_at ? $comment->updated_at->diffForHumans() : 'Récemment',
                    'icon' => 'bi-exclamation-triangle',
                    'color' => 'rose',
                    'url' => route('admin.moderation.flagged'),
                ]);
            }
        }

        // 2. Notifications Client & Auteur (Commandes personnelles)
        $userOrders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        foreach ($userOrders as $order) {
            $statusLabel = match ($order->statut) {
                'confirmée' => 'Paiement confirmé ! Votre commande a été validée.',
                'en_route' => 'Votre colis est en route avec le livreur.',
                'livrée' => 'Commande livrée avec succès. Bonne lecture !',
                'annulée' => 'Votre commande a été annulée.',
                default => 'Commande enregistrée, en cours de traitement.',
            };

            $icon = match ($order->statut) {
                'confirmée' => 'bi-check-circle-fill',
                'en_route' => 'bi-truck',
                'livrée' => 'bi-box-seam-fill',
                default => 'bi-bag-check',
            };

            $color = match ($order->statut) {
                'confirmée', 'livrée' => 'emerald',
                'en_route' => 'blue',
                'annulée' => 'red',
                default => 'amber',
            };

            $notifications->push([
                'id' => 'user_order_'.$order->id,
                'title' => 'Commande #'.$order->id,
                'message' => $statusLabel,
                'time' => $order->updated_at ? $order->updated_at->diffForHumans() : 'Récemment',
                'icon' => $icon,
                'color' => $color,
                'url' => route('client.orders'),
            ]);
        }

        // 3. Notifications Auteur (Manuscrits personnels)
        if ($user->role === 'author') {
            $authorManuscripts = Manuscript::where('user_id', $user->id)
                ->latest()
                ->take(3)
                ->get();

            foreach ($authorManuscripts as $manuscript) {
                if ($manuscript->status !== 'pending') {
                    $notifications->push([
                        'id' => 'author_ms_'.$manuscript->id,
                        'title' => 'Statut du Manuscrit',
                        'message' => 'Votre manuscrit "'.$manuscript->titre.'" est maintenant : '.strtoupper($manuscript->status),
                        'time' => $manuscript->updated_at ? $manuscript->updated_at->diffForHumans() : 'Récemment',
                        'icon' => 'bi-journal-check',
                        'color' => $manuscript->status === 'accepted' ? 'emerald' : 'rose',
                        'url' => route('client.candidatures'),
                    ]);
                }
            }
        }

        // Filtrer les notifications marquées comme lues
        return $notifications
            ->reject(fn ($notif) => in_array($notif['id'], $readIds))
            ->sortByDesc('time')
            ->values();
    }

    /**
     * Marque une notification spécifique comme lue.
     */
    public function markAsRead(string $notificationId): void
    {
        $read = session()->get('read_notifications', []);
        if (! in_array($notificationId, $read)) {
            $read[] = $notificationId;
            session()->put('read_notifications', $read);
        }
    }

    /**
     * Marque toutes les notifications courantes comme lues.
     */
    public function markAllAsRead(?User $user = null): void
    {
        $all = $this->getNotificationsForUser($user);
        $read = session()->get('read_notifications', []);
        foreach ($all as $notif) {
            if (! in_array($notif['id'], $read)) {
                $read[] = $notif['id'];
            }
        }
        session()->put('read_notifications', $read);
    }
}
