function changeContent(elementId, callback) {
    const element = document.getElementById(elementId);

    // Fade out
    element.classList.add("opacity-0");

    setTimeout(() => {
        // this is what makes it change
        callback(element);

        // Fade in
        element.classList.remove("opacity-0");
    }, 300);
}