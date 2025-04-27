<?php
session_start();
require_once '../../../admin/config/dbcon.php';

// Debug session data
error_log("Session data: " . print_r($_SESSION, true));

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the request body
    $requestData = json_decode(file_get_contents('php://input'), true);
    $action = isset($requestData['action']) ? $requestData['action'] : '';
    
    // Allow anonymous access for checking conversation existence
    if ($action === 'exists') {
        if (isset($_SESSION['auth_user'])) {
            $userId = $_SESSION['auth_user']['user_id'];
            checkConversationExists($conn, $userId);
        } else {
            echo json_encode(['status' => 'error', 'exists' => false, 'message' => 'User not logged in']);
        }
        exit;
    }
    
    // All other actions require authentication
    if (!isset($_SESSION['auth_user'])) {
        error_log("Auth user not set in session");
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }
    
    $userId = $_SESSION['auth_user']['user_id'];
    error_log("Processing action '$action' for user ID: $userId");
    
    switch ($action) {
        case 'save':
            if (isset($requestData['conversation'])) {
                saveConversation($conn, $userId, $requestData['conversation']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No conversation data provided']);
            }
            break;
            
        case 'load':
            loadConversation($conn, $userId);
            break;
            
        case 'clear':
            clearConversation($conn, $userId);
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// New function to check if a conversation exists for a user
function checkConversationExists($conn, $userId) {
    $query = "SELECT COUNT(*) as count FROM user_conversations WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    $exists = ($row['count'] > 0);
    echo json_encode([
        'status' => 'success',
        'exists' => $exists
    ]);
}

function saveConversation($conn, $userId, $conversation) {
    // The conversation is already a PHP array, so we just need to encode it once
    $conversationJson = json_encode($conversation);
    
    // Debug output to see what's being saved
    error_log("Saving conversation for user $userId: " . substr($conversationJson, 0, 100) . "...");
    
    // Check if user already has a conversation
    $checkQuery = "SELECT * FROM user_conversations WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        // Update existing conversation
        $updateQuery = "UPDATE user_conversations SET conversation_history = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "si", $conversationJson, $userId);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'Conversation updated']);
        } else {
            error_log("MySQL error: " . mysqli_error($conn));
            echo json_encode(['status' => 'error', 'message' => 'Failed to update conversation: ' . mysqli_error($conn)]);
        }
    } else {
        // Create new conversation entry
        $insertQuery = "INSERT INTO user_conversations (user_id, conversation_history) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "is", $userId, $conversationJson);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'Conversation saved']);
        } else {
            error_log("MySQL error: " . mysqli_error($conn));
            echo json_encode(['status' => 'error', 'message' => 'Failed to save conversation: ' . mysqli_error($conn)]);
        }
    }
}

function loadConversation($conn, $userId) {
    error_log("Loading conversation for user $userId");
    
    $query = "SELECT conversation_history FROM user_conversations WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Directly decode the JSON string from the database
        $conversation = json_decode($row['conversation_history'], true);
        error_log("Found conversation with " . count($conversation) . " messages");
        
        // Return the conversation with success status
        echo json_encode([
            'status' => 'success',
            'conversation' => $conversation
        ]);
    } else {
        // No conversation found
        error_log("No conversation found for user $userId");
        echo json_encode([
            'status' => 'success',
            'conversation' => null
        ]);
    }
}

function clearConversation($conn, $userId) {
    $query = "DELETE FROM user_conversations WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Conversation cleared']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to clear conversation: ' . mysqli_error($conn)]);
    }
}
?>