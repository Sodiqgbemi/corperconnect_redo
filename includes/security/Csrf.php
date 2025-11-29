<?php

declare(strict_types=1);

namespace Includes\Security;

/**
 * CSRF Token Management Class
 * 
 * Provides secure CSRF token generation and validation with automatic expiration.
 * 
 * @package Includes\Security
 */
class CSRF
{
    /**
     * Token time-to-live in seconds (30 minutes)
     */
    private const CSRF_TOKEN_TTL = 1800;
    
    /**
     * Token byte length
     */
    private const TOKEN_LENGTH = 32;
    
    /**
     * Session key for CSRF token
     */
    private const SESSION_TOKEN_KEY = 'csrf_token';
    
    /**
     * Session key for token expiration time
     */
    private const SESSION_EXPIRATION_KEY = 'csrf_token_expiration';

    /**
     * Generate and store a CSRF token with expiration time
     * 
     * Regenerates token if:
     * - No token exists in session
     * - Existing token has expired
     * 
     * @return string The CSRF token
     * @throws \RuntimeException If session is not started
     */
    public static function generateCsrfToken(): string
    {
        self::ensureSessionStarted();
        
        if (self::shouldRegenerateToken()) {
            self::regenerateToken();
        }
        
        return $_SESSION[self::SESSION_TOKEN_KEY];
    }

    /**
     * Validate a CSRF token against the stored session token
     * 
     * @param string|null $token The token to validate
     * @return bool True if valid and not expired, false otherwise
     */
    public static function validateCsrfToken(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }
        
        // Clear expired tokens
        if (self::tokenHasExpired()) {
            self::clearToken();
            return false;
        }
        
        // Ensure token exists in session and is a string
        if (!isset($_SESSION[self::SESSION_TOKEN_KEY]) 
            || !is_string($_SESSION[self::SESSION_TOKEN_KEY])
        ) {
            return false;
        }
        
        // Use timing-safe comparison
        return hash_equals($_SESSION[self::SESSION_TOKEN_KEY], $token);
    }

    /**
     * Generate HTML input field with CSRF token
     * 
     * @return string HTML hidden input field
     */
    public static function csrfField(): string
    {
        self::generateCsrfToken();
        
        return sprintf(
            '<input type="hidden" name="csrf_token" value="%s">',
            htmlspecialchars($_SESSION[self::SESSION_TOKEN_KEY], ENT_QUOTES, 'UTF-8')
        );
    }
    
    /**
     * Get CSRF token meta tag for JavaScript access
     * 
     * @return string HTML meta tag
     */
    public static function csrfMetaTag(): string
    {
        self::generateCsrfToken();
        
        return sprintf(
            '<meta name="csrf-token" content="%s">',
            htmlspecialchars($_SESSION[self::SESSION_TOKEN_KEY], ENT_QUOTES, 'UTF-8')
        );
    }
    
    /**
     * Get the current CSRF token without regenerating
     * 
     * @return string|null The current token or null if none exists
     */
    public static function getToken(): ?string
    {
        if (!isset($_SESSION[self::SESSION_TOKEN_KEY])) {
            return null;
        }
        
        return $_SESSION[self::SESSION_TOKEN_KEY];
    }
    
    /**
     * Manually clear/invalidate the current CSRF token
     * 
     * @return void
     */
    public static function clearToken(): void
    {
        unset($_SESSION[self::SESSION_TOKEN_KEY], $_SESSION[self::SESSION_EXPIRATION_KEY]);
    }

    /**
     * Check if the current token has expired
     * 
     * @return bool True if expired or expiration not set, false otherwise
     */
    private static function tokenHasExpired(): bool
    {
        if (!isset($_SESSION[self::SESSION_EXPIRATION_KEY])) {
            return true;
        }
        
        return time() > $_SESSION[self::SESSION_EXPIRATION_KEY];
    }
    
    /**
     * Determine if token should be regenerated
     * 
     * @return bool
     */
    private static function shouldRegenerateToken(): bool
    {
        return empty($_SESSION[self::SESSION_TOKEN_KEY]) || self::tokenHasExpired();
    }
    
    /**
     * Regenerate the CSRF token and set new expiration
     * 
     * @return void
     * @throws \Exception If random_bytes fails
     */
    private static function regenerateToken(): void
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $_SESSION[self::SESSION_TOKEN_KEY] = $token;
        $_SESSION[self::SESSION_EXPIRATION_KEY] = time() + self::CSRF_TOKEN_TTL;
    }
    
    /**
     * Ensure session is started
     * 
     * @return void
     * @throws \RuntimeException If session cannot be started
     */
    private static function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            throw new \RuntimeException(
                'Session must be started before using CSRF protection. Call session_start() first.'
            );
        }
    }
}