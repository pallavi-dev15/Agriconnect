$(document).ready(function(){
  let index = 0;
  let slides = $(".slide");

  function showSlide(i){
    slides.hide();
    slides.eq(i).fadeIn();
  }

  function nextSlide(){
    index = (index + 1) % slides.length;
    showSlide(index);
  }

  function prevSlide(){
    index = (index - 1 + slides.length) % slides.length;
    showSlide(index);
  }

  // Auto slideshow
  setInterval(nextSlide, 3000);

  // Manual controls
  $(".next").click(nextSlide);
  $(".prev").click(prevSlide);

  // Initial
  showSlide(index);
});