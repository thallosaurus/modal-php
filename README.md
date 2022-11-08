# modal-php
An easy way for PHP Applications to embed Dialog Windows - Powered by [micromodal.js](https://github.com/ghosh/Micromodal)

### Usage
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

PHP:
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
```