<?php

namespace Includes;

class ClientLang
{
    // Authentication messages
    public const PLEASE_LOGIN = "Please login to access this page";
    public const USER_NOT_FOUND = 'User not found';
    public const USERKYC_NOT_FOUND = 'User KYC not found';
    public const USER_VERIFIED = 'Your account has been verified successfully. Kindly login to enjoy our unlimited services';
    public const USER_VERIFIED_ADMIN = 'Account has been verified successfully.';
    public const USER_UNBLOCKED = 'Account unblocked successfully';
    public const USER_BLOCKED = 'Account blocked successfully';
    public const USER_RESTORED = 'Account restored successfully';
    public const ACCOUNT_SUSPENDED = 'Your account has been temporarily suspended.';
    public const ACCOUNT_BLOCKED = 'Your account has been temporarily blocked due to unsuccessful login attempts. Kindly check back in %s';
    public const ACCOUNT_NOT_VERIFIED = 'Your account is pending activation. Kindly check your email inbox or spam folder to verify your account';
    public const USERNAME_EXIST = "Username already exist";
    public const EMAIL_EXIST = "Email address already belong to a user";
    public const INVALID_EMAIL = "Email address is invalid";
    public const PHONE_EXIST = "Phone Number already exist";
    public const LEAGUE_EXIST = "League already exist";
    public const TEAM_EXIST = "Team already exist";
    public const PASS_LEN_5 = "Minimum password length must be 5 characters";
    public const TRANS_PIN_LEN_6 = "Transaction pin length must be 6 digits";
    public const INCORRECT_TRANS_PIN = "Transaction pin provided is incorrect. Please provide a valid pin";
    public const PASS_LEN_5_NO_SPACE = "Password must contain at least 5 characters, 1 number without a space";
    public const NEW_EQUAL_OLD_PASSWORD = "Password must be different from old password";
    public const CURRENTPLAN_EQUAL_NEWPLAN = "Current plan must be different from new plan";
    public const PLAN_ADDING_FAILED = "Error adding plan";
    public const PLAN_EXISTS = "Plan already exists";
    public const PLAN_NOT_AVAILABLE = "Plan is currently disabled from upgrade";
    public const PLAN_NOT_FOUND = "Plan is not available";
    public const PLAN_DELETE_FAILED = "Error deleting plan";
    public const PLAN_UPGRADE_SUCCESS = "Plan upgrade was successful. You can now enjoy amazing discount offers on your next purchase";
    public const NEW_EQUAL_OLD_TRANS_PIN = "New pin must be different from old password";
    public const NUMBER_ONLY_TRANS_PIN = "Transaction pin must be a number";
    public const PASSWORD_MISMATCH = "Password mismatch. Please try again";
    public const INCORRECT_CURRENT_PASSWORD = "Current password is incorrect";
    public const INVALID_CREDENTIALS = "Bad combination of username and password";
    public const OTP_RESEND_ERROR = "Error sending OTP Code";
    public const OTP_SENT_EMAIL = "An OTP code has been sent to your email. Kindly check your email inbox or spam folder to complete your request";
    public const OTP_SENT_SMS = "An OTP code has been sent to your registered phone number.";
    public const LOGIN_SUCCESS = "Login successful. Preparing your dashboard. Kindly wait...";
    public const REGISTER_SUCCESS = "Your register is successful. You can now login to your account to enjoy exclusive discounts";
    public const REGISTER_SUCCESS_VERIFY = "Registration is successful. Kindly check your email inbox or spam folder to activate your account";
    public const PASSWORD_CHANGED_SUCCESS = "Password changed successfully. This changes will take effect from your next login";
    public const TRANS_PIN_CHANGED_SUCCESS = "Transaction pin changed successfully. This changes will take effect from your next transaction";
    public const INVALID_VERIFY_LINK = "Invalid verification link. Kindly click the link sent to your email address or safely copy the url to your browser";
    public const INVALID_TOKEN_LINK = "Invalid verification token or link. Kindly click the link sent to your email address or safely copy the url to your browser";
    public const VERIFY_SUCCESS = "Account verified successfully. You may now login to enjoy amazing offers";
    public const RESET_SUCCESS = "Reset password link sent to your email address. Kindly check your email inbox or spam folder to verify your account";

    public const REGISTER_PLAN_FAILED = "Registration plan does not exist. Please contact support";
    public const REGISTER_PLAN_NOT_EXIST = "Registration plan does not exist. Please contact support";
    public const REGISTER_FAILED = "Registration failed. Please try again";
    public const PASS_RESET_SENT = 'Reset password link sent to email provided';
    public const PASS_RESET_NOT_SENT = 'Reset password link not sent';
    public const PASS_RESET_ERROR = 'Password reset failed. Please try again';
    public const PASSWORD_SENT = "Password has been sent to your email";
    public const PASSWORD_NOT_MATCH = "Password does not match";
    public const TRANS_PIN_NOT_MATCH = "Transaction pin does not match";
    public const ACCEPT_TERMS = 'You must agree with the Terms & Conditions';
    public const REQUIRED_FIELDS = "Fill all required fields";

    // Status messages
    public const SETTINGS_UPDATED = "Settings updated successfully!";
    public const SAVED = "Saved Successfully";
    public const CREATED = "Created Successfully";
    public const UPDATED = "Your request has been updated successfully";
    public const DELETED = "Deleted Successfully";
    public const DATA_EMPTY = "No data found";
    public const DATA_SUCCESS = "Data retrieved successfully";

    // Wallet
    public const NAIRA_SIGN = "₦";
    public const PAYMENT_REQUEST_CREATED = "Wallet funding request created successfully";
    public const WALLET_APPROVED = "Wallet has been credited successfully";
    public const WALLET_DEBITED = "Wallet has been debited successfully";
    public const ERROR_CREDITING_WALLET = "Something went wrong crediting your wallet";

    // Transaction
    public const ERROR_CREATING_TRANS = "Transaction could not be created. Please contact Administrator";
    public const TRANSACT_SUCCESSFUL = "Transaction Successful";
    public const TRANSACT_FAILED = "Transaction failed";
    public const TRANSACT_UPDATE_ERROR = "Transaction could not be updated. Please notify Admin";
    public const TRANSACT_TREATED_ALREADY = "Transaction already treated. Kindly check your history for more details";
    public const TRANSACT_REFUND_WALLET = "Transaction failed, wallet refunded successfully";

    // Payment
    public const ACCOUNT_UPDATDED = "Account updated successfully.";
    public const ERROR_UPDATING_PAYMENT = "Payment record could not be updated. Please contact Administrator";
    public const ERROR_CREATING_PAYMENT = "Payment record could not be created. Please contact Administrator";
    public const PAYMENT_PROVIDER_NOT_FOUND = "Payment provider not found";
    public const NO_MISSING_AUTOPAYMENT_VENDOR = "User does not have any missing automated account number";
    public const PAYMENT_PROVIDER_DETAIL_NOT_FOUND = "Payment provider details not found";
    public const PAYMENT_RECORD_NOT_FOUND = "Payment record not found";
    public const PAYMENT_ADMIN_NOTIFIED = "Your payment request notification has been sent to the administrator. You will be credited shortly";
    public const PAYMENT_TREATED_ALREADY = "Payment already treated. Kindly check your history for more details";
    public const ACCOUNT_GENERATION_ERROR = "Account number not generated, Please try again.";
    public const ACCOUNT_GENERATED = "Account number generated successfully. You can now use it to fund your wallet.";
    public const PAYMENT_NOT_FOUND = "Payment not found";
    public const PAYMENT_FAILED = "Payment failed. If you've been debited in error, contact your bank for reversal";
    public const PAYMENT_COMM_ERROR = "Error communicating to provider. Please contact Administrator";
    public const PAYMENT_APPROVED_SUCCESS = "Your payment has been approved and your wallet has been credited successfully";
    public const PAYMENT_DECLINED_SUCCESS = "Payment declined successfully";

    // Error messages
    public const UNAUTHORIZED = "You are unathorized to perform this action. Please login and try again";
    public const REQUEST_FAILED = "Your request failed. Please try again later";
    public const ACCOUNT_DELETED_ALREADY = "Your account has been deleted already";
    public const ACCOUNT_DELETED_TEMPORARILY = "Your account has been temporarily deleted. Please contact support";
    public const UNEXPECTED_ERROR = 'Unexpected error occurred. This error will be reported. Please try again later';

    //Vendor, Product and Pricing messages...
    public const PRODUCT_NOT_FOUND = "Product not found";
    public const PRODUCT_EXISTS = "Product already exists";
    public const CATEGORY_REQUIRED = "Category is required";
    public const PRODUCT_DISABLED_VENDING = "Product is currently not available for purchase. Please try again later.";
    public const PRODUCTPRICING_ERROR = "Product pricing error. Please contact Administrator";
    public const PRODUCT_NO_VENDOR_CODE = "Product vendor information is not set. Please contact Administrator";
    public const PRODUCTPRICING_DELETE_FAILED = "Error deleting product pricing";
    public const PRODUCT_ADDING_FAILED = "Error adding product";
    public const PRODUCT_DELETE_FAILED = "Error deleting product";
    public const PRODUCTPRICING_UPDATE_FAILED = "Failed to update product pricing for plan";
    public const VENDOR_NAME_EXIST = "Vendor name already exists";
    public const VENDOR_CODE_EXIST = "Vendor code already exists";
    public const VENDOR_REQUIREMENT_REQUIRED = "Vendor requirement is required";
    public const VENDOR_NO_DATA = "Vendor data information not found";
    public const DUPLICATE_VENDOR_API = "API configuration already exists for this vendor. Please edit the existing configuration.";
    public const VENDOR_ADD_SUCCESS = "Vendor added successfully";
    public const API_NAME_EXIST = "API Name already exists";
    public const API_ADD_SUCCESS = "API Configuration added successfully";
    public const API_DELETE_FAILED = "Error deleting api";
    public const VENDOR_ADD_ERROR = "Error adding vendor";

    // Other messages
    public const LOGOUT_SUCCESS = "You have been logged out successfully";
    public const INVALID_OTP = "Invalid OTP code. Please try again";
    public const EMPTY_OTP = "Please provide an OTP code";
    public const OTP_USED = "OTP code has already been used. Please request a new one";
    public const OTP_EXPIRED = "OTP code has expired. Please request a new one";
    public const MFA_SETUP_ACTIVATED = "Multi-factor authentication setup successfully";
    public const MFA_SETUP_DEACTIVATED = "Multi-factor authentication deactivated successfully";
    public const INVALID_DATE_TIME = "Invalid Date-time selected";
    public const INVALID_DATE = "Invalid Date selected";
    public const EMPTY_BANK_ACCOUNT = "You are yet to set your banking information. Kindly set your account";

    // Wallet messages
    public const INSUFFICIENT_BALANCE = "Your wallet balance is insufficient. Please add funds to your wallet and try again";
    public const AMOUNT_LESS_THAN_ZERO = "Please provide a valid amount";


    // Optional: You could add a method to retrieve messages dynamically if necessary
    // public static function getMessage(string $key): string
    // {
    //     if (defined("self::$key")) {
    //         return constant("self::$key");
    //     }

    //     return "Message not found";
    // }

    // Function to get the message with a dynamic time
    public static function getBlockedMessage($time)
    {
        // Use sprintf to replace the placeholder with the dynamic time
        return sprintf(self::ACCOUNT_BLOCKED, $time);
    }
}