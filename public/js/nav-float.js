$(document).ready(function() {
  $(window).scroll(function() {
    if($(window).scrollTop() > 75) {
      $('.sidebar-collapse').addClass('sidebar-scrolled');
    } else {
      $('.sidebar-collapse').removeClass('sidebar-scrolled');
    }
  })
});