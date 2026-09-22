/* The period tabs above the standing chart on the homepage.
 *
 * PHP renders all three panels; the two that are not current carry `hidden`.
 * Without JavaScript the first period stays visible and nothing looks broken,
 * the buttons just do nothing. */

document.addEventListener("DOMContentLoaded", () => {
    const tablist = document.querySelector(".dashboard__tabs");

    if (!tablist) {
        return;
    }

    const tabs = Array.from(tablist.querySelectorAll("[role=tab]"));

    function select(tab) {
        tabs.forEach((item) => {
            const isCurrent = item === tab;
            const panel = document.getElementById(item.getAttribute("aria-controls"));

            item.setAttribute("aria-selected", String(isCurrent));
            item.classList.toggle("is-active", isCurrent);

            /* Only the current tab is in the tab order, so Tab steps over the
               group instead of through every button in it. */
            item.tabIndex = isCurrent ? 0 : -1;

            if (panel) {
                panel.hidden = !isCurrent;
            }
        });
    }

    tablist.addEventListener("click", (event) => {
        const tab = event.target.closest("[role=tab]");

        if (tab) {
            select(tab);
        }
    });

    tablist.addEventListener("keydown", (event) => {
        const current = tabs.indexOf(document.activeElement);

        if (current === -1) {
            return;
        }

        const target = {
            ArrowLeft: current - 1,
            ArrowRight: current + 1,
            Home: 0,
            End: tabs.length - 1,
        }[event.key];

        if (target === undefined) {
            return;
        }

        event.preventDefault();

        const tab = tabs[(target + tabs.length) % tabs.length];
        tab.focus();
        select(tab);
    });

    /* Once on load, so the tab order is right from the start. */
    select(tabs.find((tab) => tab.getAttribute("aria-selected") === "true") || tabs[0]);
});
