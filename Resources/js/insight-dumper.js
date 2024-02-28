document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.insight-dump-toggle').forEach(function (element) {
        element.addEventListener('click', function () {
            const content = this.nextElementSibling; // Assumant que le contenu est toujours juste après l'élément cliquable
            if (content.style.display === 'none') {
                content.style.display = ''; // Affiche le contenu
            } else {
                content.style.display = 'none'; // Cache le contenu
            }
        });
    });
});
