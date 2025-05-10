<?php
header('Content-Type: application/json');
require_once '../../../admin/config/dbcon.php';

// Check if search query is provided
if (!isset($_GET['query']) || empty($_GET['query'])) {
    echo json_encode(['status' => 'error', 'message' => 'No search query provided']);
    exit;
}

$search_query = $_GET['query'];

// Convert search query to lowercase for case-insensitive matching
$search_query = strtolower($search_query);

// Break the query into keywords (words with 3+ characters)
$words = preg_split('/\s+/', $search_query);
$keywords = [];
foreach ($words as $word) {
    if (strlen($word) >= 3) {
        $keywords[] = mysqli_real_escape_string($conn, $word);
    }
}

// If no viable keywords, return empty result
if (empty($keywords)) {
    echo json_encode(['status' => 'success', 'faqs' => []]);
    exit;
}

// Build a query to find matching FAQs based on keywords
$keyword_conditions = [];
foreach ($keywords as $keyword) {
    $keyword_conditions[] = "LOWER(question) LIKE '%$keyword%' OR LOWER(keywords) LIKE '%$keyword%'";
}

$faq_query = "SELECT id, question, answer FROM faqs WHERE " . implode(' OR ', $keyword_conditions);
$faq_result = mysqli_query($conn, $faq_query);

$faqs = [];
if ($faq_result && mysqli_num_rows($faq_result) > 0) {
    while ($row = mysqli_fetch_assoc($faq_result)) {
        $faqs[] = $row;
    }
}

echo json_encode(['status' => 'success', 'faqs' => $faqs]);
?>
