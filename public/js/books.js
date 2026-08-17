/**
 * Exercice : compléter les appels fetch (TODO 4, 5, 6).
 */
(function () {
    const base = window.APP_BASE || '';
    const liste = document.getElementById('liste-livres');
    const form = document.getElementById('form-livre');
    const message = document.getElementById('message');

    function afficherMessage(texte, ok) {
        message.textContent = texte;
        message.className = 'mt-3 text-sm ' + (ok ? 'text-emerald-600' : 'text-rose-600');
    }

    function escapeHtml(valeur) {
        return String(valeur)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function rendu(livres) {
        if (!Array.isArray(livres) || livres.length === 0) {
            liste.innerHTML = '<p class="text-slate-500">Aucun livre.</p>';
            return;
        }

        liste.innerHTML = livres.map(function (livre) {
            const etat = livre.disponible ? 'Disponible' : 'Emprunté';
            return (
                '<article class="flex items-center justify-between gap-3 border border-slate-200 rounded-xl px-4 py-3">' +
                    '<div>' +
                        '<p class="font-semibold text-slate-800">' + escapeHtml(livre.titre) + '</p>' +
                        '<p class="text-slate-500">' + escapeHtml(livre.auteur) + '</p>' +
                    '</div>' +
                    '<div class="flex items-center gap-2 text-sm">' +
                        '<button type="button" data-id="' + livre.id + '" class="btn-toggle underline">' + etat + '</button>' +
                        '<button type="button" data-id="' + livre.id + '" class="btn-supprimer text-rose-600 underline">Supprimer</button>' +
                    '</div>' +
                '</article>'
            );
        }).join('');

        liste.querySelectorAll('.btn-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                toggleLivre(btn.getAttribute('data-id'));
            });
        });
        liste.querySelectorAll('.btn-supprimer').forEach(function (btn) {
            btn.addEventListener('click', function () {
                supprimerLivre(btn.getAttribute('data-id'));
            });
        });
    }

    // TODO 4 — charger la liste depuis l'API
    async function chargerLivres() {
        liste.textContent = 'Chargement…';

        const res = await fetch(base + '/api/books');
        const json = await res.json();

        if (!res.ok) {
            liste.innerHTML = '<p class="text-rose-600">' + (json.error || 'Erreur API') + '</p>';
            return;
        }

        rendu(json.data);
    }

    // TODO 5 — créer un livre via POST
    async function creerLivre(titre, auteur) {
        const res = await fetch(base + '/api/books', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ titre, auteur }),
        });

        const json = await res.json();

        if (!res.ok) {
            throw new Error(json.error || 'Création impossible');
        }
    }

    // TODO 6 — basculer disponible/emprunté puis recharger
    async function toggleLivre(id) {
        const res = await fetch(base + '/api/books/' + id + '/toggle', { method: 'POST' });
        const json = await res.json();

        if (!res.ok) {
            afficherMessage(json.error || 'Toggle impossible', false);
            return;
        }

        await chargerLivres();
    }

    // Bonus — supprimer un livre via DELETE
    async function supprimerLivre(id) {
        const res = await fetch(base + '/api/books/' + id, { method: 'DELETE' });
        const json = await res.json();

        if (!res.ok) {
            afficherMessage(json.error || 'Suppression impossible', false);
            return;
        }

        afficherMessage('Livre supprimé.', true);
        await chargerLivres();
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const data = new FormData(form);
        try {
            await creerLivre(data.get('titre'), data.get('auteur'));
            form.reset();
            afficherMessage('Livre ajouté.', true);
            await chargerLivres();
        } catch (err) {
            afficherMessage(err.message, false);
        }
    });

    chargerLivres();
})();
