<?php
/*
 * Documentation for the Print Aura API can be found at https://printaura.com/api/
 *
 * Use of the Print Aura class requires a Seller API key and hash.
 *
 */
require_once 'PrintAura.php';

// Replace <SELLER_API_KEY> and <SELLER_API_HASH> with the values for your account
$printaura = new PrintAura(<SELLER_API_KEY>,<SELLER_API_HASH>);

$methods = array();

// Replace <PAPRODUCT_ID> with a valid Print Aura product ID to return a single product
$_POST['parameters'] = '{"paproduct_id":<PAPRODUCT_ID>}';
$methods['Product 1'] = $printaura->printaura('viewproducts');

// Unset the parameters so that all products are returned
unset($_POST['parameters']);
$methods['All of My Products'] = $printaura->printaura('viewproducts');

?>
<html>
<body>
<?php
foreach ($methods as $method => $result)
{
    ?>
    <h3><?=$method?></h3>
    <div><?=$result?></div>
    <?php
}
?>
</body>
</html>