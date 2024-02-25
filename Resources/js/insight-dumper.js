function toggleArrayDisplay(element) {
    const content = element.previousElementSibling; // Récupère le div .insight-dump-array-content
    if (content.style.display === 'none') {
        content.style.display = 'block'; // Affiche le contenu du tableau
        element.innerText = '[-]'; // Change le texte pour indiquer que le tableau est ouvert
    } else {
        content.style.display = 'none'; // Cache le contenu du tableau
        element.innerText = '[ ... ]'; // Reviens au texte initial pour indiquer que le tableau est fermé
    }
}
