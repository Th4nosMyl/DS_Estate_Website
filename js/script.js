document.addEventListener("DOMContentLoaded", () => {
  // Αναμονή μέχρι να φορτωθεί πλήρως το DOM
  const hamburger = document.querySelector(".hamburger"); // Επιλέγει το στοιχείο με κλάση "hamburger"
  const navLinks = document.querySelector(".nav-links"); // Επιλέγει το στοιχείο με κλάση "nav-links"

  // Προσθήκη ακροατή γεγονότος κλικ στο στοιχείο "hamburger"
  hamburger.addEventListener("click", () => {
    navLinks.classList.toggle("active"); // Εναλλάσσει την κλάση "active" στο στοιχείο "nav-links"
  });
});

function validateForm() {
  // Λήψη των τιμών των πεδίων της φόρμας
  let title = document.getElementById("title").value; // Λήψη της τιμής του πεδίου με id "title"
  let area = document.getElementById("area").value; // Λήψη της τιμής του πεδίου με id "area"
  let rooms = document.getElementById("rooms").value; // Λήψη της τιμής του πεδίου με id "rooms"
  let price = document.getElementById("price").value; // Λήψη της τιμής του πεδίου με id "price"

  // Έλεγχος αν ο τίτλος περιέχει μόνο χαρακτήρες
  if (!/^[a-zA-Z\s]+$/.test(title)) {
    alert("Ο τίτλος πρέπει να περιέχει μόνο χαρακτήρες."); // Εμφάνιση μηνύματος σφάλματος
    return false; // Ακύρωση υποβολής της φόρμας
  }

  // Έλεγχος αν η περιοχή περιέχει μόνο χαρακτήρες
  if (!/^[a-zA-Z\s]+$/.test(area)) {
    alert("Η περιοχή πρέπει να περιέχει μόνο χαρακτήρες."); // Εμφάνιση μηνύματος σφάλματος
    return false; // Ακύρωση υποβολής της φόρμας
  }

  // Έλεγχος αν ο αριθμός των δωματίων είναι θετικός ακέραιος
  if (!Number.isInteger(Number(rooms)) || Number(rooms) <= 0) {
    alert("Ο αριθμός των δωματίων πρέπει να είναι θετικός ακέραιος."); // Εμφάνιση μηνύματος σφάλματος
    return false; // Ακύρωση υποβολής της φόρμας
  }

  // Έλεγχος αν η τιμή είναι θετικός ακέραιος
  if (!Number.isInteger(Number(price)) || Number(price) <= 0) {
    alert("Η τιμή πρέπει να είναι θετικός ακέραιος."); // Εμφάνιση μηνύματος σφάλματος
    return false; // Ακύρωση υποβολής της φόρμας
  }

  return true; // Επιτρέπει την υποβολή της φόρμας αν όλα τα πεδία είναι έγκυρα
}
