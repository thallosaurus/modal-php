<?php
require 'vendor/autoload.php';

use Donstrange\Modalsupport\Modal;
?>

<!DOCTYPE html>

<head>
    <?php
    echo Modal::getAssets();
    ?>
</head>

<body>
    <?php
    $m = new Modal("modal-1");

    //if the return values are not interesting
    //echo $m->getOpenButton("Hello");

    echo $m->getModalContent();
    ?>
</body>
</html>