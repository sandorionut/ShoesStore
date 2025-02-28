<?php
include 'D:\AN3_SEM1\IS\XAMP\htdocs\Site_Pantofi\ShoesStore\admin.php'; // Include your database connection file

// Fetch all shoes
$shoes = [];
$stmt = $conn->prepare("SELECT id, nume_papuc, pret, imagine, descriere, brand, m38, m39, m40, m41, m42, m43, m44, m45, rating FROM papuci");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $shoes[] = $row;
}
$stmt->close();

// Fetch all orders
$orders = [];
$stmt = $conn->prepare("SELECT c.id AS order_id, c.total_price, c.order_date, u.email, p.nume_papuc, cd.size, cd.quantity, p.pret 
                        FROM comenzi c 
                        JOIN comenzi_detalii cd ON c.id = cd.order_id 
                        JOIN papuci p ON cd.shoe_id = p.id 
                        JOIN user u ON c.user_id = u.id 
                        ORDER BY c.order_date DESC");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Admin Page</h2>

        <!-- Shoes Table -->
        <h3>All Shoes</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Brand</th>
                    <th>Size 38</th>
                    <th>Size 39</th>
                    <th>Size 40</th>
                    <th>Size 41</th>
                    <th>Size 42</th>
                    <th>Size 43</th>
                    <th>Size 44</th>
                    <th>Size 45</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shoes as $shoe): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($shoe['id']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['nume_papuc']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['pret']); ?> RON</td>
                        <td><img src="../assets/img/<?php echo htmlspecialchars($shoe['imagine']); ?>" alt="<?php echo htmlspecialchars($shoe['nume_papuc']); ?>" style="width: 50px;"></td>
                        <td><?php echo htmlspecialchars($shoe['descriere']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['brand']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['m38']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['m39']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['m40']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['m41']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['m42']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['m43']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['m44']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['m45']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['rating']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Update Shoe Form -->
        <h3>Update Shoe</h3>
        <form method="post" action="update_shoe.php">
            <div class="form-group">
                <label for="shoe_id">Shoe ID:</label>
                <input type="text" class="form-control" id="shoe_id" name="shoe_id" required>
            </div>
            <div class="form-group">
                <label for="nume_papuc">Name:</label>
                <input type="text" class="form-control" id="nume_papuc" name="nume_papuc">
            </div>
            <div class="form-group">
                <label for="pret">Price:</label>
                <input type="text" class="form-control" id="pret" name="pret">
            </div>
            <div class="form-group">
                <label for="imagine">Image:</label>
                <input type="text" class="form-control" id="imagine" name="imagine">
            </div>
            <div class="form-group">
                <label for="descriere">Description:</label>
                <input type="text" class="form-control" id="descriere" name="descriere">
            </div>
            <div class="form-group">
                <label for="brand">Brand:</label>
                <input type="text" class="form-control" id="brand" name="brand">
            </div>
            <div class="form-group">
                <label for="m38">Size 38:</label>
                <input type="text" class="form-control" id="m38" name="m38">
            </div>
            <div class="form-group">
                <label for="m39">Size 39:</label>
                <input type="text" class="form-control" id="m39" name="m39">
            </div>
            <div class="form-group">
                <label for="m40">Size 40:</label>
                <input type="text" class="form-control" id="m40" name="m40">
            </div>
            <div class="form-group">
                <label for="m41">Size 41:</label>
                <input type="text" class="form-control" id="m41" name="m41">
            </div>
            <div class="form-group">
                <label for="m42">Size 42:</label>
                <input type="text" class="form-control" id="m42" name="m42">
            </div>
            <div class="form-group">
                <label for="m43">Size 43:</label>
                <input type="text" class="form-control" id="m43" name="m43">
            </div>
            <div class="form-group">
                <label for="m44">Size 44:</label>
                <input type="text" class="form-control" id="m44" name="m44">
            </div>
            <div class="form-group">
                <label for="m45">Size 45:</label>
                <input type="text" class="form-control" id="m45" name="m45">
            </div>
            <div class="form-group">
                <label for="rating">Rating:</label>
                <input type="text" class="form-control" id="rating" name="rating">
            </div>
            <button type="submit" class="btn btn-primary">Update Shoe</button>
        </form>

        <!-- Add Shoe Form -->
        <h3>Add Shoe</h3>
        <form method="post" action="add_shoe.php">
            <div class="form-group">
                <label for="nume_papuc_add">Name:</label>
                <input type="text" class="form-control" id="nume_papuc_add" name="nume_papuc" required>
            </div>
            <div class="form-group">
                <label for="pret_add">Price:</label>
                <input type="text" class="form-control" id="pret_add" name="pret" required>
            </div>
            <div class="form-group">
                <label for="imagine_add">Image:</label>
                <input type="text" class="form-control" id="imagine_add" name="imagine" required>
            </div>
            <div class="form-group">
                <label for="descriere_add">Description:</label>
                <input type="text" class="form-control" id="descriere_add" name="descriere" required>
            </div>
            <div class="form-group">
                <label for="brand_add">Brand:</label>
                <input type="text" class="form-control" id="brand_add" name="brand" required>
            </div>
            <div class="form-group">
                <label for="m38_add">Size 38:</label>
                <input type="text" class="form-control" id="m38_add" name="m38" required>
            </div>
            <div class="form-group">
                <label for="m39_add">Size 39:</label>
                <input type="text" class="form-control" id="m39_add" name="m39" required>
            </div>
            <div class="form-group">
                <label for="m40_add">Size 40:</label>
                <input type="text" class="form-control" id="m40_add" name="m40" required>
            </div>
            <div class="form-group">
                <label for="m41_add">Size 41:</label>
                <input type="text" class="form-control" id="m41_add" name="m41" required>
            </div>
            <div class="form-group">
                <label for="m42_add">Size 42:</label>
                <input type="text" class="form-control" id="m42_add" name="m42" required>
            </div>
            <div class="form-group">
                <label for="m43_add">Size 43:</label>
                <input type="text" class="form-control" id="m43_add" name="m43" required>
            </div>
            <div class="form-group">
                <label for="m44_add">Size 44:</label>
                <input type="text" class="form-control" id="m44_add" name="m44" required>
            </div>
            <div class="form-group">
                <label for="m45_add">Size 45:</label>
                <input type="text" class="form-control" id="m45_add" name="m45" required>
            </div>
            <div class="form-group">
                <label for="rating_add">Rating:</label>
                <input type="text" class="form-control" id="rating_add" name="rating" required>
            </div>
            <button type="submit" class="btn btn-success">Add Shoe</button>
        </form>

        <!-- Delete Shoe Form -->
        <h3>Delete Shoe</h3>
        <form method="post" action="delete_shoe.php">
            <div class="form-group">
                <label for="shoe_id_delete">Shoe ID:</label>
                <input type="text" class="form-control" id="shoe_id_delete" name="shoe_id" required>
            </div>
            <div class="form-group">
                <label for="nume_papuc_delete">Name:</label>
                <input type="text" class="form-control" id="nume_papuc_delete" name="nume_papuc" required>
            </div>
            <button type="submit" class="btn btn-danger">Delete Shoe</button>
        </form>

        <!-- Orders Table -->
        <h3>All Orders</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Price</th>
                    <th>Order Date</th>
                    <th>User Email</th>
                    <th>Shoe Name</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_price']); ?> RON</td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td><?php echo htmlspecialchars($order['nume_papuc']); ?></td>
                        <td><?php echo htmlspecialchars($order['size']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($order['pret']); ?> RON</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="../assets/js/jquery-1.11.0.min.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>