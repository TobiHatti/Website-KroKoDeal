<?php
    require("lib/barcode.lib.php");

    $format = 'png';
    $symbology = 'code-128';
    $symbology = 'qr';
    $symbology = 'dmtx';
    $data = 'AT_ZI_1265';
    $options = '';

    $generator = new barcode_generator();

    /* Output directly to standard output. */
    $generator->output_image($format, $symbology, $data, $options);

    /* Create bitmap image. */
    $image = $generator->render_image($symbology, $data, $options);
    imagepng($image);
    imagedestroy($image);

    /* Generate SVG markup. */
    $svg = $generator->render_svg($symbology, $data, $options);
    echo $svg;


	include("_footer.php");
?>