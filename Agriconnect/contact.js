$(document).ready(function(){

  $("input").focus(function(){
    $(this).css({
      "border": "5px solid #7af782a4",
      "box-shadow": "0 0 10px rgba(47, 122, 52, 0.94)"
    });
  });

  $("input").blur(function(){
    $(this).css({
      "border": "1px solid #111111",
      "box-shadow": "none"
    });
  });

});