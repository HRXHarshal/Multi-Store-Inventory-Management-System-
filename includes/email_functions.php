<?php
require_once 'load.php';
// No need to require autoload.php again as it's already included in load.php

/**
 * Send an email using SendGrid API
 * 
 * @param string $to_email Recipient email address
 * @param string $to_name Recipient name
 * @param string $subject Email subject
 * @param string $content Email body
 * @return bool True if email sent successfully, false otherwise
 */
function send_email($to_email, $to_name, $subject, $content) {
    // Get API key from environment variable
    $apiKey = $_ENV['SENDGRID_API_KEY'] ?? '';
    
    if (empty($apiKey)) {
        error_log('SendGrid API Key not found in environment variables');
        return false;
    }
    
    $email = new \SendGrid\Mail\Mail();
    $email->setFrom($_ENV['SENDGRID_SENDER_EMAIL'], $_ENV['SENDGRID_SENDER_NAME']);
    $email->addTo($to_email, $to_name);
    $email->setSubject($subject);
    $email->addContent("text/plain", $content);
    
    $sendgrid = new \SendGrid($apiKey);
    
    try {
        $response = $sendgrid->send($email);
        return $response->statusCode() == 202;
    } catch (Exception $e) {
        error_log('Email sending failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Send OTP verification email to customer
 * 
 * @param string $to_email Customer email
 * @param string $to_name Customer name
 * @param string $otp One-time password
 * @param array $order_details Order details array
 * @return bool True if email sent successfully
 */
function send_otp_email($to_email, $to_name, $otp, $order_details = []) {
    $subject = "Verification Code for Your Purchase Request";
    
    $message = "Dear {$to_name},\n\n";
    $message .= "Thank you for your purchase request. To verify your request, please use the following code:\n\n";
    $message .= "Verification Code: {$otp}\n\n";
    
    // Include order details if available
    if (!empty($order_details)) {
        $message .= "ORDER DETAILS:\n";
        $message .= "---------------\n";
        $message .= "Product: " . $order_details['product_name'] . "\n";
        $message .= "Quantity: " . $order_details['quantity'] . "\n";
        $message .= "Warehouse: " . $order_details['warehouse_name'] . "\n";
        
        if (!empty($order_details['notes'])) {
            $message .= "Special Instructions: " . $order_details['notes'] . "\n";
        }
        
        $message .= "---------------\n\n";
    }
    
    $message .= "This code will expire in 15 minutes.\n\n";
    $message .= "If you did not make this request, please ignore this email.\n\n";
    $message .= "Regards,\nInventory Management System";
    
    return send_email($to_email, $to_name, $subject, $message);
}

/**
 * Send order confirmation email to customer
 * 
 * @param string $to_email Customer email
 * @param string $to_name Customer name
 * @param array $order_details Order details array
 * @return bool True if email sent successfully
 */
function send_order_confirmation_email($to_email, $to_name, $order_details = []) {
    $subject = "Your Purchase Request Has Been Approved";
    
    $message = "Dear {$to_name},\n\n";
    $message .= "We are pleased to inform you that your purchase request has been approved and is now being processed.\n\n";
    
    // Include order details if available
    if (!empty($order_details)) {
        $message .= "ORDER DETAILS:\n";
        $message .= "---------------\n";
        $message .= "Order ID: " . $order_details['request_id'] . "\n";
        $message .= "Product: " . $order_details['product_name'] . "\n";
        $message .= "Quantity: " . $order_details['quantity'] . "\n";
        $message .= "Warehouse: " . $order_details['warehouse_name'] . "\n";
        
        if (!empty($order_details['notes'])) {
            $message .= "Special Instructions: " . $order_details['notes'] . "\n";
        }
        
        $message .= "---------------\n\n";
    }
    
    $message .= "Our team will contact you shortly to arrange delivery or pickup.\n\n";
    $message .= "Thank you for choosing our services.\n\n";
    $message .= "Regards,\nInventory Management System";
    
    return send_email($to_email, $to_name, $subject, $message);
}