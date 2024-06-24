<header>
    <nav> <!-- Αρχή του στοιχείου πλοήγησης -->
        <div class="logo">DS Estate</div> <!-- Λογότυπο της ιστοσελίδας -->
        <ul class="nav-links"> <!-- Λίστα συνδέσμων πλοήγησης -->
            <li><a href="feed.php">Feed</a></li> <!-- Σύνδεσμος προς τη σελίδα Feed -->
            <li><a href="create_listing.php">Create Listing</a></li> <!-- Σύνδεσμος προς τη σελίδα δημιουργίας καταχώρησης -->
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?> <!-- Έλεγχος αν ο χρήστης είναι συνδεδεμένος -->
                <li><a href="logout.php">Logout</a></li> <!-- Σύνδεσμος αποσύνδεσης για συνδεδεμένους χρήστες -->
            <?php else: ?>
                <li><a href="login.php">Login</a></li> <!-- Σύνδεσμος σύνδεσης για μη συνδεδεμένους χρήστες -->
            <?php endif; ?>
        </ul>
        <div class="hamburger"> <!-- Εικονίδιο για το μενού πλοήγησης σε κινητές συσκευές -->
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
</header>
