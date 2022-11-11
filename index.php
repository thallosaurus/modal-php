<?php
require 'vendor/autoload.php';

use Donstrange\Modalsupport\Modal;
use Donstrange\Modalsupport\TabView;

use const Donstrange\Modalsupport\SHOW_CLOSE_X;

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

            document.querySelector("#openRadio")
            .addEventListener("click", (e) => {
                openModalById("radio-test")
                .then(data => {
                    console.log(data);
                    // alert(`Deine Lieblingsfrüchte sind ${data.fruit}`);
                })
                .catch(() => {
                    console.log("Dialog was cancelled");
                })
            })

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
    // $content = "<label for='prename'>Vorname<label><input name='prename'><br><label for='surname'>Nachname</label><input name='surname'>";
    $m = new Modal("checkboxes-test", "checkboxes-test");
    $m->setData([
        "fruits" => [
            "Äpfel",
            "Birnen",
            "Erdbeeren"
        ]
    ]);

    $buttontest = new Modal("buttontest", "buttontest");
    // $buttontest->setFilename("buttontest");
    $buttontest->setVisibleFlags(SHOW_CLOSE_X);

    //if the return values are not interesting
    echo $m->getOpenButton("Open without return values");

    // echo $m->getModalContent();

    $tabview = new TabView();
    $tabview->addTemplate("Hello", "modal-1", true);
    $tabview->addTemplate("Hello1", "buttontest");
    $tabview->addTemplate("Hello2", "checkboxes-test");

    $radiotest = new Modal("radio-test");
    $radiotest->addTabView($tabview);
    $radiotest->setData([
        "fruits" => [
            "Äpfel",
            "Birnen",
            "Erdbeeren"
        ]
    ]);
    // $radiotest->setFilename("radio-test");

    $dbg = new Modal("test", "test");
    echo $dbg->getOpenButton("Test me");
    ?>

    <button type="button" id="openFull">Open with return values</button>
    <button type="button" id="btntest">Open buttontest</button>
    <button type="button" id="openRadio">Open radiotest</button>
    <?php
        echo Modal::getAllModals();
    ?>
</body>
</html>