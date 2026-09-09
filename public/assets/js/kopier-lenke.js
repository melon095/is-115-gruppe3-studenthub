(function() {
    document.querySelectorAll("[data-kopier-mal]").forEach((knapp) => {
        const mal = document.getElementById(knapp.dataset.kopierMal);
        if (!mal) {
            return;
        }

        const opprinneligTekst = knapp.textContent;

        knapp.addEventListener("click", async () => {
            try {
                await navigator.clipboard.writeText(mal.value);
                knapp.textContent = "Kopiert!";
            } catch {
                mal.select();
                knapp.textContent = "Kunne ikke kopiere";
            }

            setTimeout(() => {
                knapp.textContent = opprinneligTekst;
            }, 2000);
        });
    });
})();
