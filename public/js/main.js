function submitUnameForm() {
	document.getElementById("uname").submit();
}

$('ul.nav li a').each(function() {
    var href = window.location.pathname;
    var thishref = $(this).attr('href');
    if(href == thishref) {
        $(this).parent(0).addClass('active');
    }
});
