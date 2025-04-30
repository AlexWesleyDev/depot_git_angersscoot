
// Vérification dynamique du mot de passe
const champMotDePasse = document.getElementById('motdepasse');
const remplissageSecurite = document.getElementById('remplissage-securite');
const texteSecurite = document.getElementById('texte-securite');

const critereLongueur = document.getElementById('longueur');
const critereMajuscule = document.getElementById('majuscule');
const critereChiffre = document.getElementById('chiffre');
const critereSpecial = document.getElementById('special');

const iconeAfficherMotDePasse = document.getElementById('afficherMotDePasse');

champMotDePasse.addEventListener('input', function() {
    const motdepasse = champMotDePasse.value;
    let securite = 0;

    if (motdepasse.length >= 6) {
        securite++;
        critereLongueur.classList.replace('invalide', 'valide');
        critereLongueur.textContent = '✅ Minimum 6 caractères';
    } else {
        critereLongueur.classList.replace('valide', 'invalide');
        critereLongueur.textContent = '❌ Minimum 6 caractères';
    }

    if (/[A-Z]/.test(motdepasse)) {
        securite++;
        critereMajuscule.classList.replace('invalide', 'valide');
        critereMajuscule.textContent = '✅ 1 Majuscule';
    } else {
        critereMajuscule.classList.replace('valide', 'invalide');
        critereMajuscule.textContent = '❌ 1 Majuscule';
    }

    if (/[0-9]/.test(motdepasse)) {
        securite++;
        critereChiffre.classList.replace('invalide', 'valide');
        critereChiffre.textContent = '✅ 1 Chiffre';
    } else {
        critereChiffre.classList.replace('valide', 'invalide');
        critereChiffre.textContent = '❌ 1 Chiffre';
    }

    if (/[\W]/.test(motdepasse)) {
        securite++;
        critereSpecial.classList.replace('invalide', 'valide');
        critereSpecial.textContent = '✅ 1 Caractère spécial';
    } else {
        critereSpecial.classList.replace('valide', 'invalide');
        critereSpecial.textContent = '❌ 1 Caractère spécial';
    }

    const pourcentage = (securite / 4) * 100;
    remplissageSecurite.style.width = pourcentage + '%';

    if (pourcentage <= 25) {
        remplissageSecurite.style.backgroundColor = 'red';
        texteSecurite.textContent = 'Mot de passe insuffisant';
    } else if (pourcentage <= 50) {
        remplissageSecurite.style.backgroundColor = 'orange';
        texteSecurite.textContent = 'Mot de passe faible';
    } else if (pourcentage < 100) {
        remplissageSecurite.style.backgroundColor = 'yellowgreen';
        texteSecurite.textContent = 'Mot de passe fort';
    } else {
        remplissageSecurite.style.backgroundColor = 'green';
        texteSecurite.textContent = 'Mot de passe très fort';
    }
});

// Gestion de l'affichage/masquage du mot de passe
iconeAfficherMotDePasse.addEventListener('click', function() {
    const type = champMotDePasse.getAttribute('type') === 'password' ? 'text' : 'password';
    champMotDePasse.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
