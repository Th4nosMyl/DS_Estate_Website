<?php
// Έλεγχος κατάστασης συνεδρίας και έναρξη συνεδρίας αν δεν έχει ξεκινήσει ήδη
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Απόκτηση του ονόματος του τρέχοντος αρχείου σελίδας
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav> <!-- Αρχή του στοιχείου πλοήγησης -->
    <div class="logo"><a href="index.php">DS Estate</a></div> <!-- Λογότυπο της ιστοσελίδας με σύνδεσμο προς την αρχική σελίδα -->
    <ul class="nav-links"> <!-- Λίστα συνδέσμων πλοήγησης -->
        <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Αρχική</a></li> <!-- Σύνδεσμος προς την αρχική σελίδα -->
        <li><a href="feed.php" class="<?php echo ($current_page == 'feed.php') ? 'active' : ''; ?>">Feed</a></li> <!-- Σύνδεσμος προς τη σελίδα Feed -->
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?> <!-- Έλεγχος αν ο χρήστης είναι συνδεδεμένος -->
            <li><a href="create_listing.php" class="<?php echo ($current_page == 'create_listing.php') ? 'active' : ''; ?>">Δημιουργία Kαταχώρησης Aκινήτου</a></li> <!-- Σύνδεσμος προς τη σελίδα δημιουργίας καταχώρησης -->
            <li><a href="user_reservations.php" class="<?php echo ($current_page == 'user_reservations.php') ? 'active' : ''; ?>">Οι Κρατήσεις Μου</a></li> <!-- Σύνδεσμος προς τις κρατήσεις του χρήστη -->
            <li><a href="owner_reservations.php" class="<?php echo ($current_page == 'owner_reservations.php') ? 'active' : ''; ?>">Κρατήσεις Ακινήτων Μου</a></li> <!-- Σύνδεσμος προς τις κρατήσεις ακινήτων του χρήστη -->
            <li class="username-display"><a href="profile.php">Καλώς ήρθες, <?php echo htmlspecialchars($_SESSION['username']); ?></a></li> <!-- Εμφάνιση του ονόματος χρήστη και σύνδεσμος προς το προφίλ -->
        <?php else: ?>
            <li><a href="login.php" class="<?php echo ($current_page == 'login.php') ? 'active' : ''; ?>">Login</a></li> <!-- Σύνδεσμος προς τη σελίδα σύνδεσης για μη συνδεδεμένους χρήστες -->
        <?php endif; ?>
    </ul>
    <div class="hamburger"> <!-- Εικονίδιο για το μενού πλοήγησης σε κινητές συσκευές -->
        <span></span>
        <span></span>
        <span></span>
    </div>
</nav>
