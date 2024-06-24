<?php
// Έναρξη συνεδρίας
session_start();

// Εισαγωγή του αρχείου ρυθμίσεων για τη βάση δεδομένων
require 'includes/config.php';

// Έλεγχος αν υπάρχει μήνυμα σφάλματος διαθεσιμότητας στη συνεδρία, αν όχι, ανακατεύθυνση στην αρχική σελίδα
if (!isset($_SESSION['availability_error'])) {
    header("Location: index.php");
    exit;
}

// Ανάθεση του μηνύματος σφάλματος σε μεταβλητή και διαγραφή του από τη συνεδρία
$error_message = $_SESSION['availability_error'];
unset($_SESSION['availability_error']);
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <!-- Ορισμός του σετ χαρακτήρων σε UTF-8 -->
    <meta charset="UTF-8">
    <!-- Ορισμός του viewport για ανταπόκριση σε κινητές συσκευές -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Τίτλος της σελίδας -->
    <title>Σφάλμα Διαθεσιμότητας</title>
    <!-- Εισαγωγή του αρχείου CSS για τη μορφοποίηση της σελίδας -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Εισαγωγή του favicon -->
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico">
</head>
<body>
    <!-- Εισαγωγή της γραμμής πλοήγησης -->
    <?php include 'includes/navbar.php'; ?>
    <main>
        <div class="confirmation-details">
            <!-- Επικεφαλίδα για το σφάλμα διαθεσιμότητας -->
            <h1>Σφάλμα Διαθεσιμότητας</h1>
            <!-- Εμφάνιση του μηνύματος σφάλματος -->
            <h2><?php echo htmlspecialchars($error_message); ?></h2>
            <!-- Κουμπί για επιστροφή στην αρχική σελίδα -->
            <a href="index.php" class="button">Επιστροφή στην Αρχική</a>
        </div>
    </main>
    <!-- Εισαγωγή του υποσέλιδου -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>
