<!-- @extends('layouts.app')

@section('title', 'Blog & Discussions — AGAU Éditions')

@section('content')
<style>
    .blog-container { max-width: 900px; margin: 0 auto; padding: 48px 20px 80px; }
    .blog-hero { text-align: center; margin-bottom: 48px; }
</style>

<div class="blog-container">
    <div class="blog-hero">
        <h1 style="font-family:'Lora',serif;font-size:2.2rem;color:#0F4A7A;margin-bottom:12px;">
            <i class="bi bi-chat-quote-fill" style="color:#1B6CA8;"></i> Blog & Discussions
        </h1>
        <p style="color:#6b7280;font-size:1rem;max-width:600px;margin:0 auto;">
            Espace libre d'échanges sur la littérature africaine, l'édition et la culture. Participez à la conversation !
        </p>
    </div>

    @include('components.comments-section')
</div>
@endsection

@push('scripts')
<script>
function toggleReplyForm(id) {
    const form = document.getElementById('reply-form-' + id);
    if (form) { form.style.display = form.style.display === 'none' ? 'block' : 'none'; }
}
function toggleEditForm(id) {
    const form = document.getElementById('edit-form-' + id);
    if (form) { form.style.display = form.style.display === 'none' ? 'block' : 'none'; }
}
function showArchiveModal(id) {
    const modal = document.getElementById('archive-modal-' + id);
    if (modal) { modal.style.display = 'flex'; }
}
function closeArchiveModal(id) {
    const modal = document.getElementById('archive-modal-' + id);
    if (modal) { modal.style.display = 'none'; }
}
</script>
@endpush -->
