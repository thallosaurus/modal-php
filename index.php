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
                openModalById("checkboxes-test")
                .then(data => {
                    console.log(data);
                    alert(`Deine Lieblingsfrüchte sind ${data.fruit}`);
                })
                .catch(() => {
                    console.log("Dialog was cancelled");
                })
            });

            document.querySelector("#btntest")
            .addEventListener("click", (e) => {
                openModalById("buttontest")
                .then(e => {
                    console.log(e);
                });
            })
        });
    </script>
</head>

<body>
    <?php
    $content = "<label for='prename'>Vorname<label><input name='prename'><br><label for='surname'>Nachname</label><input name='surname'>";
    $m = new Modal("checkboxes-test");
    $m->setData([
        "fruits" => [
            "Äpfel",
            "Birnen",
            "Erdbeeren"
        ]
    ]);

    $buttontest = new Modal("buttontest");
    $buttontest->setFilename("buttontest");

    //if the return values are not interesting
    echo $m->getOpenButton("Open without return values");

    // echo $m->getModalContent();
    ?>

    <button type="button" id="openFull">Open with return values</button>
    <button type="button" id="btntest">Open buttontest</button>
    <?php
        echo Modal::getAllModals();
    ?>
</body>
</html>