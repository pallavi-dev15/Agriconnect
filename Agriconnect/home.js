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
  setInterval(nextSlide, 3000);

  $(".next").click(nextSlide);
  $(".prev").click(prevSlide);

  showSlide(index);
});

$(document).ready(function(){
  let mainText = "Connecting Farmers & Buyers";
  let subText = "Empowering Agriculture Digitally";

  let i = 0;
  let j = 0;

  $("#mainText").text("");
  $("subText").text("");
  function typeMain(){
    if(i < mainText.length){
      $("#mainText").css("opacity", "1");
      $("#mainText").append(mainText.charAt(i));
      i++;
      setTimeout(typeMain, 80);
    } else {
      setTimeout(typeSub, 300);
    }
  }
  function typeSub(){
    if(j < subText.length){
      $("#subText").css("opacity", "1");
      $("#subText").append(subText.charAt(j));
      j++;
      setTimeout(typeSub, 50);
    }
  }
  typeMain();
});