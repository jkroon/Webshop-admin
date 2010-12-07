
$(document).ready(function() {
     $('input').focus(function() {
          id = $(this).attr('id').substr(5);

          $('#label'+id).css({'font-weight' : 'bold', 'text-decoration' : 'underline'});
     });

     $('input').focusout(function() {
          id = $(this).attr('id').substr(5);

          $('#label'+id).css({'font-weight' : 'normal', 'text-decoration' : 'none'});
     });
});

