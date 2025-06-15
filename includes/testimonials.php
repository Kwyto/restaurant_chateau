<?php
function getTestimonials($conn) {
    $query = "SELECT t.*, u.first_name, u.last_name 
              FROM testimonials t
              JOIN users u ON t.user_id = u.id
              ORDER BY t.created_at DESC
              LIMIT 3";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function addTestimonial($conn, $user_id, $rating, $comment) {
    $rating = intval($rating);
    $comment = mysqli_real_escape_string($conn, $comment);
    
    $query = "INSERT INTO testimonials (user_id, rating, comment) 
              VALUES ($user_id, $rating, '$comment')";
    return mysqli_query($conn, $query);
}