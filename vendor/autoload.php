<?php
// Simple autoloader for phpdotenv
require_once __DIR__ . '/phpdotenv/src/Dotenv.php';
require_once __DIR__ . '/phpdotenv/src/Parser/Parser.php';
require_once __DIR__ . '/phpdotenv/src/Parser/Lexer.php';
require_once __DIR__ . '/phpdotenv/src/Parser/Value.php';
require_once __DIR__ . '/phpdotenv/src/Parser/Entry.php';
require_once __DIR__ . '/phpdotenv/src/Loader/Loader.php';
require_once __DIR__ . '/phpdotenv/src/Repository/RepositoryInterface.php';
require_once __DIR__ . '/phpdotenv/src/Repository/AbstractRepository.php';
require_once __DIR__ . '/phpdotenv/src/Repository/AdapterRepository.php';
// Add other required files as needed