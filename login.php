<?php
session_start(); // Ξεκινάει το session για τη διαχείριση των συνδεδεμένων χρηστών
require 'includes/config.php';  // Περιλαμβάνει το αρχείο ρυθμίσεων της βάσης δεδομένων

$login_errors = []; // Πίνακας για τα σφάλματα σύνδεσης
$register_errors = []; // Πίνακας για τα σφάλματα εγγραφής

// Έλεγχος της μεθόδου του αιτήματος
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Αν το αίτημα είναι για σύνδεση
        $username = $_POST['username']; // Παίρνει το όνομα χρήστη από τη φόρμα
        $password = $_POST['password']; // Παίρνει τον κωδικό πρόσβασης από τη φόρμα

        // Προετοιμασία και εκτέλεση της δήλωσης για έλεγχο του χρήστη
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Αν βρέθηκε ο χρήστης, παίρνει τα αποτελέσματα
            $stmt->bind_result($id, $username, $hashed_password);
            $stmt->fetch();

            // Έλεγχος του κωδικού πρόσβασης
            if (password_verify($password, $hashed_password)) {
                // Αν ο κωδικός είναι σωστός, δημιουργεί το session για τον χρήστη
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $username;
                header("Location: feed.php"); // Ανακατεύθυνση στη σελίδα feed
                exit;
            } else {
                $login_errors[] = "Invalid password."; // Σφάλμα αν ο κωδικός είναι λανθασμένος
            }
        } else {
            $login_errors[] = "No user found with that username."; // Σφάλμα αν δεν βρέθηκε ο χρήστης
        }
        $stmt->close();
    } elseif (isset($_POST['register'])) {
        // Αν το αίτημα είναι για εγγραφή
        $first_name = $_POST['first_name']; // Παίρνει το όνομα από τη φόρμα
        $last_name = $_POST['last_name']; // Παίρνει το επώνυμο από τη φόρμα
        $username = $_POST['reg_username']; // Παίρνει το όνομα χρήστη από τη φόρμα
        $password = $_POST['reg_password']; // Παίρνει τον κωδικό πρόσβασης από τη φόρμα
        $email = $_POST['email']; // Παίρνει το email από τη φόρμα

        // Προετοιμασία και εκτέλεση της δήλωσης για έλεγχο αν το όνομα χρήστη είναι ήδη καταχωρημένο
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $register_errors[] = "Username already taken."; // Σφάλμα αν το όνομα χρήστη είναι ήδη καταχωρημένο
        } else {
            // Αν το όνομα χρήστη είναι διαθέσιμο, δημιουργεί το hash του κωδικού πρόσβασης
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Προετοιμασία και εκτέλεση της δήλωσης για εισαγωγή του νέου χρήστη στη βάση δεδομένων
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, password, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $first_name, $last_name, $username, $hashed_password, $email);

            if ($stmt->execute()) {
                header("Location: login.php?registered=1"); // Ανακατεύθυνση στη σελίδα σύνδεσης μετά την επιτυχή εγγραφή
                exit;
            } else {
                $register_errors[] = "Registration failed. Please try again."; // Σφάλμα αν αποτύχει η εγγραφή
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Συνδέει το αρχείο CSS για το στυλ της σελίδας -->
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico"> <!-- Συνδέει το favicon -->
</head>
<body>
    <?php include 'includes/navbar.php'; // Περιλαμβάνει το αρχείο πλοήγησης ?>
    <main>
        <h1>Login/Register</h1> <!-- Τίτλος σελίδας -->
        <div class="forms">
            <div class="form-container">
                <h2>Login</h2> <!-- Τίτλος φόρμας σύνδεσης -->
                <form action="login.php" method="POST">
                    <label for="username">Username:</label> <!-- Ετικέτα για το όνομα χρήστη -->
                    <input type="text" id="username" name="username" required> <!-- Πεδία εισαγωγής για το όνομα χρήστη -->

                    <label for="password">Password:</label> <!-- Ετικέτα για τον κωδικό πρόσβασης -->
                    <input type="password" id="password" name="password" required> <!-- Πεδία εισαγωγής για τον κωδικό πρόσβασης -->

                    <button type="submit" name="login">Login</button> <!-- Κουμπί υποβολής φόρμας σύνδεσης -->
                </form>
                <?php if (!empty($login_errors)) : ?> <!-- Ελέγχει αν υπάρχουν σφάλματα σύνδεσης και τα εμφανίζει -->
                    <ul class="errors">
                        <?php foreach ($login_errors as $error) : ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="form-container">
                <h2>Register</h2> <!-- Τίτλος φόρμας εγγραφής -->
                <form action="login.php" method="POST">
                    <label for="first_name">First Name:</label> <!-- Ετικέτα για το όνομα -->
                    <input type="text" id="first_name" name="first_name" required> <!-- Πεδία εισαγωγής για το όνομα -->

                    <label for="last_name">Last Name:</label> <!-- Ετικέτα για το επώνυμο -->
                    <input type="text" id="last_name" name="last_name" required> <!-- Πεδία εισαγωγής για το επώνυμο -->

                    <label for="reg_username">Username:</label> <!-- Ετικέτα για το όνομα χρήστη -->
                    <input type="text" id="reg_username" name="reg_username" required> <!-- Πεδία εισαγωγής για το όνομα χρήστη -->

                    <label for="reg_password">Password:</label> <!-- Ετικέτα για τον κωδικό πρόσβασης -->
                    <input type="password" id="reg_password" name="reg_password" required> <!-- Πεδία εισαγωγής για τον κωδικό πρόσβασης -->

                    <label for="email">Email:</label> <!-- Ετικέτα για το email -->
                    <input type="email" id="email" name="email" required> <!-- Πεδία εισαγωγής για το email -->

                    <button type="submit" name="register">Register</button> <!-- Κουμπί υποβολής φόρμας εγγραφής -->
                </form>
                <?php if (!empty($register_errors)) : ?> <!-- Ελέγχει αν υπάρχουν σφάλματα εγγραφής και τα εμφανίζει -->
                    <ul class="errors">
                        <?php foreach ($register_errors as $error) : ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if (isset($_GET['registered']) && $_GET['registered'] == 1) : ?> <!-- Ελέγχει αν η εγγραφή ήταν επιτυχής και εμφανίζει μήνυμα επιτυχίας -->
                    <p>Registration successful! Please log in.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php include 'includes/footer.php'; // Περιλαμβάνει το αρχείο υποσέλιδου ?>
</body>
</html>
