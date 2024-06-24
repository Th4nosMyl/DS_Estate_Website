<?php
session_start(); // Ξεκινάει το session
require 'includes/config.php'; // Περιλαμβάνει το αρχείο ρύθμισης της βάσης δεδομένων

// Ελέγχει αν υπάρχουν μη διαθέσιμες ημερομηνίες στη συνεδρία, αν όχι ανακατευθύνει τον χρήστη στην αρχική σελίδα
if (!isset($_SESSION['unavailable_dates'])) {
    header("Location: index.php");
    exit;
}

// Παίρνει τις μη διαθέσιμες ημερομηνίες από τη συνεδρία
$unavailable_dates = $_SESSION['unavailable_dates'];
unset($_SESSION['unavailable_dates']); // Αφαιρεί τις μη διαθέσιμες ημερομηνίες από τη συνεδρία
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8"> <!-- Ορίζει τον χαρακτήρα κωδικοποίησης σε UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Ορίζει την προβολή για κινητά και συσκευές -->
    <title>Μη Διαθέσιμες Ημερομηνίες</title> <!-- Τίτλος της σελίδας -->
    <link rel="stylesheet" href="css/styles.css"> <!-- Περιλαμβάνει το αρχείο στυλ -->
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico"> <!-- Περιλαμβάνει το favicon -->
</head>
<body>
    <?php include 'includes/navbar.php'; // Περιλαμβάνει το αρχείο πλοήγησης ?>
    <main>
        <div class="unavailable-dates-container">
            <h1>Μη Διαθέσιμες Ημερομηνίες</h1>
            <p>Οι ημερομηνίες που επιλέξατε δεν είναι διαθέσιμες. Δείτε παρακάτω τις μη διαθέσιμες ημερομηνίες για αυτό το ακίνητο:</p>
            <ul>
                <?php foreach ($unavailable_dates as $dates) : ?> <!-- Βρόχος για να εμφανίσει όλες τις μη διαθέσιμες ημερομηνίες -->
                    <li><?php echo htmlspecialchars($dates['start_date']) . ' έως ' . htmlspecialchars($dates['end_date']); ?></li> <!-- Εμφανίζει κάθε ζεύγος ημερομηνιών -->
                <?php endforeach; ?>
            </ul>
            <a href="index.php" class="button">Επιστροφή στην Αρχική</a> <!-- Κουμπί για επιστροφή στην αρχική σελίδα -->
        </div>
    </main>
    <?php include 'includes/footer.php'; // Περιλαμβάνει το αρχείο υποσέλιδου ?>
</body>
</html>
