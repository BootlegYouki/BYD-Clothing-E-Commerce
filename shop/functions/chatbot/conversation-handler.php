<?php
session_start();
require_once '../../../admin/config/dbcon.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the request body
    $requestData = json_decode(file_get_contents('php://input'), true);
    $action = isset($requestData['action']) ? $requestData['action'] : '';
    
    // Only allow logged in users to save conversations
    if (!isset($_SESSION['auth_user'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }
    
    $userId = $_SESSION['auth_user']['user_id'];
    
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
    $query = "SELECT conversation_history FROM user_conversations WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Directly decode the JSON string from the database
        $conversation = json_decode($row['conversation_history'], true);
        
        // Return the conversation with success status
        echo json_encode([
            'status' => 'success',
            'conversation' => $conversation
        ]);
    } else {
        // No conversation found
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