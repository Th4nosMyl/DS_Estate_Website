<?php
session_start(); // Ξεκινάει το session για να παρακολουθεί την κατάσταση του χρήστη
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DS Estate - Καλώς ήρθατε</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Συνδέει το αρχείο CSS για το στυλ της σελίδας -->
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico"> <!-- Συνδέει το favicon -->
</head>
<body>
    <?php include 'includes/navbar.php'; // Περιλαμβάνει το αρχείο πλοήγησης ?>
    <main>
        <section class="intro">
            <div class="container">
                <div class="logo-container">
                    <img src="logo\Photos_QigJHdkNl6-removebg-preview.png" alt="DS Estate Logo" class="intro-logo"> <!-- Εμφανίζει το λογότυπο της εταιρείας -->
                </div>
                <h1>Καλώς ήρθατε στο DS Estate</h1> <!-- Τίτλος καλωσορίσματος -->
                <p>Η DS Estate είναι η κορυφαία πλατφόρμα για την ενοικίαση και αγορά ακινήτων. Εξερευνήστε τα καλύτερα ακίνητα που έχουμε να προσφέρουμε και βρείτε το ιδανικό σας σπίτι.</p> <!-- Σύντομη περιγραφή της εταιρείας -->
                <a href="feed.php" class="button">Δείτε τα Ακίνητα μας</a> <!-- Σύνδεσμος προς τη σελίδα των διαθέσιμων ακινήτων -->
            </div>
        </section>
        <section class="about">
            <div class="container">
                <h2>Σχετικά με Εμάς</h2> <!-- Τίτλος της ενότητας -->
                <p>Στην DS Estate, δεσμευόμαστε να προσφέρουμε την καλύτερη εξυπηρέτηση και τα καλύτερα ακίνητα στην αγορά. Είτε ψάχνετε για το επόμενο σπίτι σας είτε για την ιδανική επένδυση, είμαστε εδώ για να σας βοηθήσουμε.</p> <!-- Περιγραφή της εταιρείας -->
            </div>
        </section>
        <section class="services">
            <div class="container">
                <h2>Οι Υπηρεσίες μας</h2> <!-- Τίτλος της ενότητας -->
                <ul>
                    <li>Ενοικιάσεις Κατοικιών</li> <!-- Λίστα υπηρεσιών -->
                    <li>Αγορές Ακινήτων (Σύντομα Κοντά Σας!)</li>
                    <li>Συμβουλευτικές Υπηρεσίες (Σύντομα Κοντά Σας!)</li>
                    <li>Διαχείριση Ακινήτων (Σύντομα Κοντά Σας!)</li>
                </ul>
            </div>
        </section>
        <section class="contact">
            <div class="container">
                <h2>Επικοινωνία</h2> <!-- Τίτλος της ενότητας -->
                <p>Για περισσότερες πληροφορίες, επικοινωνήστε μαζί μας μέσω τηλεφώνου ή email.</p> <!-- Σύντομη περιγραφή της ενότητας επικοινωνίας -->
                <p>Τηλέφωνο: +1234567890</p> <!-- Τηλέφωνο επικοινωνίας -->
                <p>Email: info@dsestate.com</p> <!-- Email επικοινωνίας -->
            </div>
        </section>
    </main>
    <?php include 'includes/footer.php'; // Περιλαμβάνει το αρχείο υποσέλιδου ?>
</body>
</html>
