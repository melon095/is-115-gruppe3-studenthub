const FORMATER = {
    dag: { day: "numeric" },
    maned: { month: "short" },
    full: { dateStyle: "full" }
};

function formaterDatoer(rot) {
    const sprak = document.documentElement.lang || "nb";

    rot.querySelectorAll("time[data-format]").forEach((element) => {
        const format = FORMATER[element.dataset.format];
        const dato = new Date(element.dateTime + "T00:00:00");

        if (!format || Number.isNaN(dato.getTime())) {
            return;
        }

        element.textContent = new Intl.DateTimeFormat(sprak, format).format(dato);
    });
}

(async function() {
    const kalender = document.querySelector("[data-kalender-url]");

    if (!kalender) {
        return;
    }

    try {
        const respons = await fetch(kalender.dataset.kalenderUrl, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        });

        if (!respons.ok) {
            throw new Error("Uventet svar: " + respons.status);
        }

        kalender.innerHTML = await respons.text();

        formaterDatoer(kalender);
    } catch (feil) {
        console.error("Kunne ikke laste kalender:", feil);
        kalender.textContent = "Kunne ikke laste kalenderen.";
    } finally {
        kalender.removeAttribute("aria-busy");
    }
})();
