//button filter functions to change some 'your-position' section text elements
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


//button filter functions for ranking section; only shows the section you clicked on
function showClassRanking() {
    document.getElementById("class-ranking").style.display = "block";
    document.getElementById("year-group-ranking").style.display = "none";
    document.getElementById("school-ranking").style.display = "none";
}

function showYearGroupRanking() {
    document.getElementById("class-ranking").style.display = "none";
    document.getElementById("year-group-ranking").style.display = "block";
    document.getElementById("school-ranking").style.display = "none";
}

function showSchoolRanking() {
    document.getElementById("class-ranking").style.display = "none";
    document.getElementById("year-group-ranking").style.display = "none";
    document.getElementById("school-ranking").style.display = "block";
}