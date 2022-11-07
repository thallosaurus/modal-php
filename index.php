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
echo $m->getOpenButton("Hallo");
echo $m->getModalContent();
?>
</body>
</html>