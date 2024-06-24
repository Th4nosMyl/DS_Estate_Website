<?php
session_start(); // Ξεκινάει το session
require 'includes/config.php'; // Περιλαμβάνει το αρχείο ρύθμισης της βάσης δεδομένων

// Ελέγχει αν ο χρήστης είναι συνδεδεμένος, αν όχι τον ανακατευθύνει στη σελίδα σύνδεσης
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id']; // Παίρνει το ID του συνδεδεμένου χρήστη

// Ελέγχει αν η μέθοδος του αιτήματος είναι POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Διαγραφή κρατήσεων που σχετίζονται με τα listings του χρήστη
    $stmt = $conn->prepare("DELETE r FROM reservations r INNER JOIN listings l ON r.listing_id = l.id WHERE l.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Διαγραφή εικόνων από τον φάκελο uploads
    $stmt = $conn->prepare("SELECT photo FROM listings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($photo);
    while ($stmt->fetch()) {
        $file_path = 'uploads/' . basename($photo); // Ορίζει τη διαδρομή του αρχείου
        if (file_exists($file_path)) {
            unlink($file_path); // Διαγραφή αρχείου
        }
    }
    $stmt->close();

    // Διαγραφή καταχωρίσεων
    $stmt = $conn->prepare("DELETE FROM listings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Διαγραφή κρατήσεων του χρήστη
    $stmt = $conn->prepare("DELETE FROM reservations WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Διαγραφή χρήστη
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Καθαρισμός συνεδρίας
    session_destroy();

    // Ανακατεύθυνση στην αρχική σελίδα
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8"> <!-- Ορίζει το σύνολο χαρακτήρων σε UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Ορίζει το viewport για ανταποκρινόμενη σχεδίαση -->
    <title>Διαγραφή Λογαριασμού - DS Estate</title> <!-- Τίτλος της σελίδας -->
    <link rel="stylesheet" href="css/styles.css"> <!-- Περιλαμβάνει το αρχείο στυλ -->
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico"> <!-- Ορίζει το favicon της σελίδας -->
</head>
<body>
    <?php include 'includes/navbar.php'; // Περιλαμβάνει το αρχείο πλοήγησης ?>
    <main>
        <div class="delete-account-container">
            <h1>Διαγραφή Λογαριασμού</h1>
            <p>Είστε σίγουροι ότι θέλετε να διαγράψετε τον λογαριασμό σας; Αυτή η ενέργεια δεν μπορεί να αναιρεθεί.</p>
            <form action="delete_account.php" method="post">
                <button type="submit">Διαγραφή Λογαριασμού</button> <!-- Κουμπί υποβολής για τη διαγραφή του λογαριασμού -->
            </form>
        </div>
    </main>
    <?php include 'includes/footer.php'; // Περιλαμβάνει το αρχείο υποσέλιδου ?>
</body>
</html>
