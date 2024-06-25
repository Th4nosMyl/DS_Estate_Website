<?php
session_start(); // Ξεκινάει το session για να κρατήσει τις συνεδριακές μεταβλητές

require 'includes/config.php'; // Περιλαμβάνει το αρχείο ρυθμίσεων για τη σύνδεση στη βάση δεδομένων

// Ελέγχει αν ο χρήστης είναι συνδεδεμένος, αν όχι, ανακατευθύνει στη σελίδα σύνδεσης
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id']; // Παίρνει το ID του συνδεδεμένου χρήστη από τη συνεδρία

// Προετοιμάζει το SQL ερώτημα για την απόκτηση των κρατήσεων του χρήστη
$stmt = $conn->prepare("SELECT listings.title, reservations.start_date, reservations.end_date, reservations.total_price, reservations.id FROM reservations JOIN listings ON reservations.listing_id = listings.id WHERE reservations.user_id = ?");
$stmt->bind_param("i", $user_id); // Δεσμεύει το ID του χρήστη ως παράμετρο για το SQL ερώτημα
$stmt->execute(); // Εκτελεί το ερώτημα
$result = $stmt->get_result(); // Παίρνει το αποτέλεσμα του ερωτήματος
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8"> <!-- Ορισμός χαρακτήρων του εγγράφου -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Ορισμός παραθύρου θέασης για ανταποκρινόμενη σχεδίαση -->
    <title>Οι Κρατήσεις Μου - DS Estate</title> <!-- Τίτλος του εγγράφου -->
    <link rel="stylesheet" href="css/styles.css"> <!-- Συνδέει το αρχείο CSS για το στυλ της σελίδας -->
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico"> <!-- Συνδέει το favicon -->
</head>
<body>
    <?php include 'includes/navbar.php'; // Περιλαμβάνει το αρχείο πλοήγησης ?>
    <main>
        <div class="reservations-container">
            <h1>Οι Κρατήσεις Μου</h1>
            <?php if ($result->num_rows == 0): // Ελέγχει αν δεν υπάρχουν κρατήσεις για τον χρήστη ?>
                <p>Δεν έχετε κάνει καμία κράτηση.</p>
            <?php else: // Εάν υπάρχουν κρατήσεις ?>
                <ul>
                    <?php while ($row = $result->fetch_assoc()): // Επαναλαμβάνει για κάθε κράτηση ?>
                        <li>
                            <p>Ακίνητο: <?php echo htmlspecialchars($row['title']); ?></p> <!-- Εμφανίζει τον τίτλο του ακινήτου -->
                            <p>Ημερομηνία Check-in: <?php echo htmlspecialchars($row['start_date']); ?></p> <!-- Εμφανίζει την ημερομηνία check-in -->
                            <p>Ημερομηνία Check-out: <?php echo htmlspecialchars($row['end_date']); ?></p> <!-- Εμφανίζει την ημερομηνία check-out -->
                            <p>Συνολικό Ποσό: $<?php echo htmlspecialchars($row['total_price']); ?></p> <!-- Εμφανίζει το συνολικό ποσό -->
                            <!-- Κουμπί Ακύρωσης Κράτησης -->
                            <form action="user_reservations.php" method="POST" onsubmit="return confirm('Είστε σίγουροι ότι θέλετε να ακυρώσετε αυτή την κράτηση;');">
                                <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="cancel_reservation">Ακύρωση Κράτησης</button>
                            </form>
                        </li>
                    <?php endwhile; // Τέλος του βρόχου while ?>
                </ul>
            <?php endif; // Τέλος του ελέγχου για το αν υπάρχουν κρατήσεις ?>
            <?php $stmt->close(); // Κλείνει τη δήλωση ?>
        </div>
    </main>
    <?php include 'includes/footer.php'; // Περιλαμβάνει το αρχείο υποσέλιδου ?>
</body>
</html>

<?php
// Διαχείριση ακύρωσης κράτησης
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_reservation'])) {
    $reservation_id = $_POST['reservation_id'];

    // Προετοιμασία και εκτέλεση της δήλωσης για τη διαγραφή της κράτησης από τη βάση δεδομένων
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $reservation_id);

    // Εκτέλεση της δήλωσης και έλεγχος επιτυχίας
    if ($stmt->execute()) {
        header("Location: user_reservations.php"); // Ανακατεύθυνση στη σελίδα των κρατήσεων του χρήστη
        exit;
    } else {
        echo "Κάτι πήγε στραβά. Παρακαλώ δοκιμάστε ξανά.";
    }
    $stmt->close();
}
?>
