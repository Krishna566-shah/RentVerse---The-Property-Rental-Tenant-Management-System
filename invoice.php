<?php
require 'config.php';
if (!isset($_GET['payment_id'])) die("Invalid request.");

$id = $_GET['payment_id'];
$stmt = $conn->prepare("SELECT py.*, p.title, t.name as tenant_name FROM payments py JOIN properties p ON py.property_id=p.id JOIN users t ON py.tenant_id=t.id WHERE py.id=? AND py.status='Approved'");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) die("Invoice not found or payment not approved.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice #INV-<?= $data['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body onload="window.print()" class="bg-light p-5">
    <div class="container bg-white p-5 shadow border border-2 border-danger rounded">
        <div class="d-flex justify-content-between border-bottom pb-4 mb-4">
            <div><h1 class="text-danger fw-bold">RENTVERSE</h1><p>Payment Receipt</p></div>
            <div class="text-end">
                <h4 class="fw-bold">INVOICE #INV-<?= str_pad($data['id'], 5, '0', STR_PAD_LEFT) ?></h4>
                <p>Date: <?= date("F j, Y", strtotime($data['payment_date'])) ?></p>
            </div>
        </div>
        <div class="mb-4">
            <h5><strong>Billed To:</strong></h5>
            <p><?= $data['tenant_name'] ?><br>Property: <?= $data['title'] ?></p>
        </div>
        <table class="table table-bordered">
            <thead class="bg-danger text-white"><tr><th>Description</th><th>Total</th></tr></thead>
            <tbody>
                <tr><td>Monthly Rent Payment</td><td>$<?= $data['amount'] ?></td></tr>
                <tr><th class="text-end text-danger">TOTAL PAID</th><th class="text-danger">$<?= $data['amount'] ?></th></tr>
            </tbody>
        </table>
        <div class="text-center mt-5">
            <h3 class="text-success fw-bold">PAID IN FULL</h3>
            <p class="text-muted small">Thank you for using RentVerse</p>
        </div>
    </div>
</body>
</html>