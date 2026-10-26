import './bootstrap';
import Swiper from 'swiper/bundle';

window.Swiper = Swiper;

document.addEventListener('submit', function(e) {
    if (e.target && e.target.classList.contains('favoris-form')) {
        e.preventDefault();
        
        const form = e.target;
        const button = form.querySelector('button');
        const url = form.action;
        const formData = new FormData(form);
        
        const isFavori = button.innerText.trim() === '♥';
        button.innerText = isFavori ? '♡' : '♥';
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Erreur réseau');
            return response.json();
        })
        .then(data => {
            button.innerText = data.est_favori ? '♥' : '♡';
        })
        .catch(error => {
            console.error('Erreur Favoris:', error);
            button.innerText = isFavori ? '♥' : '♡';
        });
    }
});
