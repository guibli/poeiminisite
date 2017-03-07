$ = jQuery.noConflict();

/* Add class for user image  */

$('.user-profile-image').find('img').addClass('img-circle').attr("","");

$('#myTab a').click(function (e) {
    e.preventDefault()
    $(this).tab('show')
})

jQuery(document).ready(function() {
  jQuery('.btn-btt').smoothScroll({speed: 1000});
  jQuery(window).scroll(function() {
    if(jQuery(window).scrollTop() > 200) {
        jQuery('.btn-btt').show();
      }
      else {
        jQuery('.btn-btt').hide();
      }
  }).resize(function(){
    if(jQuery(window).scrollTop() > 200) {
        jQuery('.btn-btt').show();
      }
      else {
        jQuery('.btn-btt').hide();
      }
  });
});
