<?php

include_once 'book_sc_fns.php';

// The shopping cart needs sessions, so start one
session_start();

do_html_header("Welcome to Book-O-Rama");

echo "<p>Please choose a category:</p>";

// Get categories out of database
$cat_array = get_categories();

// Display as links to cat pages
display_categories($cat_array);

// If logged in as admin, show add, delete & edit cat links
if(isset($_SESSION['admin_user'])) {
  display_button("admin.php", "admin-menu", "Admin Menu");
}

do_html_footer();

?>
