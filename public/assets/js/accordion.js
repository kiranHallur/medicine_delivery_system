let accordion = document.getElementsByClassName("custom_accordionordion");
let accordion_index;
for (accordion_index = 0; accordion_index < accordion.length; accordion_index++) {
  accordion[accordion_index].addEventListener("click", function() {
    this.classList.toggle("active");
    let panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
        panel.style.maxHeight = null;
    } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  });
}