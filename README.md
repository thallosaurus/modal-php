# modal-php
An easy way for PHP Applications to embed Dialog Windows - Powered by [micromodal.js](https://github.com/ghosh/Micromodal) and [twig](https://twig.symphony.com)

### Usage
If you want to ignore a specific field, append `data-modal-ignore` param to the element
JavaScript:
```js
openModalById("modal-1")
    //opens the modal, waits for submit of the form and return the entered values
    .then((data) => {
        console.log(data);
    })
    .catch((e) => {
        alert("Dialog was cancelled");
    }) 
```

Example PHP Usage:
```php
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
```