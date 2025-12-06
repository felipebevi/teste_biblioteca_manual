
$(document).ready(function() {
    // verifica qual pagina o cliente esta e coloca a classe active no menu correspondente
    var path = window.location.pathname;
    var page = path.split("/").pop();

    switch(page) {
        case "":
        case "index.php":
            $('a.nav-link[href="/"]').addClass("active");
            break;
        case "cadLivro.php":
            $('a.nav-link[href="/cadLivro.php"]').addClass("active");
            break;
        case "cadAutor.php":
            $('a.nav-link[href="/cadAutor.php"]').addClass("active");
            break;
        case "cadAssunto.php":
            $('a.nav-link[href="/cadAssunto.php"]').addClass("active");
            break;
        case "relatorios.php":
            $('a.nav-link[href="/relatorios.php"]').addClass("active");
            break;
    }
});




