/* Opens and closes the menu panel. The list of pages inside it is a <details>
   and needs nothing from here. */

document.addEventListener("DOMContentLoaded", () => {
    const panel = document.getElementById("main-menu");
    const toggle = document.querySelector("[data-menu-toggle]");

    if (!panel || !toggle) {
        return;
    }

    let isOpen = false;

    /* Marking the rest of the page inert is the whole focus trap: the browser
       then refuses to move focus into it, and blocks clicks on it as well.
       Looked up per call, because the bottom nav may still be empty. */
    function background() {
        return [
            document.querySelector(".topbar"),
            document.querySelector(".page"),
            document.querySelector(".bottom-nav"),
        ].filter(Boolean);
    }

    function setOpen(open) {
        isOpen = open;
        toggle.setAttribute("aria-expanded", String(open));
        document.body.classList.toggle("is-menu-open", open);

        if (open) {
            panel.dataset.open = "true";
        } else {
            delete panel.dataset.open;
        }

        background().forEach((element) => {
            element.inert = open;
        });
    }

    function open() {
        if (isOpen) {
            return;
        }

        setOpen(true);
        panel.querySelector(".menu__close").focus();
    }

    function close() {
        if (!isOpen) {
            return;
        }

        /* setOpen lifts inert off the topbar first, otherwise the button
           cannot take focus back. */
        setOpen(false);
        toggle.focus();
    }

    toggle.addEventListener("click", () => (isOpen ? close() : open()));

    document.querySelectorAll("[data-menu-close]").forEach((element) => {
        element.addEventListener("click", close);
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            close();
        }
    });
});
