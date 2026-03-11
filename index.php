<?php
/* ==========================
   DATABASE CONNECTION
========================== */
$config = [
    "host" => "mysql-produits-srv.mysql.database.azure.com",
    "db"   => "catalogueproduits",
    "user" => "user",
    "pass" => "Azure@123456"
];

try {
$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8",
    $user,
    $pass,
    [
        PDO::MYSQL_ATTR_SSL_CA => '/home/site/wwwroot/DigiCertGlobalRootCA.crt.pem'
    ]
);
} catch (PDOException $err) {
    die("Database connection failed : " . $err->getMessage());
}


/* ==========================
   ADD PRODUCT
========================== */
if (!empty($_POST['product_name'])) {

    $insert = $db->prepare("
        INSERT INTO Produits (nom, description, prix, stock, categorie)
        VALUES (:nom,:desc,:prix,:stock,:cat)
    ");

    $insert->execute([
        ":nom"  => $_POST['product_name'],
        ":desc" => $_POST['product_desc'],
        ":prix" => $_POST['product_price'],
        ":stock"=> $_POST['product_stock'],
        ":cat"  => $_POST['product_category']
    ]);

    header("Location: index.php?added=1");
    exit;
}


/* ==========================
   FETCH PRODUCTS
========================== */
$query = $db->query("SELECT * FROM Produits ORDER BY created_at DESC");
$products = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<title>Azure Product Manager</title>

<style>

body{
    font-family: 'Segoe UI',sans-serif;
    background:#f4f7fb;
    margin:0;
    padding:40px;
}

.container{
    max-width:1100px;
    margin:auto;
}

.header{
    text-align:center;
    margin-bottom:30px;
}

.header h1{
    color:#1b4f72;
}

.card{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    margin-bottom:30px;
}

input,textarea{
    width:100%;
    padding:10px;
    margin:8px 0 15px;
    border-radius:6px;
    border:1px solid #ccc;
}

button{
    background:#1b4f72;
    color:white;
    border:none;
    padding:12px 18px;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#154360;
}

.success{
    background:#d4edda;
    color:#155724;
    padding:12px;
    border-radius:6px;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#1b4f72;
    color:white;
    padding:10px;
}

td{
    padding:10px;
    border-bottom:1px solid #eee;
}

tr:hover{
    background:#f2f6fc;
}

</style>

</head>


<body>

<div class="container">

<div class="header">
<h1>🛒 Azure Product Catalogue</h1>
<p>PHP + Azure MySQL Demo</p>
</div>

<?php if(isset($_GET['added'])): ?>
<div class="success">Product successfully added ✔</div>
<?php endif; ?>


<!-- ADD PRODUCT FORM -->

<div class="card">

<h2>Add New Product</h2>

<form method="POST">

<label>Name</label>
<input type="text" name="product_name" required>

<label>Description</label>
<textarea name="product_desc"></textarea>

<label>Price (€)</label>
<input type="number" step="0.01" name="product_price" required>

<label>Stock</label>
<input type="number" name="product_stock" value="0">

<label>Category</label>
<input type="text" name="product_category">

<button type="submit">Add Product</button>

</form>

</div>



<!-- PRODUCT TABLE -->

<div class="card">

<h2>Product List (<?= count($products) ?>)</h2>

<table>

<tr>
<th>ID</th>
<th>Name</th>
<th>Description</th>
<th>Price</th>
<th>Stock</th>
<th>Category</th>
</tr>

<?php foreach($products as $prod): ?>

<tr>
<td><?= $prod['id'] ?></td>
<td><?= htmlspecialchars($prod['nom']) ?></td>
<td><?= htmlspecialchars($prod['description']) ?></td>
<td><?= $prod['prix'] ?> €</td>
<td><?= $prod['stock'] ?></td>
<td><?= htmlspecialchars($prod['categorie']) ?></td>
</tr>

<?php endforeach; ?>

</table>

</div>

</div>

</body>
</html>
