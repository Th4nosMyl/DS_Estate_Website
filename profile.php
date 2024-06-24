<?php
session_start(); // Ξεκινάει το session

// Ελέγχει αν ο χρήστης είναι συνδεδεμένος, αν όχι τον ανακατευθύνει στη σελίδα σύνδεσης
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Περιλαμβάνει το αρχείο ρύθμισης της βάσης δεδομένων
require_once "includes/config.php";

// Παίρνει το όνομα χρήστη από το session
$username = $_SESSION['username'];

// Προετοιμάζει το SQL ερώτημα για την ανάκτηση των στοιχείων του χρήστη
$sql = "SELECT * FROM users WHERE username = ?";

// Ελέγχει αν το statement προετοιμάστηκε επιτυχώς
if ($stmt = mysqli_prepare($conn, $sql)) {
    // Δέσμευση παραμέτρων στο SQL statement
    mysqli_stmt_bind_param($stmt, "s", $param_username);
    $param_username = $username;

    // Εκτέλεση του SQL statement
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        // Ελέγχει αν βρέθηκε ένας και μόνο χρήστης
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $email = isset($row['email']) ? $row['email'] : 'N/A'; // Παίρνει το email του χρήστη ή 'N/A' αν δεν υπάρχει
            $created_at = isset($row['created_at']) ? $row['created_at'] : 'N/A'; // Παίρνει την ημερομηνία εγγραφής ή 'N/A' αν δεν υπάρχει
        } else {
            echo "Ο χρήστης δεν βρέθηκε.";
            exit;
        }
    } else {
        echo "Κάτι πήγε στραβά. Παρακαλώ δοκιμάστε ξανά αργότερα.";
        exit;
    }
    mysqli_stmt_close($stmt); // Κλείνει το statement
} else {
    echo "Κάτι πήγε στραβά. Παρακαλώ δοκιμάστε ξανά αργότερα.";
    exit;
}

mysqli_close($conn); // Κλείνει τη σύνδεση με τη βάση δεδομένων
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Προφίλ - DS Estate</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico">
</head>
<body>
    <?php include 'includes/navbar.php'; // Περιλαμβάνει το αρχείο πλοήγησης ?>
    <main>
        <section class="profile">
            <div class="container">
                <h1>Προφίλ Χρήστη</h1>
                <p><strong>Όνομα Χρήστη:</strong> <?php echo htmlspecialchars($username); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Ημερομηνία Εγγραφής:</strong> <?php echo htmlspecialchars($created_at); ?></p>

                <!-- Φόρμα Αποσύνδεσης -->
                <form action="logout.php" method="post" class="profile-form">
                    <input type="submit" value="Logout" class="button">
                </form>

                <!-- Φόρμα Διαγραφής Λογαριασμού -->
                <form action="delete_account.php" method="post" class="profile-form">
                    <input type="submit" value="Διαγραφή Λογαριασμού" class="button delete">
                </form>
            </div>
        </section>
    </main>
    <?php include 'includes/footer.php'; // Περιλαμβάνει το αρχείο υποσέλιδου ?>
</body>
</html>
