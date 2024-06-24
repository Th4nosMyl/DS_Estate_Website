<?php
// Έναρξη συνεδρίας
session_start();

// Εισαγωγή του αρχείου ρυθμίσεων για τη βάση δεδομένων
require 'includes/config.php';

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος, αν όχι, ανακατεύθυνση στη σελίδα σύνδεσης
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Έλεγχος αν η αίτηση είναι POST (δηλαδή αν έχει υποβληθεί φόρμα)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Έλεγχος αν παρέχεται το ID του ακινήτου στη φόρμα
    if (!isset($_POST['property_id'])) {
        header("Location: index.php");
        exit;
    }

    // Ανάθεση των τιμών από τη φόρμα σε μεταβλητές
    $listing_id = $_POST['property_id'];
    $user_id = $_SESSION['id'];
    $start_date = $_POST['check_in_date'];
    $end_date = $_POST['check_out_date'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Έλεγχος διαθεσιμότητας του ακινήτου για τις επιλεγμένες ημερομηνίες
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE listing_id = ? AND (? BETWEEN start_date AND end_date OR ? BETWEEN start_date AND end_date OR start_date BETWEEN ? AND ? OR end_date BETWEEN ? AND ?)");
    $stmt->bind_param("issssss", $listing_id, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    // Αν το ακίνητο δεν είναι διαθέσιμο, ανακατεύθυνση στη σελίδα λάθους διαθεσιμότητας
    if ($count > 0) {
        $_SESSION['availability_error'] = "Το ακίνητο δεν είναι διαθέσιμο στις επιλεγμένες ημερομηνίες.";
        header("Location: availability_error.php");
        exit;
    }

    // Υπολογισμός των ημερών διαμονής
    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
    $interval = $start_date_obj->diff($end_date_obj);
    $days = $interval->days;

    // Απόκτηση της τιμής ανά νύχτα για το συγκεκριμένο ακίνητο
    $stmt = $conn->prepare("SELECT price_per_night FROM listings WHERE id = ?");
    $stmt->bind_param("i", $listing_id);
    $stmt->execute();
    $stmt->bind_result($price_per_night);
    $stmt->fetch();
    $stmt->close();

    // Υπολογισμός της συνολικής τιμής
    $total_price = $days * $price_per_night;

    // Προετοιμασία και εκτέλεση της δήλωσης για την εισαγωγή της κράτησης στη βάση δεδομένων
    $stmt = $conn->prepare("INSERT INTO reservations (listing_id, user_id, start_date, end_date, total_price, name, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissdss", $listing_id, $user_id, $start_date, $end_date, $total_price, $name, $email);

    // Εκτέλεση της δήλωσης και έλεγχος επιτυχίας
    if ($stmt->execute()) {
        // Αποθήκευση του ID της κράτησης στη συνεδρία
        $_SESSION['reservation_id'] = $stmt->insert_id;
        // Ανακατεύθυνση στη σελίδα επιβεβαίωσης
        header("Location: confirmation.php");
        exit;
    } else {
        // Αν κάτι πήγε στραβά, αποθήκευση μηνύματος λάθους στη συνεδρία και ανακατεύθυνση στη σελίδα λάθους διαθεσιμότητας
        $_SESSION['availability_error'] = "Κάτι πήγε στραβά. Παρακαλώ δοκιμάστε ξανά.";
        header("Location: availability_error.php");
        exit;
    }
    $stmt->close();
} else {
    // Αν η αίτηση δεν είναι POST, ανακατεύθυνση στην αρχική σελίδα
    header("Location: index.php");
    exit;
}
?>
