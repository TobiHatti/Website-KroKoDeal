<?php

    $__pageRevision = 4.0;

    echo '
        <!-- Own links -->
            <link rel="stylesheet" type="text/css" href="/css/style.css?'.$__pageRevision.'">
            <link rel="stylesheet" type="text/css" href="/css/layout.css?'.$__pageRevision.'">
            <link rel="stylesheet" type="text/css" href="/css/dropdown.css?'.$__pageRevision.'">
            <link rel="stylesheet" type="text/css" href="/css/slide.css?'.$__pageRevision.'" />
            <link rel="stylesheet" type="text/css" href="/css/fonts.css?'.$__pageRevision.'" />
            <link rel="stylesheet" type="text/css" href="/css/modal.css?'.$__pageRevision.'" />
            <link rel="stylesheet" type="text/css" href="/css/flags.css?'.$__pageRevision.'" />
            <link href="/content/favicon.png?'.$__pageRevision.'" rel="icon" type="image/x-icon" />
        <!-- End own links -->


        <!-- Own Scripts -->
            <script src="/js/source.js?'.$__pageRevision.'"></script>
        <!-- End of Own Scripts -->


        <!-- External Links/Scripts -->
            <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <!-- End of External Links/Scripts -->


        <!-- Froala-Texteditor -->
            <link rel="stylesheet" href="/plugins/froala_style/froala_editor.css">
            <link rel="stylesheet" href="/plugins/froala_style/froala_style.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/code_view.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/colors.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/emoticons.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/image_manager.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/image.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/line_breaker.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/table.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/char_counter.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/video.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/fullscreen.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/file.css">
            <link rel="stylesheet" href="/plugins/froala_style/plugins/quick_insert.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css">
        <!-- End of Froala -->


        <!-- jsColor-->
            <script src="/plugins/jscolor/jscolor.js"></script>
        <!-- End of jsColor -->


        <!-- File Buttons -->
            <!--[if IE]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->
            <script>(function(e,t,n){var r=e.querySelectorAll("html")[0];r.className=r.className.replace(/(^|\s)no-js(\s|$)/,"$1js$2")})(document,window,0);</script>
        <!-- End of File Buttons -->


        <!-- Header-Parallax -->
            <script>
                window.addEventListener("scroll", function(e) {
                    var scrOffset = - (window.scrollY/16);
                    document.getElementById("mainPage").style.backgroundPosition = "0px " + (scrOffset) + "px";
                });
            </script>
        <!-- End of Header-Parallax -->
    ';

?>