<?php
session_start(); // Ξεκινάει το session
require 'includes/config.php'; // Περιλαμβάνει το αρχείο ρύθμισης της βάσης δεδομένων

// Ελέγχει αν ο χρήστης είναι συνδεδεμένος, αν όχι τον ανακατευθύνει στη σελίδα σύνδεσης
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$message = ''; // Αρχικοποιεί το μήνυμα που θα εμφανιστεί στον χρήστη

// Ελέγχει αν η μέθοδος του αιτήματος είναι POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title']; // Παίρνει τον τίτλο από τη φόρμα
    $area = $_POST['area']; // Παίρνει την περιοχή από τη φόρμα
    $rooms = $_POST['rooms']; // Παίρνει τον αριθμό δωματίων από τη φόρμα
    $price_per_night = $_POST['price']; // Παίρνει την τιμή ανά νύχτα από τη φόρμα

    // Διαχείριση του αρχείου φωτογραφίας
    $target_dir = "uploads/"; // Ορίζει τον φάκελο όπου θα αποθηκευτεί το αρχείο
    $target_file = $target_dir . basename($_FILES["photo"]["name"]); // Ορίζει το πλήρες όνομα αρχείου
    $uploadOk = 1; // Αρχικοποιεί τη μεταβλητή ελέγχου επιτυχίας φόρτωσης
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION)); // Παίρνει την επέκταση του αρχείου

    // Ελέγχει αν το αρχείο είναι εικόνα
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check === false) {
        $message .= "Το αρχείο δεν είναι εικόνα.<br>";
        $uploadOk = 0;
    }

    // Ελέγχει το μέγεθος του αρχείου
    if ($_FILES["photo"]["size"] > 500000) {
        $message .= "Συγγνώμη, το αρχείο σας είναι πολύ μεγάλο.<br>";
        $uploadOk = 0;
    }

    // Επιτρέπει συγκεκριμένες μορφές αρχείων
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $message .= "Συγγνώμη, επιτρέπονται μόνο αρχεία JPG, JPEG, PNG & GIF.<br>";
        $uploadOk = 0;
    }

    // Ελέγχει αν η μεταβλητή $uploadOk είναι 0 λόγω λάθους
    if ($uploadOk == 0) {
        $message .= "Συγγνώμη, το αρχείο σας δεν φορτώθηκε.<br>";
    // Αν όλα είναι εντάξει, προσπαθεί να φορτώσει το αρχείο
    } else {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo = $target_file; // Ορίζει τη διαδρομή του αρχείου φωτογραφίας

            // Προετοιμάζει την εντολή εισαγωγής στη βάση δεδομένων
            $stmt = $conn->prepare("INSERT INTO listings (title, area, rooms, price_per_night, photo, user_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssidsi", $title, $area, $rooms, $price_per_night, $photo, $_SESSION['id']);

            // Εκτελεί την εντολή εισαγωγής
            if ($stmt->execute()) {
                $message = "Η καταχώρηση δημιουργήθηκε με επιτυχία.";
                header("Location: feed.php"); // Ανακατεύθυνση στο feed μετά την επιτυχή δημιουργία καταχώρησης
                exit;
            } else {
                $message = "Κάτι πήγε στραβά. Παρακαλώ προσπαθήστε ξανά.<br>";
                $message .= "Σφάλμα: " . $stmt->error;
            }

            $stmt->close(); // Κλείνει τη δήλωση
        } else {
            $message .= "Συγγνώμη, υπήρξε σφάλμα κατά την φόρτωση του αρχείου σας.<br>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Δημιουργία Καταχώρησης - DS Estate</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico">
</head>
<body>
    <?php include 'includes/navbar.php'; // Περιλαμβάνει το αρχείο πλοήγησης ?>
    <main>
        <div class="create-listing-container">
            <h1>Δημιουργία Καταχώρησης</h1>
            <?php if (!empty($message)) : // Ελέγχει αν υπάρχει μήνυμα και το εμφανίζει ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
            <form action="create_listing.php" method="post" enctype="multipart/form-data">
                <label for="photo">Φωτογραφία:</label>
                <input type="file" id="photo" name="photo" required>

                <label for="title">Τίτλος:</label>
                <input type="text" id="title" name="title" required>

                <label for="area">Περιοχή:</label>
                <input type="text" id="area" name="area" required>

                <label for="rooms">Δωμάτια:</label>
                <input type="number" id="rooms" name="rooms" required>

                <label for="price">Τιμή ανά Νύχτα:</label>
                <input type="number" id="price" name="price" step="0.01" required>

                <button type="submit">Υποβολή</button>
            </form>
        </div>
    </main>
    <?php include 'includes/footer.php'; // Περιλαμβάνει το αρχείο υποσέλιδου ?>
</body>
</html>
