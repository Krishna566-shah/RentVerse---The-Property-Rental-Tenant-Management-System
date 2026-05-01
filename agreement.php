<?php
require 'config.php';
if (!isset($_GET['assignment_id'])) die("Invalid request.");

$id = $_GET['assignment_id'];
$stmt = $conn->prepare("SELECT a.*, p.title, p.price, p.location, t.name as tenant_name, o.name as owner_name FROM property_assignments a JOIN properties p ON a.property_id=p.id JOIN users t ON a.tenant_id=t.id JOIN users o ON a.owner_id=o.id WHERE a.id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) die("Agreement not found.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rental Agreement - <?= $data['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body onload="window.print()" class="bg-light p-5">
    <div class="container bg-white p-5 shadow border border-2 border-danger rounded">
        <div class="text-center mb-5">
            <h1 class="text-danger fw-bold">RENTVERSE</h1>
            <h2>OFFICIAL RENTAL AGREEMENT</h2>
            <p class="text-muted">Generated on: <?= date("F j, Y, g:i a") ?></p>
        </div>
        <div class="row mb-4">
            <div class="col-6"><strong>Landlord:</strong> <?= $data['owner_name'] ?></div>
            <div class="col-6 text-end"><strong>Tenant:</strong> <?= $data['tenant_name'] ?></div>
        </div>
        <hr>
        <h4 class="fw-bold mt-4">1. Property Details</h4>
        <p>This agreement confirms the rental of the property known as <strong><?= $data['title'] ?></strong> located at <strong><?= $data['location'] ?></strong>.</p>
        <h4 class="fw-bold mt-4">2. Financial Terms</h4>
        <p>The tenant agrees to pay a monthly rent of <strong>$<?= $data['price'] ?></strong>.</p>
        <h4 class="fw-bold mt-4">3. Binding Agreement</h4>
        <p>This digital contract was automatically executed upon the assignment of the property on <?= $data['created_at'] ?> inside the RentVerse Management System.</p>
        <div class="mt-5 pt-5 row text-center">
            <div class="col-6"><hr class="w-75 mx-auto">Landlord Signature</div>
            <div class="col-6"><hr class="w-75 mx-auto">Tenant Signature</div>
        </div>
    </div>
</body>
</html>