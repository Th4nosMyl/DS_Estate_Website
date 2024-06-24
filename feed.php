<?php
session_start(); // Ξεκινάει το session
include 'includes/config.php'; // Περιλαμβάνει το αρχείο ρύθμισης της βάσης δεδομένων
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed - DS Estate</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Συνδέει το αρχείο CSS για το στυλ της σελίδας -->
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico"> <!-- Συνδέει το favicon -->
</head>
<body>
    <?php include 'includes/navbar.php'; // Περιλαμβάνει το αρχείο πλοήγησης ?>
    <main>
        <h1>Διαθέσιμα Ακίνητα</h1>
        <div class="properties">
            <?php
            $sql = "SELECT id, photo, title, area, rooms, price_per_night, user_id FROM listings"; // Δημιουργεί το ερώτημα SQL για την επιλογή των καταχωρίσεων ακινήτων
            $result = $conn->query($sql); // Εκτελεί το ερώτημα

            // Ελέγχει αν υπάρχουν αποτελέσματα από το ερώτημα
            if ($result->num_rows > 0) {
                // Βρόχος για να διατρέξει κάθε γραμμή αποτελεσμάτων
                while($row = $result->fetch_assoc()) {
                    echo '<div class="property">'; // Ανοίγει το div για την καταχώριση
                    // Ελέγχει αν υπάρχει φωτογραφία για την καταχώριση
                    if (!empty($row["photo"])) {
                        echo '<img src="' . $row["photo"] . '" alt="Property Photo">'; // Εμφανίζει τη φωτογραφία της καταχώρισης
                    } else {
                        echo '<img src="uploads/default.jpg" alt="Default Property Photo">'; // Εμφανίζει προεπιλεγμένη φωτογραφία αν δεν υπάρχει καμία
                    }
                    echo '<h2>' . $row["title"] . '</h2>'; // Εμφανίζει τον τίτλο της καταχώρισης
                    echo '<p>Περιοχή: ' . $row["area"] . '</p>'; // Εμφανίζει την περιοχή της καταχώρισης
                    echo '<p>Δωμάτια: ' . $row["rooms"] . '</p>'; // Εμφανίζει τον αριθμό δωματίων της καταχώρισης
                    echo '<p>Τιμή ανά Νύχτα: €' . $row["price_per_night"] . '</p>'; // Εμφανίζει την τιμή ανά νύχτα της καταχώρισης

                    // Ελέγχει αν ο χρήστης είναι συνδεδεμένος
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                        // Εμφανίζει σύνδεσμο για να κάνει κράτηση
                        echo '<a href="book.php?id=' . $row["id"] . '">Κάντε Κράτηση</a>';
                        // Ελέγχει αν η καταχώριση ανήκει στον συνδεδεμένο χρήστη και εμφανίζει σύνδεσμο για διαγραφή
                        if ($_SESSION['id'] == $row["user_id"]) {
                            echo ' | <a href="delete_listing.php?id=' . $row["id"] . '" onclick="return confirm(\'Είστε σίγουροι ότι θέλετε να διαγράψετε αυτή την καταχώριση;\')">Διαγραφή</a>';
                        }
                    } else {
                        // Εμφανίζει μήνυμα για τους μη συνδεδεμένους χρήστες
                        echo '<p>Παρακαλώ <a href="login.php">συνδεθείτε</a> για να κάνετε κράτηση.</p>';
                    }
                    echo '</div>'; // Κλείνει το div για την καταχώριση
                }
            } else {
                echo "Δεν βρέθηκαν ακίνητα"; // Εμφανίζει μήνυμα αν δεν βρέθηκαν ακίνητα
            }
            $conn->close(); // Κλείνει τη σύνδεση με τη βάση δεδομένων
            ?>
        </div>
    </main>
    <?php include 'includes/footer.php'; // Περιλαμβάνει το αρχείο υποσέλιδου ?>
</body>
</html>
