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

// Συνάρτηση για την απόκτηση των μη διαθέσιμων ημερομηνιών για ένα ακίνητο
function getUnavailableDates($listing_id, $conn) {
    $stmt = $conn->prepare("SELECT start_date, end_date FROM reservations WHERE listing_id = ?");
    $stmt->bind_param("i", $listing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $unavailable_dates = [];
    while ($row = $result->fetch_assoc()) {
        $unavailable_dates[] = $row;
    }
    $stmt->close();
    return $unavailable_dates;
}

// Έλεγχος αν η αίτηση είναι POST (υποβολή φόρμας)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Έλεγχος αν παρέχεται το ID ακινήτου
    if (!isset($_POST['property_id'])) {
        echo "Δεν παρέχεται ID ακινήτου";
        exit;
    }
    $listing_id = $_POST['property_id'];
    $user_id = $_SESSION['id'];
    $start_date = $_POST['check_in_date'];
    $end_date = $_POST['check_out_date'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Έλεγχος για μη διαθέσιμες ημερομηνίες
    $unavailable_dates = getUnavailableDates($listing_id, $conn);
    foreach ($unavailable_dates as $dates) {
        if (($start_date >= $dates['start_date'] && $start_date <= $dates['end_date']) || 
            ($end_date >= $dates['start_date'] && $end_date <= $dates['end_date']) || 
            ($start_date <= $dates['start_date'] && $end_date >= $dates['end_date'])) {
            $_SESSION['unavailable_dates'] = $unavailable_dates;
            header("Location: unavailable_dates.php");
            exit;
        }
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

    // Προετοιμασία και εκτέλεση της δήλωσης για την εισαγωγή στη βάση δεδομένων
    $stmt = $conn->prepare("INSERT INTO reservations (listing_id, user_id, start_date, end_date, total_price, name, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissdss", $listing_id, $user_id, $start_date, $end_date, $total_price, $name, $email);

    // Εκτέλεση της δήλωσης και έλεγχος επιτυχίας
    if ($stmt->execute()) {
        $_SESSION['reservation_id'] = $stmt->insert_id;  // Αποθήκευση του ID της κράτησης στη συνεδρία
        header("Location: confirmation.php");
        exit;
    } else {
        echo "Κάτι πήγε στραβά. Παρακαλώ δοκιμάστε ξανά.";
    }
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Έλεγχος αν παρέχεται το ID ακινήτου
    if (isset($_GET['id'])) {
        $property_id = $_GET['id'];

        // Απόκτηση των λεπτομερειών του ακινήτου από τη βάση δεδομένων
        $stmt = $conn->prepare("SELECT title, area, rooms, price_per_night, photo FROM listings WHERE id = ?");
        $stmt->bind_param("i", $property_id);
        $stmt->execute();
        $stmt->bind_result($title, $area, $rooms, $price_per_night, $photo);
        $stmt->fetch();
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Κάντε Κράτηση</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico">
</head>
<body>
    <!-- Εισαγωγή της γραμμής πλοήγησης -->
    <?php include 'includes/navbar.php'; ?>
    <main>
        <div class="booking-container">
            <h1>Κάντε Κράτηση</h1>
            <?php
            // Έλεγχος αν παρέχεται το ID ακινήτου
            if (isset($property_id)) {
                echo '<div class="property-details">';
                echo '<img src="' . $photo . '" alt="Property Photo">';
                echo '<h2>' . $title . '</h2>';
                echo '<p>Περιοχή: ' . $area . '</p>';
                echo '<p>Δωμάτια: ' . $rooms . '</p>';
                echo '<p>Τιμή ανά Νύχτα: €' . $price_per_night . '</p>';
                echo '</div>';

                // Φόρμα κράτησης
                echo '<form class="booking-form" action="book.php" method="POST">';
                echo '<input type="hidden" name="property_id" value="' . $property_id . '">';
                echo '<div class="form-group">';
                echo '<label for="check_in_date">Ημερομηνία Check-in:</label>';
                echo '<input type="date" id="check_in_date" name="check_in_date" required>';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label for="check_out_date">Ημερομηνία Check-out:</label>';
                echo '<input type="date" id="check_out_date" name="check_out_date" required>';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label for="name">Όνομα:</label>';
                echo '<input type="text" id="name" name="name" required>';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label for="email">Email:</label>';
                echo '<input type="email" id="email" name="email" required>';
                echo '</div>';
                echo '<button type="submit">Κάντε Κράτηση</button>';
                echo '</form>';
            } else {
                // Μήνυμα αν δεν παρέχεται το ID ακινήτου
                echo '<div class="booking-message">';
                echo '<p>Δεν παρέχεται ID ακινήτου</p>';
                echo '<a href="index.php" class="button">Επιστροφή στην Αρχική</a>';
                echo '</div>';
            }
            ?>
        </div>
    </main>
    <!-- Εισαγωγή του υποσέλιδου -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php
}
?>
