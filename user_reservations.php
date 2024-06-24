<?php
session_start(); // Ξεκινάει το session
require 'includes/config.php'; // Περιλαμβάνει το αρχείο ρύθμισης της βάσης δεδομένων

// Ελέγχει αν ο χρήστης είναι συνδεδεμένος, αν όχι τον ανακατευθύνει στη σελίδα σύνδεσης
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id']; // Παίρνει το ID του χρήστη από τη συνεδρία

// Ελέγχει αν η μέθοδος του αιτήματος είναι POST για διαγραφή κράτησης
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = $_POST['reservation_id']; // Παίρνει το ID της κράτησης από τη φόρμα

    // Διαγραφή κράτησης
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Ανακατεύθυνση στη σελίδα με τις κρατήσεις του χρήστη
    header("Location: user_reservations.php");
    exit;
}

// Απόκτηση κρατήσεων χρήστη
$stmt = $conn->prepare("SELECT reservations.id, listings.title, reservations.start_date, reservations.end_date, reservations.total_price FROM reservations JOIN listings ON reservations.listing_id = listings.id WHERE reservations.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($reservation_id, $listing_title, $start_date, $end_date, $total_price);
$reservations = []; // Αρχικοποιεί τον πίνακα για τις κρατήσεις
while ($stmt->fetch()) {
    $reservations[] = [
        'reservation_id' => $reservation_id,
        'listing_title' => $listing_title,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'total_price' => $total_price
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8"> <!-- Ορίζει τον χαρακτήρα κωδικοποίησης σε UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Ορίζει την προβολή για κινητά και συσκευές -->
    <title>Οι Κρατήσεις Μου - DS Estate</title> <!-- Τίτλος της σελίδας -->
    <link rel="stylesheet" href="css/styles.css"> <!-- Περιλαμβάνει το αρχείο στυλ -->
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico"> <!-- Περιλαμβάνει το favicon -->
</head>
<body>
    <?php include 'includes/navbar.php'; // Περιλαμβάνει το αρχείο πλοήγησης ?>
    <main>
        <div class="reservations-container">
            <h1>Οι Κρατήσεις Μου</h1>
            <?php if (empty($reservations)): ?> <!-- Ελέγχει αν δεν υπάρχουν κρατήσεις -->
                <p>Δεν έχετε κάνει καμία κράτηση.</p>
            <?php else: ?> <!-- Αν υπάρχουν κρατήσεις, τις εμφανίζει -->
                <ul>
                    <?php foreach ($reservations as $reservation): ?>
                        <li>
                            <p>Ακίνητο: <?php echo htmlspecialchars($reservation['listing_title']); ?></p> <!-- Εμφανίζει τον τίτλο του ακινήτου -->
                            <p>Ημερομηνία Check-in: <?php echo htmlspecialchars($reservation['start_date']); ?></p> <!-- Εμφανίζει την ημερομηνία check-in -->
                            <p>Ημερομηνία Check-out: <?php echo htmlspecialchars($reservation['end_date']); ?></p> <!-- Εμφανίζει την ημερομηνία check-out -->
                            <p>Συνολικό Ποσό: €<?php echo htmlspecialchars($reservation['total_price']); ?></p> <!-- Εμφανίζει το συνολικό ποσό -->
                            <form action="user_reservations.php" method="post">
                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>"> <!-- Κρυφό πεδίο με το ID της κράτησης -->
                                <button type="submit">Ακύρωση Κράτησης</button> <!-- Κουμπί για ακύρωση της κράτησης -->
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'includes/footer.php'; // Περιλαμβάνει το αρχείο υποσέλιδου ?>
</body>
</html>
