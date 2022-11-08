<?php
require 'vendor/autoload.php';

use Donstrange\Modalsupport\Modal;
?>

<!DOCTYPE html>

<head>
    <?php
    echo Modal::getAssets();
    ?>

    <script>
        window.addEventListener("load", () => {
            document.querySelector("#openFull")
            .addEventListener("click", (e) => {
                openModalById("modal-1")
                .then(data => {
                    alert(`Hello ${data.prename} ${data.surname}`)
                })
                .catch(() => {
                    console.log("Dialog was cancelled");
                })
            })
        });
    </script>
</head>

<body>
    <?php
    $content = "<label for='prename'>Vorname<label><input name='prename'><br><label for='surname'>Nachname</label><input name='surname'>";
    $m = new Modal("modal-1", $content);

    //if the return values are not interesting
    echo $m->getOpenButton("Open without return values");

    echo $m->getModalContent();
    ?>

    <button type="button" id="openFull">Open with return values</button>
</body>
</html>