 $(document).ready(function(){
    $(".input_ani").focusin(function(){
    $(this).next().addClass("active");
    });
     $(".input_ani").focusout(function(){
   if($(this).val()==""){
    $(this).next().removeClass("active");
    }
    else{
       $(this).next().addClass("active");
    }
    });
  
    
  });