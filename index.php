<?php
require 'vendor/autoload.php';

use Donstrange\Modalsupport\Modal;
use Donstrange\Modalsupport\TabView;

use Donstrange\Modalsupport\TemplateLoader;
use const Donstrange\Modalsupport\SHOW_SUBMIT;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>modal-php Demonstration</title>

    <?php
    //Imports all required CSS and JavaScript files into the HTML
    echo Modal::getAssets();

    //Sets the path to where the templates are
    TemplateLoader::setModalPath(__DIR__ . "/example");
    ?>

    <script>
        window.addEventListener("load", (event) => {

            //example 2
            document.querySelector("#open-example2")
                .addEventListener("click", (ev) => {
                    openModalById("example2")
                        .then(e => {
                            console.log(e);

                            alert("You birthday is on the " + e.birthdayinput + "!");
                        });
                });

            document.querySelector("#open-example3")
                .addEventListener("click", (ev) => {
                    openModalById("example3", (event) => {
                        console.log(event);
                    });
                });

            document.querySelector("#open-example4")
            .addEventListener("click", (ev) => {
                openModalById("example4")
                .then(console.log);
            })
        });
    </script>

</head>

<button type="button" id="open-example2">Open Birthdaypicker</button>
<button type="button" id="open-example3">Open Something else</button>
<button type="button" id="open-example4">Open Tabs</button>

<body>

    <?php
    //example 1
    $m = new Modal("example1", "discard-example");
    $m->setVisibleFlags(SHOW_SUBMIT);
    $m->setData([
        "welcomestring" => "Welcome to modal-php v0.0.1!"
    ]);
    echo $m->getOpenButton("Click for an introduction");

    //example 2
    //you don't have to save the modal in a variable
    new Modal("example2", "channel1-example");

    //example 3
    new Modal("example3", "channel2-example");

    //example 4
    $tabModal = new Modal("example4");
    $tabs = new TabView();

    //setting the third argument to true makes it preselected
    $tabs->addTemplate("Tab 1", "tabs/tab1", true);
    $tabs->addTemplate("Tab 2", "tabs/tab2");
    $tabs->addTemplate("Tab 3", "tabs/tab3");
    $tabModal->addTabView($tabs);

    //required, renders all defined modals to html and echos it
    echo Modal::getAllModals();
    ?>
</body>

</html>