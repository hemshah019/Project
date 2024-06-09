<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AlertService {
    public static function setError($message) {
        $_SESSION['error'] = $message;
    }

    public static function setSuccess($message) {
        $_SESSION['success'] = $message;
    }

    public static function setWarning($message) {
        $_SESSION['warning'] = $message;
    }

    public static function displayAlerts() {
        if (isset($_SESSION['error'])) {
            echo '<div class="alert-container"><div class="alert error">' . $_SESSION['error'] . '</div></div>';
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['success'])) {
            echo '<div class="alert-container"><div class="alert success">' . $_SESSION['success'] . '</div></div>';
            unset($_SESSION['success']);
        }

        if (isset($_SESSION['warning'])) {
            echo '<div class="alert-container"><div class="alert warning">' . $_SESSION['warning'] . '</div></div>';
            unset($_SESSION['warning']);
        }
    }

    public static function includeCSS($fadeOutDuration = '15') {
        echo '
        <style>
            .alert-container {
                position: fixed;
                top: 130px;
                right: 150px;
                z-index: 1000;
            }
            .alert {
                padding: 10px 20px;
                border-radius: 5px;
                margin-bottom: 10px;
                color: white;
                animation: slideIn 0.5s forwards, fadeOut ' . $fadeOutDuration . 's forwards;
                opacity: 0;
            }
    
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                }
                to {
                    transform: translateX(0);
                }
            }
    
            @keyframes fadeOut {
                from {
                    opacity: 1;
                }
                to {
                    opacity: 0;
                }
            }
    
            .alert.error {
                background-color: #f44336;
            }
    
            .alert.success {
                background-color: #4CAF50;
            }
    
            .alert.warning {
                background-color: #FFC107;
            }
        </style>';

    }
    
    
}