<?php
// Έναρξη συνεδρίας
session_start();
// Εισαγωγή του αρχείου ρυθμίσεων για τη βάση δεδομένων
require 'includes/config.php';

// Έλεγχος αν υπάρχει το ID της κράτησης στη συνεδρία, αν όχι, ανακατεύθυνση στην αρχική σελίδα
if (!isset($_SESSION['reservation_id'])) {
    header("Location: index.php");
    exit;
}

// Απόκτηση του ID της κράτησης από τη συνεδρία
$reservation_id = $_SESSION['reservation_id'];

// Απόκτηση των στοιχείων της κράτησης από τη βάση δεδομένων
$stmt = $conn->prepare("SELECT listings.title, reservations.start_date, reservations.end_date, reservations.total_price, reservations.name, reservations.email FROM reservations JOIN listings ON reservations.listing_id = listings.id WHERE reservations.id = ?");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$stmt->bind_result($title, $start_date, $end_date, $total_price, $name, $email);
$stmt->fetch();
$stmt->close();

// Καθαρισμός της συνεδρίας κράτησης
unset($_SESSION['reservation_id']);
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Επιβεβαίωση Κράτησης</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico">
</head>
<body>
    <!-- Εισαγωγή της γραμμής πλοήγησης -->
    <?php include 'includes/navbar.php'; ?>
    <main>
        <div class="confirmation-details">
            <h1>Επιβεβαίωση Κράτησης</h1>
            <h2>Ευχαριστούμε για την κράτησή σας!</h2>
            <p>Στοιχεία Κράτησης:</p>
            <p>Όνομα: <?php echo htmlspecialchars($name); ?></p>
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
            <p>Ακίνητο: <?php echo htmlspecialchars($title); ?></p>
            <p>Ημερομηνία Check-in: <?php echo htmlspecialchars($start_date); ?></p>
            <p>Ημερομηνία Check-out: <?php echo htmlspecialchars($end_date); ?></p>
            <p>Συνολικό Ποσό: €<?php echo htmlspecialchars($total_price); ?></p>
            <a href="index.php" class="button">Επιστροφή στην Αρχική</a>
        </div>
    </main>
    <!-- Εισαγωγή του υποσέλιδου -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>
