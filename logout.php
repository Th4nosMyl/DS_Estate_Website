<?php
session_start(); // Ξεκινάει το session για τη διαχείριση των συνδεδεμένων χρηστών
session_unset(); // Αφαιρεί όλες τις μεταβλητές συνεδρίας
session_destroy(); // Καταστρέφει τη συνεδρία
header("Location: login.php"); // Ανακατευθύνει τον χρήστη στη σελίδα σύνδεσης
exit(); // Τερματίζει το script
?>
