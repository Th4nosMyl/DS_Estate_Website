<?php
session_start(); // Ξεκινάει το session
require 'includes/config.php'; // Περιλαμβάνει το αρχείο ρύθμισης της βάσης δεδομένων

// Ελέγχει αν ο χρήστης είναι συνδεδεμένος, αν όχι τον ανακατευθύνει στη σελίδα σύνδεσης
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id']; // Παίρνει το ID του χρήστη από το session

// Ελέγχει αν η μέθοδος του αιτήματος είναι POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = $_POST['reservation_id']; // Παίρνει το ID της κράτησης από τη φόρμα

    // Διαγραφή κράτησης που ανήκει σε ακίνητο του χρήστη
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ? AND listing_id IN (SELECT id FROM listings WHERE user_id = ?)");
    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: owner_reservations.php"); // Ανακατεύθυνση στη σελίδα κρατήσεων του ιδιοκτήτη
    exit;
}

// Απόκτηση κρατήσεων για τα ακίνητα του χρήστη
$stmt = $conn->prepare("SELECT reservations.id, listings.title, reservations.start_date, reservations.end_date, reservations.total_price, reservations.name, reservations.email FROM reservations JOIN listings ON reservations.listing_id = listings.id WHERE listings.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($reservation_id, $listing_title, $start_date, $end_date, $total_price, $guest_name, $guest_email);
$reservations = [];
while ($stmt->fetch()) {
    $reservations[] = [
        'reservation_id' => $reservation_id,
        'listing_title' => $listing_title,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'total_price' => $total_price,
        'guest_name' => $guest_name,
        'guest_email' => $guest_email
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Κρατήσεις Ακινήτων Μου - DS Estate</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico">
</head>
<body>
    <?php include 'includes/navbar.php'; // Περιλαμβάνει το αρχείο πλοήγησης ?>
    <main>
        <div class="reservations-container">
            <h1>Κρατήσεις Ακινήτων Μου</h1>
            <?php if (empty($reservations)): // Ελέγχει αν δεν υπάρχουν κρατήσεις και εμφανίζει κατάλληλο μήνυμα ?>
                <p>Δεν υπάρχουν κρατήσεις για τα ακίνητά σας.</p>
            <?php else: // Εμφανίζει τις κρατήσεις ?>
                <ul>
                    <?php foreach ($reservations as $reservation): ?>
                        <li>
                            <p>Ακίνητο: <?php echo htmlspecialchars($reservation['listing_title']); ?></p>
                            <p>Όνομα Επισκέπτη: <?php echo htmlspecialchars($reservation['guest_name']); ?></p>
                            <p>Email Επισκέπτη: <?php echo htmlspecialchars($reservation['guest_email']); ?></p>
                            <p>Ημερομηνία Check-in: <?php echo htmlspecialchars($reservation['start_date']); ?></p>
                            <p>Ημερομηνία Check-out: <?php echo htmlspecialchars($reservation['end_date']); ?></p>
                            <p>Συνολικό Ποσό: €<?php echo htmlspecialchars($reservation['total_price']); ?></p>
                            <form action="owner_reservations.php" method="post">
                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">
                                <button type="submit">Ακύρωση Κράτησης</button>
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
