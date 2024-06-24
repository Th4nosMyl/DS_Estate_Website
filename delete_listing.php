<?php
session_start(); // Ξεκινάει το session
require 'includes/config.php'; // Περιλαμβάνει το αρχείο ρύθμισης της βάσης δεδομένων

// Ελέγχει αν ο χρήστης είναι συνδεδεμένος, αν όχι τον ανακατευθύνει στη σελίδα σύνδεσης
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Ελέγχει αν παρέχεται το ID της καταχώρισης μέσω GET
if (isset($_GET['id'])) {
    $listing_id = $_GET['id']; // Παίρνει το ID της καταχώρισης

    // Ελέγχει αν η καταχώριση ανήκει στον συνδεδεμένο χρήστη
    $stmt = $conn->prepare("SELECT user_id, photo FROM listings WHERE id = ?");
    $stmt->bind_param("i", $listing_id);
    $stmt->execute();
    $stmt->bind_result($user_id, $photo);
    $stmt->fetch();
    $stmt->close();

    // Εάν η καταχώριση δεν ανήκει στον συνδεδεμένο χρήστη, εμφανίζει μήνυμα και σταματά την εκτέλεση
    if ($user_id != $_SESSION['id']) {
        echo "Δεν έχετε δικαίωμα να διαγράψετε αυτή την καταχώριση.";
        exit;
    }

    // Διαγραφή της καταχώρισης από τη βάση δεδομένων
    $stmt = $conn->prepare("DELETE FROM listings WHERE id = ?");
    $stmt->bind_param("i", $listing_id);

    // Εάν η διαγραφή είναι επιτυχής, διαγράφει και το αρχείο φωτογραφίας
    if ($stmt->execute()) {
        if (!empty($photo) && file_exists($photo)) {
            unlink($photo); // Διαγραφή του αρχείου φωτογραφίας
        }
        header("Location: feed.php"); // Ανακατεύθυνση στη σελίδα feed
        exit;
    } else {
        echo "Κάτι πήγε στραβά. Παρακαλώ δοκιμάστε ξανά."; // Εμφανίζει μήνυμα σφάλματος εάν η διαγραφή αποτύχει
    }

    $stmt->close(); // Κλείνει τη δήλωση
} else {
    echo "Δεν παρέχεται ID καταχώρισης."; // Εμφανίζει μήνυμα εάν δεν παρέχεται ID καταχώρισης
}
?>
