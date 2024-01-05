console.log("init");

const slideHeight = document.querySelector(".games").clientHeight + 50;
const carousel = document.querySelector("#carousel");

// SLIDER BTN

const sliderBtns = document.querySelectorAll('#slider .slider-btn');
let clicked = false;

for (let i = 0 ; i < sliderBtns.length ; i++) {
    sliderBtns[i].addEventListener("click", (e) => {
        e.preventDefault();
        carousel.scrollTo({
            top: slideHeight * i
        });
        document.querySelector('.current-slide').classList.toggle('current-slide');
        sliderBtns[i].classList.toggle('current-slide');
        clicked = true;
    });
}

carousel.addEventListener("scroll", function(e) {
    setTimeout(function() {
        for (let i = 0 ; i < sliderBtns.length ; i++) {
            if (carousel.scrollTop == slideHeight * i) {
                document.querySelector('.current-slide').classList.toggle('current-slide');
                sliderBtns[i].classList.toggle('current-slide');
            }
        }
    }, 380);
});